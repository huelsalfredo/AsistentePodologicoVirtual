<?php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['modo_admin']) && $_SESSION['modo_admin']) {
        $nombrePaciente = $_SESSION['nombre_paciente'] ?? 'Paciente no definido';
        $nombreAdmin = $_SESSION['paciente'] ?? 'Administrador';
    } else {
        // Si no está en modo admin, verifica login normal de paciente
        if (!isset($_SESSION['paciente'])) {
            echo '
                <script src="../js/alerta.js"></script>;
                <script>
                    alerta("Por favor debes iniciar sesión", "index.php");
                </script>
            ';
            session_destroy();
            die();
        }

        $nombrePaciente = $_SESSION['paciente'];
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes</title>
    <link rel="stylesheet" href="../Css/estilos.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">

            <?php if (isset($_SESSION['modo_admin']) && $_SESSION['modo_admin']): ?>
                <span class="navbar-text ms-3 nav-usuario">👤 : <?= htmlspecialchars($nombrePaciente); ?></span>
                <span class="navbar-text ms-3 nav-usuario text-warning">👨‍⚕️ : <?= htmlspecialchars($nombreAdmin); ?></span>
            <?php else: ?>
                <a href="#" class="nav-item">Turnero Podológico</a>
                <span class="navbar-text ms-3 nav-usuario">👤 : <?= htmlspecialchars($nombrePaciente); ?></span>
            <?php endif; ?>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>👨‍⚕️ 
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Solicitar mi turno
                        </a>
                        <div class="dropdown-menu">
                            <div><a class="dropdown-item" href="calendarioPaciente.php">Abrir Calendario</a></div>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Ver mi turno
                        </a>
                        <div class="dropdown-menu">
                            <div><a class="dropdown-item" href="ultimoTurnoTomado.php">Ver mi último turno</a></div>
                            <div><a class="dropdown-item" href="visualizarTurnos.php?id_paciente=<?php echo $_SESSION['id_paciente']; ?>">Ver todos mis turnos</a></div>
                        </div>
                    </div>

                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Cambiar mi turno
                        </a>
                        <div class="dropdown-menu">
                            <div><a class="dropdown-item" href="cambiarBorrarTurno.php">Cambiar/Cancelar Turno</a></div>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Mis Datos
                        </a>
                        <div class="dropdown-menu"> 
                            <div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalVerDatos">Ver/Editar</a>
                            </div>
                            <div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCambioClave">Cambio de Clave</a>
                            </div>
                        </div>
                    </div>

                    <!-- Cerrar Sesión -->
                    <?php if (isset($_SESSION['modo_admin']) && $_SESSION['modo_admin']): ?>
                        <div class="nav-item d-flex align-items-center ms-2">
                            <form action="cerrarSesionPaciente.php" method="POST" class="d-inline m-0 p-0">
                                <button type="submit"
                                        class="btn btn-link text-warning p-0 m-0"
                                        style="text-decoration: none; color: rgb(57, 255, 20) !important;">
                                    🔙 Cerrar sesión del paciente
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="nav-item d-flex align-items-center">
                            <form action="cerrarSesion.php" method="POST" class="d-inline m-0 p-0">
                                <button type="submit"
                                        class="btn btn-link text-warning p-0 m-0"
                                        style="text-decoration: none; color: rgb(57, 255, 20) !important;">
                                    🔙 Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </nav>

<?php include 'verDatosPaciente.php'; ?>

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
