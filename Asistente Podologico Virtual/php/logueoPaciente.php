<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Logueo del Paciente</title>
    <link rel="icon" href="../Imagenes/Pardepies.jpg">
    <link rel="stylesheet" href="../Css/estilos.css">
</head>
<body>
    <!-- Se incluye para poder usar el modal alerta -->
    <script src="../js/alerta.js"></script>
</body>
</html>

<?php
session_start();
include 'conexion_be.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $contrasena_raw = $_POST['contrasena']; // Contraseña tal como la ingresó el usuario
    $contrasena_hash = hash('sha512', $contrasena_raw);

    try {
        // Verificar si el email existe
        $stmt = $conexion->prepare("SELECT * FROM paciente WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $datos_paciente = $stmt->fetch(PDO::FETCH_ASSOC);
            $clave_guardada = $datos_paciente['contrasena'];
            $rol = $datos_paciente['rol'];
            $id_paciente = $datos_paciente['idPaciente'];
            $usuario = $datos_paciente['apellido'] . ", " . $datos_paciente['nombres'];

            // Si la contraseña ingresada está vacía y coincide con la clave blanqueada
            if ($contrasena_raw === "" && $clave_guardada === hash('sha512', "")) {
                // Se considera clave blanqueada, redirige a pantalla de cambio
                $_SESSION['id_paciente'] = $id_paciente;
                $_SESSION['nombre_paciente'] = $datos_paciente['nombres'] . ' ' . $datos_paciente['apellido'];
                header("Location: cambiarClaveBlanqueada.php");
                exit;
            }

            // Si coincide el email y contraseña hasheada
            if ($clave_guardada === $contrasena_hash) {
                $_SESSION['paciente'] = $usuario;
                $_SESSION['rol'] = $rol;
                $_SESSION['id_paciente'] = $id_paciente;

                if ($rol === "administrador") {
                    $_SESSION['id_administrador'] = $id_paciente;
                    $_SESSION['modo_admin'] = true;
                    header("Location: pantalla_administrador.php");
                } else {
                    header("Location: pantalla_paciente.php");
                }
                exit;
            } else {
                echo '
                <script src="../js/alerta.js"></script>
                <script>alerta("El password ingresado es incorrecto", "index.php");</script>';
                exit;
            }
        } else {
            echo '
            <script src="../js/alerta.js"></script>
            <script>alerta("Paciente no encontrado. Por favor verifique si escribió bien su correo o regístrese", "index.php");</script>';
            exit;
        }
    } catch (PDOException $e) {
        echo '<script>alerta("Error en la conexión: ' . $e->getMessage() . '", "index.php");</script>';
        exit;
    }
} elseif (!isset($_SESSION['paciente'])) {
    echo '
    <script src="../js/alerta.js"></script>
    <script>alerta("Por favor debes iniciar sesión", "index.php");</script>';
    session_destroy();
    die();
}
?>
