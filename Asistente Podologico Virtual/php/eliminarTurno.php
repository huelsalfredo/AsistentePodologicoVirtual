
<?php
session_start();
include 'conexion_be.php';

if (!isset($_SESSION['id_paciente'])) {
    echo'
        <script src="../js/alerta.js"></script>
        <script>
            window.onload = function() {
                alerta("Debes iniciar sesión", "index.php");
            };
        </script>
    ';
    exit;
}

$id_paciente = $_SESSION['id_paciente'];
$idTurno = $_GET['idTurno'] ?? null;

if (!$idTurno) {
    echo "<p>Error: ID del turno no especificado.</p>";
    exit;
}

try {
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtiene la fecha y hora del turno
    $sql = "SELECT start FROM turno WHERE idTurno = :idTurno AND id_paciente = :id_paciente";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idTurno', $idTurno);
    $stmt->bindParam(':id_paciente', $id_paciente);
    $stmt->execute();
    $turno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$turno) {
        echo "<p>No se encontró el turno o no te pertenece.</p>";
        exit;
    }

    $fechaTurno = strtotime($turno['start']);
    $ahora = time();

    if (($fechaTurno - $ahora) < 86400) { // 86400 segundos = 24 horas
        echo '
            <script src="../js/alerta.js"></script>
            <script>
                window.onload = function {
                    alerta("No se puede eliminar el turno con menos de 24 horas de anticipación", "pantalla_paciente.php");
                };
            </script>';
        exit;
    }

    // Elimina el turno
    $sqlDelete = "DELETE FROM turno WHERE idTurno = :idTurno AND id_paciente = :id_paciente";
    $stmtDelete = $conexion->prepare($sqlDelete);
    $stmtDelete->bindParam(':idTurno', $idTurno);
    $stmtDelete->bindParam(':id_paciente', $id_paciente);
    $stmtDelete->execute();

    echo '
        <script src="../js/alerta.js"></script>
        <script>
            window.onload = function() {
                alerta("Turno eliminado correctamente", "pantalla_paciente.php");
            };
        </script>
    ';

} catch (PDOException $e) {
    echo "<p>Error al eliminar el turno: " . $e->getMessage() . "</p>";
}
?>
