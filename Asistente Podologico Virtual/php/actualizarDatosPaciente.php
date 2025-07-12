<?php

session_start();
include 'conexion_be.php';

$idPaciente = $_SESSION['id_paciente'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apellido = $_POST['apellido'];
    $nombres = $_POST['nombres'];
    $email = $_POST['email'];
    $celular = $_POST['celular'];

    $stmt = $conexion->prepare("UPDATE paciente SET apellido = :apellido, nombres = :nombres, email = :email, celular = :celular WHERE idPaciente = :id");
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':celular', $celular);
    $stmt->bindParam(':id', $idPaciente);

    if ($stmt->execute()) {
        echo '<script src="../js/alerta.js"></script>;
          <script>alerta("Datos actualizados con exito !!!", "pantalla_paciente.php");</script>';
    } else {
        echo '<script src="../js/alerta.js"></script>;
          <script>alerta("Error al actualizar.", "pantalla_paciente.php");</script>';
    }    
}
?>