<?php

    // Primero me aseguro de que la sesión esté activa, si no lo está, la inicio
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si estoy en modo administrador, manejo los datos tanto del paciente como del admin
    if (isset($_SESSION['modo_admin']) && $_SESSION['modo_admin']) {

        // Acá me guardo el nombre del paciente actual, si existe, o pongo un texto por defecto
        $nombrePaciente = $_SESSION['nombre_paciente'] ?? 'Paciente no definido';

        // Acá guardo el nombre del admin que está logueado
        $nombreAdmin = $_SESSION['paciente'] ?? 'Administrador';

        // Guardo también el ID del paciente
        $idnombrePaciente = $_SESSION['idnombre_paciente'] ?? 'Paciente no definido';

        // Este es el ID real del administrador logueado
        $idnombreAdmin = $_SESSION['id_paciente'] ?? 'Administrador';

        // Ahora, si por algún motivo no hay un paciente asignado (por ejemplo, si se quiere ver "Mis datos" sin haber cargado uno),
        // uso los datos del admin directamente para que no falle nada.
        if ($idnombrePaciente === '' || $idnombrePaciente === null) {
            $idnombrePaciente = $idnombreAdmin;
            $nombrePaciente = $nombreAdmin;
        }

    } else {
        // Si no estoy en modo admin, es un paciente logueado normalmente

        // Me fijo si hay alguien logueado. Si no, muestro alerta y lo saco
        if (!isset($_SESSION['paciente'])) {
            echo '
                <script src="../js/alerta.js"></script>;
                <script>
                    alerta("Por favor debes iniciar sesión", "index.php");
                </script>
            ';
            session_destroy(); // Limpio la sesión
            die(); // Corto la ejecución del archivo
        }

        // Si está todo ok, me guardo el nombre del paciente
        $nombrePaciente = $_SESSION['paciente'];
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes</title>
    
    <!-- CSS general del sitio -->
    <link rel="stylesheet" href="../Css/estilos.css">

    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    <!-- Inicio del navbar (barra superior del sitio) -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">

            <?php if (isset($_SESSION['modo_admin']) && $_SESSION['modo_admin']): ?>
                <!-- Si soy admin, muestro el nombre del paciente y el nombre del admin -->
                <span class="navbar-text ms-3 nav-usuario">👤 : <?= htmlspecialchars($nombrePaciente); ?></span>
                <span class="navbar-text ms-3 nav-usuario text-warning">👨‍⚕️ : <?= htmlspecialchars($nombreAdmin); ?></span>
            <?php else: ?>
                <!-- Si soy paciente común, solo muestro el nombre del paciente -->
                <a href="#" class="nav-item">Turnero Podológico</a>
                <span class="navbar-text ms-3 nav-usuario">👤 : <?= htmlspecialchars($nombrePaciente); ?></span>
            <?php endif; ?>

            <!-- Botón hamburguesa en móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>👨‍⚕️ 
            </button>

            <!-- Menú desplegable -->
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <div class="navbar-nav ms-auto">

                    <!-- Menú para solicitar turno -->
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Solicitar mi turno</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="calendarioPaciente.php">Abrir Calendario</a>
                        </div>
                    </div>

                    <!-- Menú para ver mis turnos -->
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Ver mi turno</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="ultimoTurnoTomado.php">Ver mi último turno</a>
                            <a class="dropdown-item" href="visualizarTurnos.php?id_paciente=<?php echo $_SESSION['id_paciente']; ?>">Ver todos mis turnos</a>
                        </div>
                    </div>

                    <!-- Menú para cambiar o cancelar turno -->
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Cambiar mi turno</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="cambiarBorrarTurno.php">Cambiar/Cancelar Turno</a>
                        </div>
                    </div>

                    <!-- Menú para editar datos o cambiar clave -->
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Mis Datos</a>
                        <div class="dropdown-menu"> 
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalVerDatos">Ver/Editar</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCambioClave">Cambio de Clave</a>

                            <!-- Solo visible si soy admin -->
                            <?php if (isset($_SESSION['modo_admin']) && $_SESSION['modo_admin']): ?>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalBlanqueoClave">Blanqueo de Clave</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Botón para cerrar sesión -->
                    <?php if (isset($_SESSION['modo_admin']) && $_SESSION['modo_admin']): ?>
                        <!-- Cierro sesión del paciente si soy admin -->
                        <div class="nav-item d-flex align-items-center ms-2">
                            <form action="cerrarSesionPaciente.php" method="POST">
                                <button type="submit" class="btn btn-link text-warning p-0 m-0">🔙 Cerrar sesión del paciente</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Cierro mi propia sesión si soy paciente común -->
                        <div class="nav-item d-flex align-items-center">
                            <form action="cerrarSesion.php" method="POST">
                                <button type="submit" class="btn btn-link text-warning p-0 m-0">🔙 Cerrar Sesión</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Incluyo el PHP que tiene el modal que muestra los datos del paciente -->
    <?php include 'verDatosPaciente.php'; ?>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para mostrar automáticamente el modal al cargar y redirigir al cerrar -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Creo el modal y lo muestro
            var myModal = new bootstrap.Modal(document.getElementById('pacientesModal'), {
                backdrop: 'static',
                keyboard: false
            });
            myModal.show();

            // Cuando el modal se cierra, vuelvo a pantalla_administrador.php
            document.getElementById('pacientesModal').addEventListener('hidden.bs.modal', function () {
                window.location.href = 'pantalla_administrador.php';
            });
        });
    </script>

    <!-- Modal para confirmar blanqueo de clave -->
    <div class="modal fade" id="modalBlanqueoClave" tabindex="-1" aria-labelledby="blanqueoClaveLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="blanqueoClaveLabel">Confirmar Blanqueo de Clave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>Se está por <strong>BLANQUEAR</strong> la clave de <strong><?= htmlspecialchars($nombrePaciente) ?></strong>. ¿Deseás continuar?</p>
                </div>
                <div class="modal-footer">
                    <form method="POST" action="blanquearClave.php">
                        <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($idnombrePaciente) ?>">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Sí, blanquear</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
