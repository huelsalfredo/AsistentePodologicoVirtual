<?php

include 'conexion_be.php';

try {
    // Consulta para obtener el listado de pacientes
    $query = "SELECT apellido, nombres, email, celular, dni FROM paciente";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<script>alert("Error en la consulta: ' . $e->getMessage() . '");</script>');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Pacientes</title>
    <link rel="stylesheet" href="../Css/estilos.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>

<?php include 'nav_superior_admin.php'; ?>

<!-- Modal que se muestra al cargar la página -->
<div class="modal fade show" id="pacientesModal" tabindex="-1" aria-labelledby="pacientesModalLabel" aria-hidden="true" style="display: block;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="pacientesModalLabel">Listado de Pacientes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Apellido</th>
                            <th>Nombres</th>
                            <th>Correo</th>
                            <th>Celular</th>
                            <th>DNI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pacientes as $row) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombres']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['celular']); ?></td>
                                <td><?php echo htmlspecialchars($row['dni']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Popper.js y Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para abrir el modal automáticamente y redirigir al cerrar -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('pacientesModal'), {
            backdrop: 'static', // Evita que se cierre al hacer clic fuera del modal
            keyboard: false     // Evita que se cierre al presionar la tecla Escape
        });
        
        myModal.show();

        // Detecta el evento de cierre del modal y redirige
        document.getElementById('pacientesModal').addEventListener('hidden.bs.modal', function () {
            window.location.href = 'pantalla_administrador.php';
        });
    });
</script>

</body>
</html>
