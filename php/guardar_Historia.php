<?php

include 'conexion_be.php';

session_start();

$id_turno     = $_POST['idTurno'] ?? null;
$id_paciente  = $_POST['id_paciente'] ?? null;
$id_podologo  = $_POST['id_podologo'] ?? null;
$notas        = $_POST['notas'] ?? '';
$tratamiento  = $_POST['tratamiento'] ?? '';


confirmar("¿Desea guardar los cambios?\n\nEsta acción no se puede deshacer.", "Confirmar guardado")
.then(respuesta => {
    if (!respuesta) {
    return; // el usuario canceló, cortamos acá
    }

    if ($id_turno && $id_paciente && $notas && $tratamiento) {
        $stmt = $conexion->prepare("INSERT INTO historiaclinica 
            (notas, tratamiento, id_paciente, id_podologo, id_turno, fecha)
            VALUES 
            (:notas, :tratamiento, :id_paciente, :id_podologo, :id_turno, NOW())");

        $stmt->execute([
            ':notas' => $notas,
            ':tratamiento' => $tratamiento,
            ':id_paciente' => $id_paciente,
            ':id_podologo' => $id_podologo,
            ':id_turno' => $id_turno
        ]);

        header("Location: " . $_SERVER['HTTP_REFERER']);

        echo 
            "<script src='../js/alerta.js'></script>;
            <script>
                window.onload = function() {
                    alerta('Historia Clínica guardada correctamente...');
                };
            </script>";
        exit;
    } else {
        echo "Faltan datos para guardar la historia clínica.";
    }
})
?>
