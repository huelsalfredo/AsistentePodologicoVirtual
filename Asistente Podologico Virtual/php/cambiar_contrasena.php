<!-- ---------- Archivo cambiar_contrasena.php ----------------->

<?php
// Inicio sesión si no está iniciada para acceder a variables de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluyo la conexión a la base de datos
include 'conexion_be.php'; 

// Obtengo el id del paciente y la nueva clave desde el formulario (POST)
$idPaciente = $_POST['idPaciente'] ?? null;
$nuevaClave1 = $_POST['nuevaClave1'] ?? null;

// Verifico que recibí los datos necesarios, si falta algo respondo error y corto ejecución
if (!$idPaciente || !$nuevaClave1) {
    echo 'ERROR';
    exit;
}

// Hasheo la nueva contraseña con SHA-512 para guardarla de forma segura
$nuevaHash = hash('sha512', $nuevaClave1);

// Preparo la consulta SQL para actualizar la contraseña en la tabla paciente
$stmt = $conexion->prepare("UPDATE paciente SET contrasena = :contrasena WHERE idPaciente = :id");
$stmt->bindParam(':contrasena', $nuevaHash);             // Asigno el hash al parámetro contrasena
$stmt->bindParam(':id', $idPaciente, PDO::PARAM_INT);    // Asigno el idPaciente al parámetro id

// Ejecuto la consulta y dependiendo del resultado envío OK o ERROR
if ($stmt->execute()) {
    echo 'OK';    // Todo salió bien
} else {
    echo 'ERROR'; // Algo falló al ejecutar la consulta
}
?>
