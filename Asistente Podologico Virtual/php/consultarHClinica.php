<?php

include 'conexion_be.php';
include 'nav_superior_paciente.php';

$idPaciente = $_GET['idPaciente'] ?? null;

if (!$idPaciente) {
    http_response_code(400);
    echo "ID de paciente no especificado.";
    exit;
}

try {
$query = "SELECT t.start, hc.notas, hc.tratamiento, p.nombres, p.apellido, p.dni
          FROM historiaclinica hc
          INNER JOIN turno t ON hc.id_turno = t.idTurno
          INNER JOIN paciente p ON hc.id_paciente = p.idPaciente
          WHERE hc.id_paciente = :idPaciente
          ORDER BY t.idTurno DESC";

    $stmt = $conexion->prepare($query);

    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':idPaciente', $idPaciente, PDO::PARAM_INT);
    $stmt->execute();
    $historiasClinicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($historiasClinicas)) {
        echo '<script src="../js/alerta.js"></script>';
        echo '<script>';
        echo 'alerta("No se encontraron historias clínicas para este paciente.", "pantalla_administrador.php");';
        echo '</script>';
        exit;
    }

    $nombrePaciente = $historiasClinicas[0]['nombres'];
    $apellidoPaciente = $historiasClinicas[0]['apellido'];
    $dniPaciente = $historiasClinicas[0]['dni'];

} catch (Exception $e) {    

        echo '<script src="../js/alerta.js"></script>';
        echo '<script>';
        echo 'alerta("Error: ' . htmlspecialchars($e->getMessage()) . '", "pantalla_administrador.php");';
        echo '</script>';
        exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Historias Clínicas</title>
    <link rel="stylesheet" href="../Css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="modal fade" id="historiasClinicasModal" tabindex="-1" aria-labelledby="tituloHistorial" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="tituloHistorial">
          Historia clínica de <?php echo "$nombrePaciente $apellidoPaciente - DNI: $dniPaciente"; ?>
        </h5>
        <button type="button" class="btn-close" id="btnCerrarX" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">Turno</th>
                    <th>Descripción</th>
                    <th>Tratamiento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historiasClinicas as $historia): ?>
                <tr>
                    <td class="text-center"><?= htmlspecialchars(date("d/m/Y H:i", strtotime($historia['start']))) ?></td>
                    <td><?= htmlspecialchars($historia['notas']) ?></td>
                    <td><?= htmlspecialchars($historia['tratamiento']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="btnCerrarPie" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Crea el modal con opciones para evitar cierre accidental
    const modal = new bootstrap.Modal(document.getElementById('historiasClinicasModal'), {
        backdrop: 'static',
        keyboard: false
    });

    modal.show();

    // Redirige, al cerrar con la X o el botón Cerrar
    document.getElementById('btnCerrarX').addEventListener('click', function () {
        window.location.href = 'pantalla_administrador.php';
    });

    document.getElementById('btnCerrarPie').addEventListener('click', function () {
        window.location.href = 'pantalla_administrador.php';
    });

    // Por seguridad, también capturamos el cierre del modal en general
    document.getElementById('historiasClinicasModal').addEventListener('hidden.bs.modal', function () {
        window.location.href = 'pantalla_administrador.php';
    });
});
</script>

</body>
</html>
