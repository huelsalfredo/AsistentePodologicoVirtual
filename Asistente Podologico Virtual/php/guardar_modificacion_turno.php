<?php
include 'conexion_be.php';
session_start();

$idTurno = $_POST['idTurno'] ?? null;
$start = $_POST['nuevo_start'] ?? null;
$end = $_POST['nuevo_end'] ?? null;
$title = $_POST['nuevo_title'] ?? null;
$id_paciente = $_SESSION['id_paciente'] ?? null;
$id_podologo = 4; // Valor por defecto por ahora

if (!$idTurno || !$start || !$end || !$id_paciente) {
    http_response_code(400);
    echo "Datos incompletos.";
    exit;
}

// Verifica que no haya colisión con otro turno
$sql = "SELECT COUNT(*) FROM turno WHERE start = :start AND idTurno != :idTurno";
$stmt = $conexion->prepare($sql);
$stmt->execute([':start' => $start, ':idTurno' => $idTurno]);
$existe = $stmt->fetchColumn();

if ($existe > 0) {
    echo "Ese horario ya está reservado.";
    exit;
}

// Actualiza el turno
$update = $conexion->prepare("UPDATE turno 
    SET start = :start, end = :end, title = :title, id_podologo = :id_podologo 
    WHERE idTurno = :idTurno AND id_paciente = :id_paciente");
$update->execute([
    ':start' => $start,
    ':end' => $end,
    ':title' => $title,
    ':idTurno' => $idTurno,
    ':id_paciente' => $id_paciente,
    ':id_podologo' => $id_podologo
]);

header("Location: notificar_cambios.php?idTurno=$idTurno");
echo "Turno modificado correctamente.";

exit;

