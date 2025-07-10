<?php 

    if (session_status() === PHP_SESSION_NONE) {
        session_start();}

    $_SESSION = array();

    // Si tenes una conexión activa con PDO, asegura cerrarla.
    if (isset($conexion)) {
        $conexion = null; // Esto cierra la conexión en PDO.
    }

    // Destruye la sesión del usuario
    session_destroy();

    // Redirige al usuario a la página principal o de inicio de sesión
    echo '<script>window.location.href = "index.php";</script>';
  //  exit();

?>
