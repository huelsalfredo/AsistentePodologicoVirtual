<!-- ---------- Archivo verDatosPaciente.php ----------------->

<?php
// Primero me aseguro de que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Si no hay sesión activa, la inicio
}

// Incluyo el archivo que me da acceso a la base de datos
include 'conexion_be.php';

// Intento obtener el id del paciente desde la sesión
$idPaciente = $_SESSION['id_paciente'] ?? null;

// Si no tengo el id del paciente, muestro un mensaje de error y corto la ejecución
if (!$idPaciente) {
    exit('<div class="alert alert-danger">Paciente no identificado.</div>');
}

// Preparo la consulta para traer todos los datos del paciente con ese ID
$stmt = $conexion->prepare("SELECT * FROM paciente WHERE idPaciente = :id");

// Enlazo el parámetro :id con el valor real del id del paciente
$stmt->bindParam(':id', $idPaciente, PDO::PARAM_INT);

// Ejecuto la consulta
$stmt->execute();

// Guardo el resultado como un array asociativo
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!------------------ Modal modalVerDatos ------------------->
<!-- Defino el contenedor del modal con ID 'modalVerDatos', oculto por defecto (fade), y con backdrop estático para que no se cierre si hago clic fuera -->
<div class="modal fade" id="modalVerDatos" tabindex="-1" aria-labelledby="tituloVerDatos" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg"> <!-- El modal será grande -->
    <div class="modal-content"> <!-- Contenido principal del modal -->

      <!-- Inicio el formulario. No uso 'action' ni 'method' porque voy a controlar el envío con JS tras confirmación -->
      <form id="formDatosPaciente">  

        <!-- Encabezado del modal -->
        <div class="modal-header bg-info">
          <h5 class="modal-title text-center w-100" id="tituloVerDatos">Modificación de la Información Almacenada</h5>
          <!-- Botón para cerrar el modal -->
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Cuerpo del modal -->
        <div class="modal-body">
          <!-- Campo para Apellido -->
          <div class="mb-3">
            <label class="form-label">Apellido:</label>
            <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($paciente['apellido']) ?>">
          </div>

          <!-- Campo para Nombre -->
          <div class="mb-3">
            <label class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombres" value="<?= htmlspecialchars($paciente['nombres']) ?>">
          </div>

          <!-- Campo para DNI (solo lectura) -->
          <div class="mb-3">
            <label class="form-label">DNI:</label>
            <input type="text" class="form-control" name="dni" value="<?= htmlspecialchars($paciente['dni']) ?>" readonly>
          </div>

          <!-- Campo para Correo -->
          <div class="mb-3">
            <label class="form-label">Correo:</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($paciente['email']) ?>">
          </div>

          <!-- Campo para Celular -->
          <div class="mb-3">
            <label class="form-label">Celular:</label>
            <input type="text" class="form-control" name="celular" value="<?= htmlspecialchars($paciente['celular']) ?>">
          </div>
        </div>

        <!-- Pie del modal con botones -->
        <div class="modal-footer">
          <!-- Botón para enviar el formulario (se confirmará con JS) -->
          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
          <!-- Botón para cerrar el modal -->
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
  // Escucho cuando el usuario intenta enviar el formulario con ID "formDatosPaciente"
  document.getElementById('formDatosPaciente').addEventListener('submit', function(event) {
    // Evito que el formulario se envíe normalmente (por acción HTML)
    event.preventDefault();

    // Le pido al usuario que confirme si quiere guardar los cambios
    confirmar("¿Desea guardar los cambios?\n\nEsta acción no se puede deshacer.", "Confirmar guardado")
    .then(respuesta => {
      // Si el usuario cancela, no hago nada
      if (!respuesta) {
        return;
      }

      // Si confirmó, obtengo el formulario y preparo sus datos
      const form = document.getElementById('formDatosPaciente');
      const formData = new FormData(form); // Los datos se empaquetan para enviar por POST

      // Envío los datos al archivo PHP usando fetch (sin recargar la página)
      fetch('actualizarDatosPaciente.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text()) // Espero que el PHP me devuelva un texto
      .then(data => { 
         // Si el PHP respondió "OK", significa que se guardó correctamente
         if (data === 'OK') {
            // Le muestro al usuario una alerta de éxito
            alerta("Los datos fueron cambiados con éxito !!!");

            // Obtengo el modal para poder cerrarlo manualmente
            const modalEl = document.getElementById('modalVerDatos');

            // Le saco atributos que podrían estar bloqueando el cierre automático
            modalEl.removeAttribute('data-bs-backdrop');
            modalEl.removeAttribute('data-bs-keyboard');

            // Cierro el modal usando Bootstrap
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }

            // Si quedó algún fondo oscuro del modal (backdrop), lo elimino manualmente
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Me aseguro de que el fondo de la página vuelva a ser usable (sin scroll bloqueado)
            document.body.classList.remove('modal-open');
            document.body.style = '';

         } else {
            // Si el servidor no respondió "OK", le aviso que hubo un error
            alerta("Los datos no pudieron ser cambiados");
         }
      });  
    });
  });
</script>

<!--------------------- Sección Cambio de Contraseña ------------------------------>
<!----------------------- Modal modalCambioClave ---------------------------------->
<!-- Modal para cambiar la contraseña -->
<div class="modal fade" id="modalCambioClave" tabindex="-1" aria-labelledby="modalCambioClaveLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Formulario para el cambio de contraseña. Se maneja por JS, por eso no se usa "action" directamente -->
      <form id="formCambioClave"> <!-- method="POST" action="cambiar_contrasena.php"> se cambia para confirmar envío -->

        <!-- Encabezado del modal con título y botón para cerrar -->
        <div class="modal-header bg-info">
          <h5 class="modal-title" id="modalCambioClaveLabel">Cambiar Contraseña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Cuerpo del modal con los campos necesarios -->
        <div class="modal-body">

          <!-- Campo oculto con el id del paciente -->
          <input type="hidden" name="idPaciente" value="<?= $idPaciente ?>">

          <!-- Campo para ingresar la contraseña actual -->
          <div class="mb-3">
            <label for="claveActual" class="form-label">Clave actual</label>
            <div class="input-group">
              <input type="password" class="form-control" name="claveActual" id="claveActual" required>
              <!-- Botón para mostrar/ocultar contraseña -->
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('claveActual', this)">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
          </div>

          <!-- Campo para la nueva contraseña -->
          <div class="mb-3">
            <label for="nuevaClave1" class="form-label">Nueva clave</label>
            <div class="input-group">
              <input type="password" class="form-control" name="nuevaClave1" id="nuevaClave1" required>
              <!-- Botón para mostrar/ocultar contraseña -->
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('nuevaClave1', this)">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
          </div>

          <!-- Campo para repetir la nueva contraseña -->
          <div class="mb-3">
            <label for="nuevaClave2" class="form-label">Repetir nueva clave</label>
            <div class="input-group">
              <input type="password" class="form-control" name="nuevaClave2" id="nuevaClave2" required>
              <!-- Botón para mostrar/ocultar contraseña -->
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('nuevaClave2', this)">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
          </div>

          <!-- Área para mostrar mensajes de error (por ejemplo, claves que no coinciden) -->
          <div id="mensajeError" class="text-danger"></div>
        </div>

        <!-- Pie del modal con los botones de acción -->
        <div class="modal-footer">
          <!-- Botón para cancelar y cerrar el modal -->
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <!-- Botón para enviar el formulario (guardar los cambios) -->
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
  // Se agrega un listener para capturar el envío del formulario y evitar que recargue la página
  document.getElementById('formCambioClave').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita el envío normal del formulario

    // Se obtienen los valores de los campos del formulario
    const claveActual = document.getElementById('claveActual').value.trim();
    const nueva1 = document.getElementById('nuevaClave1').value.trim();
    const nueva2 = document.getElementById('nuevaClave2').value.trim();
    const idPaciente = document.querySelector('input[name="idPaciente"]').value;
    const mensaje = document.getElementById('mensajeError'); // Elemento para mostrar errores

    // Se muestra una ventana de confirmación antes de continuar
    confirmar("¿Desea guardar los cambios?\n\nEsta acción no se puede deshacer.", "Confirmar guardado")
    .then(respuesta => {
      if (!respuesta) {
        return; // El usuario canceló la confirmación, se interrumpe la ejecución
      }
      
      // Validación básica: verificar que los campos no estén vacíos
      if (!claveActual || !nueva1 || !nueva2) {
        mensaje.textContent = 'Todos los campos son obligatorios.';
        return;
      }

      // Validar que las dos nuevas contraseñas coincidan
      if (nueva1 !== nueva2) {
        mensaje.textContent = 'Las nuevas contraseñas no coinciden.';
        return;
      }

      // Se envía la clave actual al servidor para verificar si es correcta
      fetch('verificar_clave_actual.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `idPaciente=${encodeURIComponent(idPaciente)}&claveActual=${encodeURIComponent(claveActual)}`
      })
      .then(response => response.text())
      .then(data => {
        if (data === 'OK') { // La clave actual fue validada correctamente
          mensaje.textContent = ''; // Se limpia cualquier mensaje de error previo

          // Se prepara el nuevo valor para enviar al servidor
          const formData = new URLSearchParams();
          formData.append('idPaciente', idPaciente);
          formData.append('nuevaClave1', nueva1);

          // Se realiza la solicitud para cambiar la contraseña
          fetch('cambiar_contrasena.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
          })
          .then(response => response.text())
          .then(data => { 
              console.log("Respuesta del servidor:", data); // Debug (puede quitarse)

              if (data === 'OK') {
                  alerta("Clave cambiada con éxito !!!"); // Mensaje positivo

                  // Se obtiene la referencia al modal actual
                  const modalEl = document.getElementById('modalCambioClave');

                  // Se restablecen los atributos que bloquean el fondo del modal
                  modalEl.removeAttribute('data-bs-backdrop');
                  modalEl.removeAttribute('data-bs-keyboard');

                  // Se cierra el modal con Bootstrap
                  const modal = bootstrap.Modal.getInstance(modalEl);
                  if (modal) {
                      modal.hide(); // Oculta el modal
                  }

                  // Elimina cualquier fondo de modal que haya quedado
                  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                  // Restaura el estado del scroll en el body
                  document.body.classList.remove('modal-open');
                  document.body.style = '';

              } else {
                  alerta("La clave no pudo ser cambiada"); // Error del servidor
              }
          });  

        } else {
          mensaje.textContent = 'La contraseña actual no es correcta.'; // Error de validación
        }
      })
      .catch(error => {
        // En caso de error en la petición, se informa al usuario
        console.error('Error:', error);
        mensaje.textContent = 'Error al validar la contraseña actual.';
      });
    });
  });
</script>

<!-- 
  FUNCIÓN togglePassword SIMPLE:
  Esta versión solo alterna entre mostrar y ocultar el contenido de un input de tipo password,
  pero NO modifica ningún icono visual. 
  Está pensada para casos simples donde solo se quiere alternar el tipo del input.
-->
<script>
function togglePassword(idInput) {
  const input = document.getElementById(idInput);
  if (input.type === "password") {
    input.type = "text"; // Muestra la contraseña
  } else {
    input.type = "password"; // Oculta la contraseña
  }
}
</script>

<!-- 
  FUNCIÓN togglePassword COMPLETA CON ÍCONO:
  Esta versión es más avanzada y está pensada para inputs que tienen al lado un botón con un ícono.
  Además de alternar el tipo del input, también cambia el ícono (por ejemplo: ojo abierto / cerrado).
-->
<script>
function togglePassword(inputId, button) {
  const input = document.getElementById(inputId);         // Obtiene el input por ID
  const icon = button.querySelector('i');                 // Obtiene el ícono dentro del botón

  if (input.type === "password") {
    input.type = "text";                                  // Muestra la contraseña
    icon.classList.remove("bi-eye-slash");                // Cambia el ícono a "ojo abierto"
    icon.classList.add("bi-eye");
  } else {
    input.type = "password";                              // Oculta la contraseña
    icon.classList.remove("bi-eye");                      // Cambia el ícono a "ojo cerrado"
    icon.classList.add("bi-eye-slash");
  }
}
</script>

<!------------   INCLUSIÓN DE SCRIPTS EXTERNOS:  ------------>
<!---- Para poder usar la función Alerta personalizada (simil alert) -->
<script src="../js/alerta.js"></script>

<!-- Para poder usar la función Confirmar personalizada (simil confirm) -->
<script src="../js/confirmar.js"></script>
<!-- <script src="../Css/estilos.css"></script> -->