<?php
session_start();

$id_paciente = $_SESSION['id_paciente'] ?? null;
$ultimoTurno = null;

if ($id_paciente) {
    try {

    include 'conexion_be.php';

    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sentenciaSQL = $conexion->prepare("
            SELECT * FROM turno 
            WHERE id_paciente = :id_paciente 
            ORDER BY start DESC 
            LIMIT 1
        ");
        $sentenciaSQL->bindParam(':id_paciente', $id_paciente);
        $sentenciaSQL->execute();
        $ultimoTurno = $sentenciaSQL->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error al conectar con la base de datos: " . $e->getMessage();
    }
}

// Incluye nav sin volver a hacer session_start() dentro de él
include 'nav_superior_paciente.php';
?>

<style>
  /* Mueve el modal 30px más abajo */
  #modalUltimoTurno .modal-dialog {
    margin-top: 100px !important;
  }
</style>

<!-- Modal Último Turno -->
<div class="modal fade" id="modalUltimoTurno" tabindex="-1" aria-labelledby="modalUltimoTurnoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="modalUltimoTurnoLabel">Último Turno Tomado</h5>
      </div>
      <div class="modal-body">
        <?php
            if ($ultimoTurno):
                $motivo = "";
                if ($ultimoTurno['title'] === "ATENCION") {
                    $motivo = "ATENCION PODOLOGICA";
                } elseif ($ultimoTurno['title'] === "CONTROL") {
                    $motivo = "CONTROL PODOLOGICO";
                } else {
                    // En caso de que sea otro valor distinto
                    $motivo = htmlspecialchars($ultimoTurno['title']);
                }
        ?>  
          <p><strong>Fecha:</strong> <?= date("d/m/Y", strtotime($ultimoTurno['start'])) ?></p>
          <p><strong>Hora:</strong> <?= date("H:i", strtotime($ultimoTurno['start'])) ?> hs</p>
          <p><strong>Motivo:</strong> <?= htmlspecialchars($motivo) ?></p>
        <?php else: ?>
          <p>No se encontró un turno registrado.</p>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='pantalla_paciente.php'">Cerrar</button>      
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS con Popper (sólo si no está ya incluido en nav_superior_paciente.php) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Muestra el modal automáticamente al cargar la página
document.addEventListener("DOMContentLoaded", function() {
    var modal = new bootstrap.Modal(document.getElementById('modalUltimoTurno'), {
      backdrop: 'static',
      keyboard: false
    });
    modal.show();
  });
</script>
