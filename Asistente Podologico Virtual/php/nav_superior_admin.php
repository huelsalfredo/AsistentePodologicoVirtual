<?php
// Inicio la sesión si no está iniciada aún
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluyo el archivo de conexión a la base de datos
include 'conexion_be.php';

// Verifico si el usuario está autenticado como administrador
if (!isset($_SESSION['paciente']) || $_SESSION['rol'] !== 'administrador') {
    echo '
        <script src="../js/alerta.js"></script>
        <script>
            alerta("Acceso denegado. Debes iniciar sesión como administrador.", "index.php");
        </script>
    ';
    session_destroy(); // Cierro la sesión por seguridad
    die(); // Termino la ejecución del script
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>

    <!-- Agrego mi css personalizado -->
    <link rel="stylesheet" href="../Css/estilos.css">

    <!-- Lo que necesito para funcione Bootstrap CSS y Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-custom" style="padding: 0px 80px 0px 10px;">
    <div class="container-fluid">

        <!-- Logo del sistema -->
        <a href="#" class="nav-item">
            <img src="../Imagenes/Logo.jpg" alt="Logo" style="height: 52px; width: auto;">
        </a>

        <!-- Nombre de usuario logueado -->
        <span class="navbar-text ms-3 nav-usuario">👨‍⚕️ : <?php echo htmlspecialchars($_SESSION['paciente']); ?></span>

        <!-- Botón hamburguesa para dispositivos móviles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú desplegable -->
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <div class="navbar-nav ms-auto">

                <!-- Menú Pacientes -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Pacientes
                    </a>
                    <div class="dropdown-menu">
                        <div>
                            <!-- Opción para buscar pacientes -->
                            <a href="#" 
                                onclick="BuscarPacientes(function(id, paciente) {
                                    window.location.href = 'pantalla_paciente.php?idPaciente=' + id;
                                });"                            
                                class="dropdown-item">
                                Seleccionar un Paciente
                            </a>
                        </div>
                        <!-- Listado general de pacientes -->
                        <div><a class="dropdown-item" href="listado.php">Listado de Todos los Pacientes</a></div>
                        <!-- Listado de turnos -->
                        <div><a class="dropdown-item" href="calendarioTurnos.php">Listado de Turnos Actuales</a></div>
                    </div>
                </div>

                <!-- Menú Historias Clínicas -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Historias Clínicas
                    </a>
                    <div class="dropdown-menu">
                        <!-- Cargar Historias Clínicas -->
                        <div><a class="dropdown-item" href="cargarTurnosHC.php">Cargar Historia Clínica</a></div>
                        <div>
                            <!-- Buscar Historias Clínicas -->
                            <a href="#" 
                                onclick="BuscarPacientes(function(id, paciente) {
                                    window.location.href = 'consultarHClinica.php?idPaciente=' + id;
                                });" 
                                class="dropdown-item">
                                Buscar Historia Clínica
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Menú Notificaciones -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Notificaciones
                    </a>
                    <div class="dropdown-menu">
                        <div><a class="dropdown-item" href="enviarRecordatorioTurno.php">Enviar Recordatorio de Turno</a></div>
                        <div><a class="dropdown-item" href="notificar_cambios.php">Notificar Cambios de Turno</a></div>
                    </div>
                </div>

                <!-- Menú Mis Datos (administrador) -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Mis Datos
                    </a>
                    <div class="dropdown-menu"> 
                        <!-- Ver/Editar datos personales -->
                        <div>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalVerDatos">Ver/Editar</a>
                        </div>
                        <!-- Cambio de clave -->
                        <div>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCambioClave">Cambio de Clave</a>
                        </div>
                    </div>
                </div>

                <!-- Botón para cerrar sesión -->
                <div class="nav-item d-flex align-items-center">
                    <form action="cerrarSesion.php" method="POST" class="d-inline m-0 p-0">
                        <button type="submit"
                                class="btn btn-link text-warning p-0 m-0"
                                style="text-decoration: none; color: rgb(57, 255, 20) !important;">
                            🔙 Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Incluyo el PHP para usar el modal con los datos del usuario logueado -->
<?php include 'verDatosPaciente.php'; ?>

<!-- Lo que necesito para que funcione Bootstrap Bundle JS (incluye Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para mostrar automáticamente el modal de selección de paciente -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('pacientesModal'), {
            backdrop: 'static', // No permite cerrar haciendo clic fuera del modal
            keyboard: false     // No permite cerrar con la tecla Escape
        });
        
        myModal.show(); // Muestra el modal al cargar la página

        // Cuando se cierra el modal, se redirige a pantalla_administrador.php
        document.getElementById('pacientesModal').addEventListener('hidden.bs.modal', function () {
            window.location.href = 'pantalla_administrador.php';
        });
    });
</script>

</body>
</html>
