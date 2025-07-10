<?php

    try {
        // Configuración de la conexión
        // Version hosting
        // $host = "sql110.ezyro.com";
        // $dbname = "ezyro_39426325_AsistentePodologico";
        // $username = "ezyro_39426325";
        // $password = "31484c6c1";

        // version localhost
        $host = "localhost";
        $dbname = "asistentepodologico";
        $username = "root";
        $password = "";

        // Crea la conexión con PDO
        $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

        // Configura atributos para el manejo de errores
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Mensaje de éxito al conectar
        // echo "Conectado exitosamente a la base de datos";

    } catch (PDOException $e) {
        // Maneja errores
        echo 'Error al conectar con la base de datos: ' . $e->getMessage();
    }
?>
