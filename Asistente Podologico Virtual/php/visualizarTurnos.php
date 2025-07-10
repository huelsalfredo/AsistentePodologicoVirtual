<?php
include 'conexion_be.php';

session_start();

// Si no está logueado, redirige
if (!isset($_SESSION['paciente'])) {
    echo '<script src="../js/alerta.js"></script>;
          <script>alerta("Por favor inicia sesión", "index.php");</script>';
    exit;
}

$idPaciente = $_GET['id_paciente'] ?? null;

if (!$idPaciente) {
    echo "<p>Error: ID del paciente no especificado.</p>";
    exit;
}

// Consulta de turnos
$sql = "SELECT start, end, title FROM turno WHERE id_paciente = ? ORDER BY start DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute([$idPaciente]);
$turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Turnos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'nav_superior_paciente.php'; ?>

<!-- Modal -->
<div class="modal fade" id="turnosModal" tabindex="-1" aria-labelledby="turnosModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="turnosModalLabel">Mis Turnos Tomados</h5>
        <button type="button" class="btn-close" aria-label="Cerrar" 
                onclick="window.location.href='pantalla_paciente.php'"></button>
      </div>
      <div class="modal-body">
        <?php
        if (count($turnos) === 0) {
            echo "<p>No tenés turnos tomados.</p>";
        } else {
            echo "<ul class='list-group'>";
            foreach ($turnos as $turno) {
                $fecha = date("d/m/Y", strtotime($turno['start']));
                $horaInicio = date("H:i", strtotime($turno['start']));
                $horaFin = date("H:i", strtotime($turno['end']));
                $title = ucfirst($turno['title']);  // Capitaliza la primera letra
                
                echo "<li class='list-group-item'>";
                echo "Día : $fecha de $horaInicio a $horaFin - Motivo : $title";
                echo "</li>";
            }            
            echo "</ul>";
        }
        ?>
      </div>
      <div class="modal-footer">
        <a href="pantalla_paciente.php" class="btn btn-secondary">Cerrar</a>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para abrir el modal automáticamente -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    var turnosModal = new bootstrap.Modal(document.getElementById('turnosModal'), {
      backdrop: 'static',
      keyboard: false
    });
    turnosModal.show();
  });
</script>

</body>
</html>
<?php
include 'conexion_be.php';

session_start();

// Si no está logueado, redirige
if (!isset($_SESSION['paciente'])) {
    echo '<script src="../js/alerta.js"></script>;
          <script>alerta("Por favor inicia sesión", "index.php");</script>';
    exit;
}

$idPaciente = $_GET['id_paciente'] ?? null;

if (!$idPaciente) {
    echo "<p>Error: ID del paciente no especificado.</p>";
    exit;
}

// Consulta de turnos
$sql = "SELECT start, end, title FROM turno WHERE id_paciente = ? ORDER BY start DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute([$idPaciente]);
$turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Turnos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'nav_superior_paciente.php'; ?>

<!-- Modal -->
<div class="modal fade" id="turnosModal" tabindex="-1" aria-labelledby="turnosModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="turnosModalLabel">Mis Turnos Tomados</h5>
        <button type="button" class="btn-close" aria-label="Cerrar" 
                onclick="window.location.href='pantalla_paciente.php'"></button>
      </div>
      <div class="modal-body">
        <?php
        if (count($turnos) === 0) {
            echo "<p>No tenés turnos tomados.</p>";
        } else {
            echo "<ul class='list-group'>";
            foreach ($turnos as $turno) {
                $fecha = date("d/m/Y", strtotime($turno['start']));
                $horaInicio = date("H:i", strtotime($turno['start']));
                $horaFin = date("H:i", strtotime($turno['end']));
                $title = ucfirst($turno['title']);  // Capitaliza la primera letra
                
                echo "<li class='list-group-item'>";
                echo "Día : $fecha de $horaInicio a $horaFin - Motivo : $title";
                echo "</li>";
            }            
            echo "</ul>";
        }
        ?>
      </div>
      <div class="modal-footer">
        <a href="pantalla_paciente.php" class="btn btn-secondary">Cerrar</a>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para abrir el modal automáticamente -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    var turnosModal = new bootstrap.Modal(document.getElementById('turnosModal'), {
      backdrop: 'static',
      keyboard: false
    });
    turnosModal.show();
  });
</script>

</body>
</html>
