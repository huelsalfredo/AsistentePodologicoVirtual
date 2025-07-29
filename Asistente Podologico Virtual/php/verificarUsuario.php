<?php
include 'conexion_be.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];

    if ($tipo === 'dni') {
        $query = $conexion->prepare("SELECT 1 FROM paciente WHERE dni = :valor");
    } elseif ($tipo === 'email') {
        $query = $conexion->prepare("SELECT 1 FROM paciente WHERE email = :valor");
    } else {
        echo 'error';
        exit;
    }

    $query->bindParam(':valor', $valor);
    $query->execute();

    echo $query->rowCount() > 0 ? 'existe' : 'ok';
}
