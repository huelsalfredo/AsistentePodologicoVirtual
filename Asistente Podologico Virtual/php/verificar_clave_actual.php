<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conexion_be.php';
    // try {
    //     // Configuración de la conexión
    //     $host = "localhost";
    //     $dbname = "asistentepodologico";
    //     $username = "root";
    //     $password = "";

    //     // Crea la conexión con PDO
    //     $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    //     // Configura atributos para el manejo de errores
    //     $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //     // Mensaje de éxito al conectar
    //     // echo "Conectado exitosamente a la base de datos";

    // } catch (PDOException $e) {
    //     // Maneja errores
    //     echo 'Error al conectar con la base de datos: ' . $e->getMessage();
    // }

$idPaciente = $_POST['idPaciente'] ?? null;
$claveActual = $_POST['claveActual'] ?? null;

if (!$idPaciente || !$claveActual) {
    echo 'ERROR';
    exit;
}

// Encripta la clave actual recibida
$claveHash = hash('sha512', $claveActual);

// Busca en la base la fila con ese id y la clave encriptada
$stmt = $conexion->prepare("SELECT * FROM paciente WHERE idPaciente = :id AND contrasena = :contrasena");
$stmt->bindParam(':id', $idPaciente, PDO::PARAM_INT);
$stmt->bindParam(':contrasena', $claveHash);
$stmt->execute();

// Si hay coincidencia, está todo bien
if ($stmt->fetch()) {
    echo 'OK';
} else {
    echo 'ERROR';
}
?>
