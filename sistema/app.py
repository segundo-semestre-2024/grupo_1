import numpy as np
import pandas as pd
import requests  # Asegurar que esté importado
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
import joblib
from flask import Flask, request, jsonify
import firebase_admin
from firebase_admin import credentials, db
import os
from dotenv import load_dotenv
from functools import wraps  # Para el decorador de la API Key

# 🔹 Cargar las variables de entorno
load_dotenv()

API_KEY = os.getenv("API_KEY", "default_key")
NOTIFICACIONES_URL = os.getenv("NOTIFICACIONES_URL")

print(f"API_KEY cargada: {API_KEY}")
print(f"Microservicio de Notificaciones: {NOTIFICACIONES_URL}")

app = Flask(__name__)

# Cargar el modelo y el vectorizador
model = joblib.load('modelo1.pkl')
vectorizer = joblib.load('vectorizer.pkl')

# Inicializar Firebase
cred = credentials.Certificate("proyecto-13e39-firebase-adminsdk-fbsvc-96e049fd8e.json")
firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://proyecto-13e39-default-rtdb.firebaseio.com/'
})

# Middleware para validar API Key
def require_api_key(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        api_key = request.headers.get("X-API-KEY")
        if api_key != API_KEY:
            return jsonify({"message": "Acceso denegado. Clave API incorrecta."}), 403
        return func(*args, **kwargs)
    return wrapper

# Función para guardar datos en Firebase
def guardar_datos(ruta, datos):
    ref = db.reference(ruta)
    ref.push(datos)
    print("✅ Datos guardados con éxito en Firebase")

@app.route('/prediction', methods=['POST'])
@require_api_key
def predict():
    data = request.get_json()
    if not data or 'comment' not in data:
        return jsonify({'error': 'Debe proporcionar un comentario'}), 400
    
    comment = data['comment']
    x = vectorizer.transform([comment])
    P = model.predict(x)
    clases = model.classes_
    
    prediction = "negativo" if clases[P[0]] == 0 else "positivo" if clases[P[0]] == 1 else "neutro"

    # Guardar en Firebase
    datos_firebase = {'comentario': comment, 'prediccion': prediction}
    guardar_datos('/comentarios', datos_firebase)

    # Guardar en CSV
    df_comentarios = pd.DataFrame({'Comentario': [comment], 'Prediccion': [prediction]})
    df_comentarios.to_csv('comentarios.csv', mode='a', header=not os.path.exists('comentarios.csv'), index=False)

    response_data = {'prediction': prediction}  # Inicializar el diccionario de respuesta

    # Enviar notificación si el comentario es negativo
    if prediction == "negativo":
        print("⚠️ Comentario negativo detectado, enviando notificación...")

        notification_payload = {
            "destino": "+573013179250",  # Número de destino
            "mensaje": f"Se ha detectado un comentario negativo: '{comment}'"
        }
        headers = {"X-API-KEY": API_KEY, "Content-Type": "application/json"}

        try:
            notify_response = requests.post(NOTIFICACIONES_URL, json=notification_payload, headers=headers)
            if notify_response.status_code == 200:
                print("✅ Notificación enviada correctamente.")
                response_data["notification_status"] = "Enviada"
            else:
                print(f"❌ Error enviando notificación: {notify_response.text}")
                response_data["notification_status"] = "Error"
        except requests.exceptions.RequestException as e:
            print(f"❌ Error en la solicitud de notificación: {e}")
            response_data["notification_status"] = "Error de conexión"

    return jsonify(response_data)

if __name__ == '__main__':
    app.run(debug=True)
