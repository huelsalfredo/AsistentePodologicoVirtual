<?php

session_start();

include 'conexion_be.php';

if (isset($_GET['idPaciente'])) {
    $_SESSION['id_paciente'] = $_GET['idPaciente'];

    // Busca datos del paciente
    $stmt = $conexion->prepare("SELECT nombres, apellido FROM paciente WHERE idPaciente = :idPaciente");
    $stmt->bindParam(':idPaciente', $_GET['idPaciente']);
    $stmt->execute();
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paciente) {
        $_SESSION['nombre_paciente'] = $paciente['nombres'] . ' ' . $paciente['apellido'];
        $_SESSION['modo_admin'] = true; // Marca que es el administrador usando la vista de paciente
    }
}

if (!isset($_SESSION['id_paciente'])) {
    echo '<script src="../js/alerta.js"></script>;
          <script>alerta("Debes iniciar sesión", "index.php");</script>';
    exit;
}

$id_paciente = $_SESSION['id_paciente'];

// include 'nav_superior_paciente.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla Paciente</title>
    <link rel="stylesheet" href="../Css/estilos.css">
    <!-- bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>

<body>

<?php include 'nav_superior_paciente.php'; ?>

<!-- Popper.js y Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
