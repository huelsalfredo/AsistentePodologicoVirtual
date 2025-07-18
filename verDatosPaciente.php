<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'conexion_be.php';

$idPaciente = $_SESSION['id_paciente'] ?? null;

if (!$idPaciente) {
    exit('<div class="alert alert-danger">Paciente no identificado.</div>');
}

$stmt = $conexion->prepare("SELECT * FROM paciente WHERE idPaciente = :id");
$stmt->bindParam(':id', $idPaciente, PDO::PARAM_INT);
$stmt->execute();
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Modal -->
<div class="modal fade" id="modalVerDatos" tabindex="-1" aria-labelledby="tituloVerDatos" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form action="actualizarDatosPaciente.php" method="POST">
        <div class="modal-header bg-info">
          <h5 class="modal-title text-center w-100" id="tituloVerDatos">Modificación de la Información Almacenada</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Apellido:</label>
            <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($paciente['apellido']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombres" value="<?= htmlspecialchars($paciente['nombres']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">DNI:</label>
            <input type="text" class="form-control" name="dni" value="<?= htmlspecialchars($paciente['dni']) ?>" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Correo:</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($paciente['email']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Celular:</label>
            <input type="text" class="form-control" name="celular" value="<?= htmlspecialchars($paciente['celular']) ?>">
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Sección Cambio de Contraseña -->
<!-- Modal: Cambio de Contraseña -->
<div class="modal fade" id="modalCambioClave" tabindex="-1" aria-labelledby="modalCambioClaveLabel" aria-hidden="true"data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formCambioClave"> <!-- method="POST" action="cambiar_contrasena.php"> -->
        <div class="modal-header bg-info">
          <h5 class="modal-title" id="modalCambioClaveLabel">Cambiar Contraseña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="idPaciente" value="<?= $idPaciente ?>">

          <div class="mb-3">
            <label for="claveActual" class="form-label">Clave actual</label>
            <div class="input-group">
              <input type="password" class="form-control" name="claveActual" id="claveActual" required>
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('claveActual', this)">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
          </div>

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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('formCambioClave').addEventListener('submit', function(event) {
    event.preventDefault();

    const claveActual = document.getElementById('claveActual').value.trim();
    const nueva1 = document.getElementById('nuevaClave1').value.trim();
    const nueva2 = document.getElementById('nuevaClave2').value.trim();
    const idPaciente = document.querySelector('input[name="idPaciente"]').value;
    const mensaje = document.getElementById('mensajeError');

    confirmar("¿Desea guardar los cambios?\n\nEsta acción no se puede deshacer.", "Confirmar guardado")
    .then(respuesta => {
      if (!respuesta) {
        return; // el usuario canceló, cortamos acá
      }
      
      // Validación inicial
      if (!claveActual || !nueva1 || !nueva2) {
        mensaje.textContent = 'Todos los campos son obligatorios.';
        return;
      }

      if (nueva1 !== nueva2) {
        mensaje.textContent = 'Las nuevas contraseñas no coinciden.';
        return;
      }

      // AJAX para validar clave actual
      fetch('verificar_clave_actual.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `idPaciente=${encodeURIComponent(idPaciente)}&claveActual=${encodeURIComponent(claveActual)}`
      })
      .then(response => response.text())
      .then(data => {
        if (data === 'OK') {
          mensaje.textContent = '';

          const formData = new URLSearchParams();
          formData.append('idPaciente', idPaciente);
          formData.append('nuevaClave1', nueva1);

          fetch('cambiar_contrasena.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: formData.toString()
          })
          .then(response => response.text())
          .then(data => { 
              console.log("Respuesta del servidor:", data); // para ver que devuelve el servidor (borrar)

              if (data === 'OK') {
                  alerta("Clave cambiada con éxito !!!");

                  const modalEl = document.getElementById('modalCambioClave');

                  // Para restaurar comportamiento normal del modal
                  modalEl.removeAttribute('data-bs-backdrop');
                  modalEl.removeAttribute('data-bs-keyboard');

                  // Cierro el modal normalmente
                  const modal = bootstrap.Modal.getInstance(modalEl);
                  if (modal) {
                      modal.hide();
                  }

                  //  Elimino manualmente cualquier backdrop que haya quedado
                  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                  //  Habilito el scroll y la interacción con el fondo si quedó bloqueado
                  document.body.classList.remove('modal-open');
                  document.body.style = '';

              } else {
                  alerta("La clave no pudo ser cambiada");
              }
          });  

        } else {
          mensaje.textContent = 'La contraseña actual no es correcta.';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        mensaje.textContent = 'Error al validar la contraseña actual.';
      });
    });
  });
</script>

<script>
function togglePassword(idInput) {
  const input = document.getElementById(idInput);
  if (input.type === "password") {
    input.type = "text";
  } else {
    input.type = "password";
  }
}
</script>

<script>
function togglePassword(inputId, button) {
  const input = document.getElementById(inputId);
  const icon = button.querySelector('i');

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
</script>
<script src="../js/alerta.js"></script>
<script src="../js/confirmar.js"></script>
<!-- <script src="../Css/estilos.css"></script> -->