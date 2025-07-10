<?php

include 'conexion_be.php';

session_start();

$id_turno     = $_POST['idTurno'] ?? null;
$id_paciente  = $_POST['id_paciente'] ?? null;
$id_podologo  = $_POST['id_podologo'] ?? null;
$notas        = $_POST['notas'] ?? '';
$tratamiento  = $_POST['tratamiento'] ?? '';

if ($id_turno && $id_paciente && $notas && $tratamiento) {
    $stmt = $conexion->prepare("INSERT INTO HistoriaClinica 
        (notas, tratamiento, id_paciente, id_podologo, id_turno, fecha)
        VALUES 
        (:notas, :tratamiento, :id_paciente, :id_podologo, :id_turno, NOW())");

    $stmt->execute([
        ':notas' => $notas,
        ':tratamiento' => $tratamiento,
        ':id_paciente' => $id_paciente,
        ':id_podologo' => $id_podologo,
        ':id_turno' => $id_turno
    ]);

    header("Location: " . $_SERVER['HTTP_REFERER']);
    alerta ("Historia Clinica guardada correctamente...")
    exit;
} else {
    echo "Faltan datos para guardar la historia clínica.";
}

?><?php

include 'conexion_be.php';

session_start();

$id_turno     = $_POST['idTurno'] ?? null;
$id_paciente  = $_POST['id_paciente'] ?? null;
$id_podologo  = $_POST['id_podologo'] ?? null;
$notas        = $_POST['notas'] ?? '';
$tratamiento  = $_POST['tratamiento'] ?? '';

if ($id_turno && $id_paciente && $notas && $tratamiento) {
    $stmt = $conexion->prepare("INSERT INTO HistoriaClinica 
        (notas, tratamiento, id_paciente, id_podologo, id_turno, fecha)
        VALUES 
        (:notas, :tratamiento, :id_paciente, :id_podologo, :id_turno, NOW())");

    $stmt->execute([
        ':notas' => $notas,
        ':tratamiento' => $tratamiento,
        ':id_paciente' => $id_paciente,
        ':id_podologo' => $id_podologo,
        ':id_turno' => $id_turno
    ]);

    header("Location: " . $_SERVER['HTTP_REFERER']);
    alerta ("Historia Clinica guardada correctamente...")
    exit;
} else {
    echo "Faltan datos para guardar la historia clínica.";
}

?>