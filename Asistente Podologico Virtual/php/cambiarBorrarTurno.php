<?php
session_start();

$id_paciente = $_SESSION['id_paciente'] ?? null;
$ultimoTurno = null;

// if (!isset($_SESSION['paciente'])) {
//     echo '<script src="../js/alerta.js"></script>;
//           <script>alerta("Por favor inicia sesión", "index.php");</script>';
//     exit;
// }

if ($id_paciente) {
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
}

include 'nav_superior_paciente.php';
?>

<style>
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
        <?php if ($ultimoTurno): 
            $fechaTurno = new DateTime($ultimoTurno['start']);
            $fechaActual = new DateTime();
            $intervalo = $fechaActual->diff($fechaTurno);
            $diferenciaHoras = ($fechaTurno->getTimestamp() - $fechaActual->getTimestamp()) / 3600;

            $puedeModificar = $fechaTurno > $fechaActual && $diferenciaHoras >= 24;

            $motivo = match($ultimoTurno['title']) {
                "ATENCION" => "ATENCION PODOLOGICA",
                "CONTROL" => "CONTROL PODOLOGICO",
                default => htmlspecialchars($ultimoTurno['title']),
            };
        ?>
          <p><strong>Fecha:</strong> <?= $fechaTurno->format("d/m/Y") ?></p>
          <p><strong>Hora:</strong> <?= $fechaTurno->format("H:i") ?> hs</p>
          <p><strong>Motivo:</strong> <?= $motivo ?></p>

          <?php if ($puedeModificar): ?>
            <div class="d-flex justify-content-between mt-4">
              <a href="modificarTurno.php?idTurno=<?= $ultimoTurno['idTurno'] ?>" class="btn btn-warning">Modificar</a>
              <a href="eliminarTurno.php?idTurno=<?= $ultimoTurno['idTurno'] ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro que deseas eliminar este turno?')">Eliminar</a>
            </div>
          <?php else: ?>
            <div class="alert alert-secondary mt-3">No podés modificar ni eliminar este turno porque faltan menos de 24 horas o ya ocurrió.</div>
          <?php endif; ?>
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
