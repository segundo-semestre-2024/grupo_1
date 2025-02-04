import firebase_admin
from firebase_admin import credentials
from firebase_admin import db

# Inicializar Firebase Admin SDK con las credenciales
cred = credentials.Certificate("proyecto-13e39-firebase-adminsdk-fbsvc-96e049fd8e.json")  # Ruta al archivo de clave de servicio
firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://proyecto-13e39-default-rtdb.firebaseio.com/'  # URL de tu base de datos en tiempo real de Firebase
})

# Función para guardar datos (Push)
def guardar_datos(ruta, datos):
    ref = db.reference(ruta)
    ref.push(datos)  # Crea una nueva entrada con un ID generado automáticamente
    print("Datos guardados con éxito en Firebase")

# Función para actualizar datos (Update)
def actualizar_datos(ruta, clave, nuevos_datos):
    ref = db.reference(f'{ruta}/{clave}')
    ref.update(nuevos_datos)  # Actualiza los datos en la referencia dada
    print("Datos actualizados con éxito en Firebase")

def borrar_datos(ruta, clave):
    ref = db.reference(f'{ruta}/{clave}')
    if ref.get() is not None:  # Verifica si el nodo existe antes de intentar borrarlo
        ref.delete()  # Elimina el nodo con la clave específica
        print(f"Datos con clave {clave} borrados con éxito de Firebase")
    else:
        print(f"No se encontró el nodo con la clave {clave} en {ruta}")

# Función para leer datos (Get)
def leer_datos(ruta):
    ref = db.reference(ruta)
    datos = ref.get()  # Obtiene todos los datos de la ruta
    if datos:
        print("Datos leídos de Firebase:", datos)
        return datos
    else:
        print("No hay datos disponibles en Firebase en la ruta especificada.")
        return None

# Ejemplo de uso de las funciones

# Guardar datos
#datos = {'abc': 4667, 'efg': 4667, 'hij': 4667, 'klm':{'opq':5677}}
#guardar_datos('/resumen', datos)



# # Actualizar datos (suponiendo que conoces la clave del nodo que deseas actualizar)
# clave = 'total_sales'  # Reemplaza con la clave real
# nuevos_datos = {'total_sales': 5000}
# actualizar_datos('/resumen', clave, nuevos_datos)

# Borrar datos (suponiendo que conoces la clave del nodo que deseas borrar)
# Borrar datos con la clave especificada
#clave = '-O638pMis3bhJWvJ1qWl'  # Clave del nodo a borrar
#borrar_datos('/resumen', clave)


# Leer datos
#leer_datos('/resumen') 