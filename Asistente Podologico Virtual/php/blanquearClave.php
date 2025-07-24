<?php
session_start();
include 'conexion_be.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_paciente'])) {
    $idPaciente = $_POST['id_paciente'];

    // Encriptar la clave con sha512
    $clave_blanqueada = hash('sha512', 'BLANQUEADA');

    // Actualizar la contraseña encriptada
    $stmt = $conexion->prepare("UPDATE paciente SET contrasena = :clave WHERE idPaciente = :id");
    $stmt->bindParam(':clave', $clave_blanqueada);
    $stmt->bindParam(':id', $idPaciente, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo '
            <script src="../js/alerta.js"></script>;
            <script>alerta("Clave blanqueada exitosamente.","pantalla_paciente.php?idPaciente=$idPaciente");
                    
            </script>';
        exit;
        $_SESSION['mensaje'] = "Clave blanqueada exitosamente.";
    } else {
        $_SESSION['mensaje'] = "Error al blanquear la clave.";
    }
}

header("Location: pantalla_administrador.php");
exit;
?>