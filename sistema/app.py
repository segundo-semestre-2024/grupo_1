import numpy as np
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
import joblib
from flask import Flask, request, render_template, jsonify, send_file
import firebase_admin
from firebase_admin import credentials
from firebase_admin import db
import os
from fpdf import FPDF



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
    prediction = None #inicializar las variables a usar
    comment = None
    if request.method == 'POST':
        comment = request.form['comment']
        # Preprocesamiento del comentario
        x = vectorizer.transform([comment])

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

        return render_template('index.html', prediction=prediction, comment=comment)  # Correcto: return dentro del if

    return render_template('index.html', prediction=prediction, comment=comment)  # Correcto: return fuera del if (para GET)


@app.route('/export/pdf', methods=['POST'])
def export_pdf():
    try:
        data = request.get_json()
        comment = data.get("comment", "").strip()
        prediction = data.get("prediction", "").strip()

        if not comment or not prediction:
            return jsonify({'error': 'No se recibieron datos válidos'}), 400

        pdf_path = "resultado.pdf"

        # Crear el PDF con estilo
        pdf = FPDF()
        pdf.add_page()
        pdf.set_font("Arial", "B", 16)
        pdf.cell(200, 10, txt="Reporte de Análisis de Comentario", ln=True, align='C')
        pdf.ln(10)
        
        pdf.set_font("Arial", size=12)
        pdf.multi_cell(0, 10, f"Comentario: {comment}")
        pdf.ln(5)
        pdf.set_text_color(255, 0, 0)  # Texto en rojo
        pdf.multi_cell(0, 10, f"Predicción: {prediction}")
        
        pdf.output(pdf_path)
        return send_file(pdf_path, as_attachment=True)

    except Exception as e:
        return jsonify({"error": f"Error interno: {str(e)}"}), 500

@app.route('/export/excel', methods=['GET'])
def export_excel():
    try:
        # Datos de ejemplo, reemplázalos con los valores reales
        comment = request.args.get("comment", "").strip()
        prediction = request.args.get("prediction", "").strip()

        if not comment or not prediction:
            return jsonify({'error': 'No se recibieron datos válidos'}), 400

        # Crear un DataFrame con los datos
        df = pd.DataFrame({"Comentario": [comment], "Predicción": [prediction]})

        # Guardar en un archivo Excel
        excel_path = "resultado.xlsx"
        df.to_excel(excel_path, index=False, engine='openpyxl')

        return send_file(excel_path, as_attachment=True)

    except Exception as e:
        return jsonify({"error": f"Error interno: {str(e)}"}), 500
    

if __name__ == '__main__':
    app.run(debug=True)