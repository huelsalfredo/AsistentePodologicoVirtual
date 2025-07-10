<?php
include 'conexion_be.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Olvidó su contraseña</title>
    <link rel="stylesheet" href="../Css/estilos.css">
</head>
<body>
    <div class="form-container">
        <h2>Recuperar contraseña</h2>
        <form method="POST" action="">
            <label>Ingrese su DNI o Correo Electrónico</label>
            <input type="text" name="dato" required>
            <button type="submit" name="enviar">Enviar</button>
        </form>
    </div>
</body>
<script src="../js/alerta.js"></script>
</html>

<?php
if (isset($_POST['enviar'])) {
    $dato = $_POST['dato'];

    // Busca por email o DNI
    $stmt = $conexion->prepare("SELECT idPaciente, email, celular, nombres, apellido FROM paciente WHERE email = :dato OR dni = :dato");
    $stmt->bindParam(':dato', $dato);
    $stmt->execute();
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paciente) {
        $email = $paciente['email'];
        $celular = $paciente['celular'];
        $nombre = $paciente['nombres'];
        $apellido = $paciente['apellido'];
        $id = $paciente['idPaciente'];

        // Si tiene email registrado, se envía un correo
        if (!empty($email)) {
            $token = bin2hex(random_bytes(16)); // Token seguro
            $enlace = "resetearPass.php?token=$token&id=$id";

            // Guarda token en tabla (opcional: crear tabla de recuperación)
            $conexion->prepare("INSERT INTO recuperacion_password (id_paciente, token, creado_en) VALUES (:id, :token, NOW())")
                     ->execute([':id' => $id, ':token' => $token]);

            $asunto = "Recuperación de contraseña";
            $mensaje = "Hola $nombre $apellido,\n\nPara restablecer tu contraseña, hacé clic en el siguiente enlace:\n$enlace\n\nSi no solicitaste esto, ignorá este mensaje.";

            // Envia email (simplificado)
            mail($email, $asunto, $mensaje, "From: noreply@tuclinica.com");

            echo "<script>alert('Se envió un correo con instrucciones a $email');</script>";
        }
        // Si no tiene email, pero sí celular, abre WhatsApp
        elseif (!empty($celular)) {
            $texto = urlencode("Hola $nombre, ¿solicitaste un cambio de contraseña en el turnero podológico?");
            echo "<script>window.location.href = 'https://wa.me/54$celular?text=$texto';</script>";
        }
        else {
            echo "<script>alerta('No hay un correo ni celular registrado para este paciente. Contactá al administrador.');</script>";
        }
    } else {
        echo "<script>alerta('No se encontró ningún paciente con ese dato.');</script>";
    }
}
?>