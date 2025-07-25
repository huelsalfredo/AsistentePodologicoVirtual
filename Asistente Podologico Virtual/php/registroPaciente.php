<?php

include 'conexion_be.php';

header('Content-Type: text/plain'); 

$response = [];

$apellido = $_POST['apellido'];
$nombres = $_POST['nombres'];
$email = $_POST['correo'];
$celular = $_POST['celular'];
$dni = $_POST['dni'];
$fechaNac = $_POST['fechaNac'];
$contrasena = hash('sha512', $_POST['contrasena']);

try {
    // Verifico si el email ya está registrado
    // Busco en la tabla paciente una fila donde el dni coincida con el valor que paso, pero solo hago devolver el número 1 y no toda la fila
    $stmt = $conexion->prepare("SELECT 1 FROM paciente WHERE email = :email"); 
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "El correo ya se encuentra registrado.";
        exit;
    }

    // Verifico si el DNI ya está registrado
    $stmt = $conexion->prepare("SELECT 1 FROM paciente WHERE dni = :dni");
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "El DNI ya se encuentra registrado.";
        exit;
    }

    // Inserto nuevo paciente
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
        echo "Registro exitoso";
    } else {
        echo "Error al registrar el paciente.";
    }

} catch (PDOException $e) {
    echo "Error en la base de datos: " . $e->getMessage();
}
