import numpy as np
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
import joblib
from flask import Flask, request, render_template, jsonify
import firebase_admin
from firebase_admin import credentials
from firebase_admin import db
import os

app = Flask(__name__)

# Cargar el modelo y el vectorizador
model = joblib.load('modelo1.pkl')
vectorizer = joblib.load('vectorizer.pkl')

# Inicializar Firebase
cred = credentials.Certificate("proyecto-13e39-firebase-adminsdk-fbsvc-96e049fd8e.json")  # Reemplaza con la ruta correcta
firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://proyecto-13e39-default-rtdb.firebaseio.com/'  # Reemplaza con tu URL
})

def guardar_datos(ruta, datos):
    ref = db.reference(ruta)
    ref.push(datos)  # Crea una nueva entrada con un ID generado automáticamente
    print("Datos guardados con éxito en Firebase")

@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        comment = request.form.get('comment', None)  # Using .get() avoids the KeyError
        if comment:
            # Preprocesamiento del comentario
            x = vectorizer.transform([comment])
        else:
            # Handle missing comment
            return 'No comment provided', 400

        # Predicción
        P = model.predict(x)
        clases = model.classes_
        if clases[P[0]] == 0:
            prediction = "negativo"
        elif clases[P[0]] == 1:
            prediction = "positivo"
        else:
            prediction = "neutro"

        # Guardar datos en Firebase
        datos_firebase = {
            'comentario': comment,
            'prediccion': prediction
        }
        guardar_datos('/comentarios', datos_firebase)

        # Guardar comentario y predicción en CSV (opcional)
        df_comentarios = pd.DataFrame({'Comentario': [comment], 'Prediccion': [prediction]})
        df_comentarios.to_csv('comentarios.csv', mode='a', header=not os.path.exists('comentarios.csv'), index=False)

        return render_template('index.html', prediction=prediction, comment=comment)  # Correcto: return dentro del if

    return render_template('index.html', prediction=prediction, comment=comment)  # Correcto: return fuera del if (para GET)


if __name__ == '__main__':
    app.run(debug=True)