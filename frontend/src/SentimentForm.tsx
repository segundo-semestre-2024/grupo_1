import React, { useState } from "react";

const SentimentForm: React.FC = () => {
  const [text, setText] = useState("");  // Estado para el comentario
  const [result, setResult] = useState<string | null>(null);  // Estado para la respuesta
  const [loading, setLoading] = useState(false);  // Estado de carga

  const analyzeSentiment = async () => {
    setLoading(true);
    setResult(null);

    try {
      const response = await fetch("http://127.0.0.1:8000/api/prediction", { 
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${localStorage.getItem("token")}`  // Si usas autenticación con Sanctum
        },
        body: JSON.stringify({ comment: text }),
      });

      if (!response.ok) {
        throw new Error("Error en la respuesta del servidor");
      }

      const data = await response.json();
      setResult(data.prediction);  // Guardar la predicción en el estado

    } catch (error) {
      console.error("Error al analizar el sentimiento:", error);
      setResult("Error en el análisis");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-md mx-auto mt-10 p-5 bg-white shadow-md rounded-lg">
      <h2 className="text-xl font-bold mb-4">Análisis de Sentimiento</h2>

      <textarea
        className="w-full p-2 border border-gray-300 rounded-md"
        placeholder="Escribe tu comentario..."
        value={text}
        onChange={(e) => setText(e.target.value)}
      ></textarea>

      <button
        className="w-full bg-blue-500 text-white py-2 px-4 mt-3 rounded-md hover:bg-blue-600"
        onClick={analyzeSentiment}
        disabled={loading}
      >
        {loading ? "Analizando..." : "Analizar Sentimiento"}
      </button>

      {result && (
        <div className="mt-4 p-3 border rounded-md bg-gray-100">
          <strong>Resultado:</strong> {result}
        </div>
      )}
    </div>
  );
};

export default SentimentForm;
