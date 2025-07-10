<?php
include 'conexion_be.php';

$stmt = $conexion->query("
    SELECT t.idTurno AS id, t.title, t.start, t.end, 
           p.nombres, p.apellido
    FROM turno t
    JOIN paciente p ON t.id_paciente = p.idPaciente
    WHERE t.idTurno NOT IN (
        SELECT id_turno FROM historiaclinica WHERE id_turno IS NOT NULL
    )
");

$eventos = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $eventos[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start'],
        'end' => $row['end'],
 //       'color' => '#3a87ad',
        'nombrePaciente' => $row['nombres'] . ' ' . $row['apellido']
    ];
}

header('Content-Type: application/json');
echo json_encode($eventos);
?>
