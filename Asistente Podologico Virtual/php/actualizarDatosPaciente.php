<!-- ---------- Archivo actualizarDatosPaciente.php ----------------->

<?php

// Inicio la sesión para acceder a variables como id del paciente
session_start();

// Incluyo el archivo de conexión a la base de datos
include 'conexion_be.php';

// Guardo en una variable el id del paciente que tengo en sesión
$idPaciente = $_SESSION['id_paciente'];

// Verifico si el formulario se envió por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Tomo los datos enviados desde el formulario
    $apellido = $_POST['apellido'];
    $nombres = $_POST['nombres'];
    $email = $_POST['email'];
    $celular = $_POST['celular'];

    // Preparo la consulta SQL para actualizar los datos del paciente
    $stmt = $conexion->prepare("UPDATE paciente 
                                SET apellido = :apellido, nombres = :nombres, email = :email, celular = :celular 
                                WHERE idPaciente = :id");

    // Asigno los valores a los parámetros de la consulta
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':celular', $celular);
    $stmt->bindParam(':id', $idPaciente);

    // Ejecuto la consulta y devuelvo una respuesta al frontend
    if ($stmt->execute()) {
        echo 'OK';     // Si todo salió bien, devuelvo "OK"
    } else {
        echo 'ERROR';  // Si algo falló, devuelvo "ERROR"
    }    
}
?>
