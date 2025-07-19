<!-- ---------------- Archivo buscarPaciente.php --------------------->

<?php
// Incluir la conexión a la base de datos
include 'conexion_be.php';

// Obtiene el parámetro de búsqueda desde la URL
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Inicializa la respuesta como un array vacío
$response = [];

if (!empty($query)) {
    try {
        // Prepara la consulta para buscar coincidencias en nombres o apellidos
        $stmt = $conexion->prepare("
            SELECT idPaciente, apellido, nombres, email, dni 
            FROM paciente 
            WHERE apellido LIKE :query OR nombres LIKE :query
        ");

        // Ejecuta la consulta con el parámetro de búsqueda
        $stmt->execute([':query' => "%$query%"]);

        // Obtiene los resultados como un array asociativo
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Maneja los errores de la base de datos (opcional)
        error_log("Error en la consulta: " . $e->getMessage());
        http_response_code(500); // Código de error interno del servidor
    }
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
