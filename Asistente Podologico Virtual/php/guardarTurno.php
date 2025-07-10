<?php

session_start(); 

try {

    // --- conexión a la base de datos ---
    include 'conexion_be.php';

    // Obtiene datos del POST
    $start = $_POST['start'];
    $end = $_POST['end'];
    $title = $_POST['title'];
    $id_paciente = $_SESSION['id_paciente'] ?? null;
    $id_podologo = 4;  // por ahora por defecto

   // Verifica que todos los datos existan
    if (!$id_paciente || !$start || !$end || !$title) {
        http_response_code(400);
        echo "Faltan datos obligatorios.";
        exit;
    }

    // Prepara la consulta
    $sql = "INSERT INTO turno (start, end, title, id_paciente, id_podologo)
            VALUES (:start, :end, :title, :id_paciente, :id_podologo)";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':start', $start);
    $stmt->bindParam(':end', $end);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':id_paciente', $id_paciente);
    $stmt->bindParam(':id_podologo', $id_podologo);
    
    // Ejecutar
    if ($stmt->execute()) {
        echo "ok";
        
    } else {
        http_response_code(500);
        echo "Error al guardar";
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo "Error de base de datos: " . $e->getMessage();
}
?>