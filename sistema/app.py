import numpy as np
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
import joblib
from flask import Flask, request, jsonify
import firebase_admin
from firebase_admin import credentials, db
import os

app = Flask(__name__)

# Cargar el modelo y el vectorizador
model = joblib.load('modelo1.pkl')
vectorizer = joblib.load('vectorizer.pkl')

# Inicializar Firebase
cred = credentials.Certificate("proyecto-13e39-firebase-adminsdk-fbsvc-96e049fd8e.json")
firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://proyecto-13e39-default-rtdb.firebaseio.com/'
})

def guardar_datos(ruta, datos):
    ref = db.reference(ruta)
    ref.push(datos)
    print("Datos guardados con éxito en Firebase")

@app.route('/prediction', methods=['POST'])
def predict():
    data = request.get_json()
    if not data or 'comment' not in data:
        return jsonify({'error': 'Debe proporcionar un comentario'}), 400
    
    comment = data['comment']
    x = vectorizer.transform([comment])
    P = model.predict(x)
    clases = model.classes_
    
    prediction = "negativo" if clases[P[0]] == 0 else "positivo" if clases[P[0]] == 1 else "neutro"
    
    datos_firebase = {'comentario': comment, 'prediccion': prediction}
    guardar_datos('/comentarios', datos_firebase)
    
    df_comentarios = pd.DataFrame({'Comentario': [comment], 'Prediccion': [prediction]})
    df_comentarios.to_csv('comentarios.csv', mode='a', header=not os.path.exists('comentarios.csv'), index=False)
    
    return jsonify({'prediction': prediction})

if __name__ == '__main__':
    app.run(debug=True)