<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Registro de Paciente</title>
    </head>
    <body>
        <!-- Se incluye para poder usar el modal alerta -->
        <script src="../js/alerta.js"></script>
    </body>
</html>

<?php
include 'conexion_be.php'; 

$apellido = $_POST['apellido'];
$nombres = $_POST['nombres'];
$email = $_POST['email'];
$celular = $_POST['celular'];
$dni = $_POST['dni'];
$fechaNac = $_POST['fechaNac'];
$contrasena = hash('sha512', $_POST['contrasena']);

try {
    // Valida que no exista el email
    $stmt = $conexion->prepare("SELECT * FROM paciente WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo '<script>alerta("Este correo ya se encuentra registrado", "index.php");</script>';
        exit;
    }

    // Valida que no exista el DNI
    $stmt = $conexion->prepare("SELECT * FROM paciente WHERE dni = :dni");
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo '<script>alerta("Este DNI ya se encuentra registrado", "index.php");</script>';
        exit;
    }

    // Inserta nuevo paciente
    $stmt = $conexion->prepare("INSERT INTO paciente (apellido, nombres, email, celular, dni, fechaNac, contrasena) 
                                VALUES (:apellido, :nombres, :email, :celular, :dni, :fechaNac, :contrasena)");

    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':celular', $celular);
    $stmt->bindParam(':dni', $dni);
    $stmt->bindParam(':fechaNac', $fechaNac);
    $stmt->bindParam(':contrasena', $contrasena);

    if ($stmt->execute()) {
        echo '<script>alerta("Paciente almacenado con éxito", "index.php");</script>';
    } else {
        echo '<script>alerta("No se pudo registrar el paciente. Intente nuevamente.", "index.php");</script>';
    }

} catch (PDOException $e) {
    echo '<script>alerta("Error en la base de datos: ' . $e->getMessage() . '", "index.php");</script>';
}
?><!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Registro de Paciente</title>
    </head>
    <body>
        <!-- Se incluye para poder usar el modal alerta -->
        <script src="../js/alerta.js"></script>
    </body>
</html>

<?php
include 'conexion_be.php'; 

$apellido = $_POST['apellido'];
$nombres = $_POST['nombres'];
$email = $_POST['email'];
$celular = $_POST['celular'];
$dni = $_POST['dni'];
$fechaNac = $_POST['fechaNac'];
$contrasena = hash('sha512', $_POST['contrasena']);

try {
    // Valida que no exista el email
    $stmt = $conexion->prepare("SELECT * FROM paciente WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo '<script>alerta("Este correo ya se encuentra registrado", "index.php");</script>';
        exit;
    }

    // Valida que no exista el DNI
    $stmt = $conexion->prepare("SELECT * FROM paciente WHERE dni = :dni");
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo '<script>alerta("Este DNI ya se encuentra registrado", "index.php");</script>';
        exit;
    }

    // Inserta nuevo paciente
    $stmt = $conexion->prepare("INSERT INTO paciente (apellido, nombres, email, celular, dni, fechaNac, contrasena) 
                                VALUES (:apellido, :nombres, :email, :celular, :dni, :fechaNac, :contrasena)");

    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':celular', $celular);
    $stmt->bindParam(':dni', $dni);
    $stmt->bindParam(':fechaNac', $fechaNac);
    $stmt->bindParam(':contrasena', $contrasena);

    if ($stmt->execute()) {
        echo '<script>alerta("Paciente almacenado con éxito", "index.php");</script>';
    } else {
        echo '<script>alerta("No se pudo registrar el paciente. Intente nuevamente.", "index.php");</script>';
    }

} catch (PDOException $e) {
    echo '<script>alerta("Error en la base de datos: ' . $e->getMessage() . '", "index.php");</script>';
}
?>