<!-- ---------- Archivo turnosTomados.php ----------------->

<?php
// Incluyo la conexión a la base de datos para poder ejecutar consultas
include 'conexion_be.php';

// Ejecuto una consulta para obtener los turnos que NO tienen historia clínica asociada
$stmt = $conexion->query("
    SELECT t.idTurno AS id, t.title, t.start, t.end, 
           p.nombres, p.apellido, p.idPaciente
    FROM turno t 
    JOIN paciente p ON t.id_paciente = p.idPaciente
    WHERE t.idTurno NOT IN (
        SELECT id_turno FROM historiaclinica WHERE id_turno IS NOT NULL
    )
");

// Creo un array vacío donde voy a guardar los eventos que voy a devolver
$eventos = [];

// Recorro cada fila que me devolvió la consulta para armar el arreglo con datos necesarios
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $eventos[] = [
        'id' => $row['id'],                             // Id del turno
        'title' => $row['title'],                       // Título del turno
        'start' => $row['start'],                       // Fecha y hora inicio
        'end' => $row['end'],                           // Fecha y hora fin
        //'color' => '#3a87ad',                      
        'nombrePaciente' => $row['nombres'] . ' ' . $row['apellido'], // Nombre completo paciente
        'idPaciente' => $row['idPaciente'],             // Id del paciente
    ];
}

// Indico que la respuesta será en formato JSON para que el cliente la pueda interpretar
header('Content-Type: application/json');

// Convierto el array de eventos a JSON y lo envío como respuesta
echo json_encode($eventos);
?>
