<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Eliminar solo los datos del paciente
unset($_SESSION['id_paciente']);
unset($_SESSION['nombre_paciente']);
unset($_SESSION['modo_admin']);

// Cierra la conexión si existe
if (isset($conexion)) {
    $conexion = null;
}

// Redirigir sin destruir toda la sesión
echo '<script>window.location.href = "pantalla_administrador.php";</script>';
?>
