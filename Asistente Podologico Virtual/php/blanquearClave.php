<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Blanquear Contraseña</title>
    <link rel="icon" href="../Imagenes/Pardepies.jpg">
    <link rel="stylesheet" href="../Css/estilos.css">
</head>
<body>
    <!-- Se incluye para poder usar el modal alerta -->
    <script src="../js/alerta.js"></script>
</body>
</html>

<?php
// Inicio la sesión para acceder a variables de sesión si es necesario
session_start();

// Incluyo el archivo con la conexión a la base de datos
include 'conexion_be.php'; 

// Verifico si se recibió una solicitud POST con el ID del paciente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_paciente'])) {
    $idPaciente = $_POST['id_paciente'];        // Capturo el ID del paciente desde el formulario
    $clave_blanqueada = hash('sha512', '');     // Genero la clave en blanco encriptada (cadena vacía en sha512)

    // Consulto cual es la clave actual
    $stmtCheck = $conexion->prepare("SELECT contrasena FROM paciente WHERE idPaciente = :id");
    $stmtCheck->bindParam(':id', $idPaciente, PDO::PARAM_INT);
    $stmtCheck->execute();
    $claveActual = $stmtCheck->fetchColumn();      // Obtengo la contraseña actual

    // Verifico si la clave actual ya fue blanqueda
    if ($claveActual === $clave_blanqueada) {
        echo '<script src="../js/alerta.js"></script>;
              <script>alerta("La clave ya está blanqueada.","pantalla_paciente.php?idPaciente='.$idPaciente.'");</script>';
        exit;
    }

    // Actualizo la contraseña
    $stmt = $conexion->prepare("UPDATE paciente SET contrasena = :clave WHERE idPaciente = :id");
    $stmt->bindParam(':clave', $clave_blanqueada);           // Asocio la nueva clave (vacía)
    $stmt->bindParam(':id', $idPaciente, PDO::PARAM_INT);    // Asocio el ID del paciente

    // Ejecuta la actualización
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
             // Si se modificó al menos una fila, muestra mensaje de éxito
            echo '<script src="../js/alerta.js"></script>;
                  <script>alerta("Clave blanqueada exitosamente.","pantalla_paciente.php?idPaciente='.$idPaciente.'");</script>';
        } else {
            echo '<script src="../js/alerta.js"></script>;
                  <script>alerta("La clave no se modificó, \n posiblemente ya se haya blanqueado con anterioridad.","pantalla_paciente.php?idPaciente='.$idPaciente.'");</script>';
        }
    } else {
        // Si hubo un error en la ejecución de la consulta, se muestra el error
        $error = $stmt->errorInfo();
        echo "Error al actualizar: " . $error[2];
    }

    exit;
}
?>
