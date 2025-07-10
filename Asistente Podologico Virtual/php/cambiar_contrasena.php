<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conexion_be.php'; 

$idPaciente = $_POST['idPaciente'] ?? null;
$nuevaClave1 = $_POST['nuevaClave1'] ?? null;

if (!$idPaciente || !$nuevaClave1) {
    echo 'ERROR';
    exit;
}

// Hasheamos la nueva clave
$nuevaHash = hash('sha512', $nuevaClave1);

// Ejecutamos el UPDATE
$stmt = $conexion->prepare("UPDATE paciente SET contrasena = :contrasena WHERE idPaciente = :id");
$stmt->bindParam(':contrasena', $nuevaHash);
$stmt->bindParam(':id', $idPaciente, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo 'OK';
} else {
    echo 'ERROR';
}
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conexion_be.php'; 

$idPaciente = $_POST['idPaciente'] ?? null;
$nuevaClave1 = $_POST['nuevaClave1'] ?? null;

if (!$idPaciente || !$nuevaClave1) {
    echo 'ERROR';
    exit;
}

// Hasheamos la nueva clave
$nuevaHash = hash('sha512', $nuevaClave1);

// Ejecutamos el UPDATE
$stmt = $conexion->prepare("UPDATE paciente SET contrasena = :contrasena WHERE idPaciente = :id");
$stmt->bindParam(':contrasena', $nuevaHash);
$stmt->bindParam(':id', $idPaciente, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo 'OK';
} else {
    echo 'ERROR';
}
?>
