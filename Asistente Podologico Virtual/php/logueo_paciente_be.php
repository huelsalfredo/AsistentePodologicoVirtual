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
    $contrasena = $_POST['contrasena'];
    $contrasena = hash('sha512', $contrasena);
 
    try {
        // Verifica si el paciente existe por su email
        $stmt = $conexion->prepare("SELECT * FROM paciente WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Verifica si el email y la contraseña coinciden
            $stmt = $conexion->prepare("SELECT * FROM paciente WHERE email = :email AND contrasena = :contrasena");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contrasena', $contrasena);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $datos_paciente = $stmt->fetch(PDO::FETCH_ASSOC);
                $apellido = $datos_paciente['apellido'];
                $nombres = $datos_paciente['nombres'];
                $rol = $datos_paciente['rol'];
                $id_paciente = $datos_paciente['idPaciente'];

                $usuario = $apellido . ", " . $nombres;

                // Guardar datos de sesión comunes
                $_SESSION['paciente'] = $usuario;
                $_SESSION['rol'] = $rol;
                $_SESSION['id_paciente'] = $id_paciente;

                // Si es administrador, guardar también su ID como admin y flag de modo admin
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
                <script>
                    alerta("El password ingresado es incorrecto", "index.php");
                </script>';
                exit;
            }
        } else {
            echo '
            <script>
                alerta("Paciente no encontrado. Por favor verifique si escribió bien su correo o regístrese", "index.php");
            </script>';
            exit;
        }
    } catch (PDOException $e) {
        echo '<script>alerta("Error en la conexión: ' . $e->getMessage() . '", "index.php");</script>';
        exit;
    }
} elseif (!isset($_SESSION['paciente'])) {
    // Si no hay sesión y no se ha enviado el formulario, muestra el mensaje de sesión.
    echo '
    <script>
        alerta("Por favor debes iniciar sesión", "index.php");
    </script>';
    session_destroy();
    die();
}
?>
