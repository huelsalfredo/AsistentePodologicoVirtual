<?php
session_start();

include 'conexion_be.php';

// Asegurarse de que hay sesión y nombre del paciente
$nombre = $_SESSION['nombre_paciente'] ?? 'Paciente';
$idPaciente = $_SESSION['id_paciente'] ?? null;

if (!$idPaciente) {
    echo "Error: No se pudo obtener el ID del paciente.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cambio de Contraseña</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../Css/estilos.css">

  <style>
    body {
      background-color: #f8f9fa;
      max-width: 500px;
      margin: 50px auto;
    }
    .modal-dialog {
      background-color: rgb(242, 247, 248,0.85);
      border-radius: 10px;
      max-width: 500px;
      margin: 50px auto;
      padding: 20px;
    }
  </style>
</head>
<body>

<div class="modal-dialog">
  <div class="modal-content">

    <form id="formBlanqueodeClave">
      <div class="modal-header bg-info">
        <h5 class="modal-title text-center p-2">Cambio de Contraseña por Blanqueo</h5>
      </div>

      <div class="modal-body text-center p-4">
        <p class="fw-bold">Bienvenido/a, <?= htmlspecialchars($nombre) ?></p>
        <p>Tu contraseña fue blanqueada.<br>Por favor, creá una nueva para continuar.</p>

        <input type="hidden" name="idPaciente" value="<?= $idPaciente ?>">

        <div class="mb-3">
          <label for="nuevaClave1" class="form-label">Nueva clave</label>
          <div class="input-group">
            <input type="password" class="form-control" name="nuevaClave1" id="nuevaClave1" required>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('nuevaClave1', this)">
              <i class="bi bi-eye-slash"></i>
            </button>
          </div>
        </div>

        <div class="mb-3">
          <label for="nuevaClave2" class="form-label">Repetir nueva clave</label>
          <div class="input-group">
            <input type="password" class="form-control" name="nuevaClave2" id="nuevaClave2" required>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('nuevaClave2', this)">
              <i class="bi bi-eye-slash"></i>
            </button>
          </div>
        </div>

        <div id="mensajeError" class="text-danger"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='pantalla_paciente.php'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>

  </div>
</div>

<script>
function togglePassword(idInput, btn) {
  const input = document.getElementById(idInput);
  const icon = btn.querySelector("i");

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("bi-eye-slash");
    icon.classList.add("bi-eye");
  } else {
    input.type = "password";
    icon.classList.remove("bi-eye");
    icon.classList.add("bi-eye-slash");
  }
}

document.getElementById("formBlanqueodeClave").addEventListener("submit", function(e) {
  e.preventDefault();

  const clave1 = document.getElementById("nuevaClave1").value;
  const clave2 = document.getElementById("nuevaClave2").value;
  const mensajeError = document.getElementById("mensajeError");

  // Validación del largo de la contraseña
   if (clave1.length < 6 || clave1.length > 15) {
       mensajeError.textContent = "La contraseña debe tener entre 6 y 15 caracteres.";
       return;
   }

  if (clave1 !== clave2) {
    mensajeError.textContent = "Las contraseñas no coinciden.";
    return;
  }

  const formData = new FormData(this);

  fetch("cambiar_contrasena.php", {
    method: "POST",
    body: formData
  })
  .then(resp => resp.text())
  .then(texto => {
    if (texto.trim() === "OK") {
      alerta("Contraseña actualizada correctamente");
      window.location.href = "pantalla_paciente.php";
    } else {
      mensajeError.textContent = "Error al actualizar la contraseña.";
    }
  })
  .catch(() => {
    mensajeError.textContent = "Error en la conexión.";
  });
});
</script>

<!-- Se incluye para poder usar el modal alerta -->
<script src="../js/alerta.js"></script>

</body>
</html>
