<?php
session_start();
include 'conexion_be.php';

if (!isset($_SESSION['paciente'])) {
    echo '<script src="../js/alerta.js"></script>;
          <script>alerta("Por favor inicia sesión", "index.php");</script>';
    exit;
}

$id_paciente = $_SESSION['id_paciente'];

// Obtiene el último turno
$stmt = $conexion->prepare("SELECT * FROM turno WHERE id_paciente = :id_paciente ORDER BY start DESC LIMIT 1");
$stmt->bindParam(':id_paciente', $id_paciente);
$stmt->execute();
$turno = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica si existe y si es modificable
if (!$turno) {
    echo '<script src="../js/alerta.js"></script>;
          <script>alerta("No se encontró un turno registrado."); window.location.href="pantalla_paciente.php";</scrip>';
    exit;
}

$fechaTurno = strtotime($turno['start']);
$ahora = time();

if (($fechaTurno - $ahora) < 86400) {
    echo '<script src="../js/alerta.js"></script>;
          <script>alerta("No se puede modificar un turno con menos de 24 horas de anticipación."); window.location.href="pantalla_paciente.php";</script>';
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Modificar Turno</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
  <link rel="stylesheet" href="../Css/estilos2.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/es.js"></script>

</head>
<body class="bg-ligt>
  <?php include 'nav_superior_paciente.php'; ?>

  <div class="modal fade show" id="pacientesModal" tabindex="-1" aria-labelledby="pacientesModalLabel" aria-hidden="true" style="display: block;">
    <div class="container fondo">
      <div class="row">
        <div class="d-none d-lg-block col-lg"></div>
        <div class="col-12 color01 col-lg-7 subir-calendario">
            <h3 class="text-center">Modificar Turno</h3>
            <p class="text-center">Tu turno actual: <strong><?= date('d/m/Y H:i', strtotime($turno['start'])) ?></strong> (<?= $turno['title'] ?>)</p>
            <div id="CalendarioWeb" class="mt-4"></div>
        </div>
        <div class="d-none d-lg-block col-lg"></div>
      </div>
    </div>
  </div>

<!-- Modal Modificar Turno -->
<div class="modal fade" id="modalModificar" tabindex="-1" aria-labelledby="modalModificarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header color02">
        <h1 class="modal-title fs-5" id="modalModificarLabel">Modificar Turno</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- Cuerpo del modal -->
      <div class="modal-body color01" id="modalModificarBody">
        <!-- Texto con la nueva fecha -->
        <p id="nuevaFechaTexto" class="text-center fs-4 fw-semibold"></p>
        <p> ____________________________________________________________________ </p>
        <p class="text-center fs-7">Seleccione el nuevo tipo de atención para este turno:</p>

        <!-- Checkboxes -->
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="checkAtencion" value="ATENCION">
          <label class="form-check-label fw-semibold" for="checkAtencion">
            Atención podológica nueva?
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="checkControl" value="CONTROL">
          <label class="form-check-label fw-semibold" for="checkControl">
            Control de atención podológica anterior?
          </label>
        </div>
      </div>

      <!-- Pie del modal -->
      <div class="modal-footer color02">
        <button type="button" class="btn btn-primary" id="btnGuardarCambios">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script>
let fechaSeleccionada = null;
let idTurno = <?= json_encode($turno['idTurno']) ?>;
let tipoActual = <?= json_encode($turno['title']) ?>;

$('#CalendarioWeb').fullCalendar({
  header: {
    left: 'prev,next today',
    center: 'title',
    right: 'agendaWeek,agendaDay'
  },
  defaultView: 'agendaWeek',
  locale: 'es',
  allDaySlot: false,
  minTime: "08:00:00",
  maxTime: "22:00:00",
  slotDuration: '01:00:00',
  events: 'turnosTomados.php',

  eventAfterAllRender: function(view) {
    if ($('#btnVolver').length === 0) {
      const volverBtn = $('<button id="btnVolver" class="btn btn-warning btn-sm mx-2">Salir sin Guardar</button>');
      volverBtn.on('click', function () {
        window.location.href = 'pantalla_paciente.php'; 
      });

      // Inserta el botón entre "Hoy" y "Mes"
      $('.fc-left .fc-today-button').after(volverBtn);
    }
  },

  dayClick: function(date, jsEvent, view) {
    const ahora = moment();  // Captura la fecha y hora actual
    const hoy = moment().startOf('day');  // Captura el dia actual
    const limite = moment().add(2, 'hours');  // Le suma 2 horas a la fecha y hora actual
  
      // Detecta si es domingo
    if (date.day() === 0) { // 0 = Domingo
      alerta("No se pueden tomar turnos los domingos.");
      return;
    }

      // Detecta si es sábados
    if (date.day() === 6) { // 6 = Sábado
      alerta("No se pueden tomar turnos los sabados.");
      return;
    }

    // Lista de feriados (puede crecer)
    const feriados = [
      '01-01',  // 1ro de año
      '05-01',  // dia del trabajador
      '05-25',  // 25 de mayo
      '06-20',  // 20 de junio
      '07-09',  // 9 de julio
      '09-17',  // 17 de agosto
      '10-12',  // 12 de octubre
      '08-12',  // 8 de diciembre
      '12-25'   // navidad
      // Agregar más fechas en formato MM-DD
    ];

    if (feriados.includes(date.format('MM-DD'))) {
      alerta("No se pueden tomar turnos en días feriados.");
      return;
    }
    
    if (ahora.format("YYYY-MM-DD HH:mm")>date.format("YYYY-MM-DD HH:mm")){
      alerta("No se puede elegir una fecha pasada..."); return}
    else { if (limite.format("YYYY-MM-DD HH:mm")>date.format("YYYY-MM-DD HH:mm")) {
              alerta("No se puede tomar un turno con menos de 2 horas de antelacion..");
              return;}
    }      

    fechaSeleccionada = date;
    $('#nuevaFechaTexto').text("Nuevo turno: " + date.format("dddd DD/MM/YYYY [a las] HH:mm") + " hs");

    // Marcar el checkbox según el tipo actual
    if (tipoActual === 'ATENCION') {
      $('#checkAtencion').prop('checked', true);
      $('#checkControl').prop('checked', false);
    } else {
      $('#checkControl').prop('checked', true);
      $('#checkAtencion').prop('checked', false);
    }

    const modal = new bootstrap.Modal(document.getElementById('modalModificar'));
    modal.show();
  },

  eventRender: function(event, element) {
    element.find('.fc-title').text('RESERVADO');
    element.css('font-size', '12px');
  }
});

// Validación de checkboxes
$('#checkAtencion').on('change', function() {
  if (this.checked) $('#checkControl').prop('checked', false);
});
$('#checkControl').on('change', function() {
  if (this.checked) $('#checkAtencion').prop('checked', false);
});

// Guardar cambios
$('#btnGuardarCambios').on('click', function() {
  if (!fechaSeleccionada) return;

  let nuevoTipo = $('#checkAtencion').is(':checked') ? 'ATENCION' :
                  $('#checkControl').is(':checked') ? 'CONTROL' : null;

  if (!nuevoTipo) {
    alerta("Debe seleccionar un tipo de turno.");
    return;
  }

  $.ajax({
    url: 'guardar_modificacion_turno.php',
    method: 'POST',
    data: {
      nuevo_start: fechaSeleccionada.format('YYYY-MM-DD HH:mm:ss'),
      nuevo_end: fechaSeleccionada.clone().add(1, 'hour').format('YYYY-MM-DD HH:mm:ss'),
      nuevo_title: nuevoTipo,
      idTurno: idTurno
    },
    success: function(response) {
      alerta("El turno se ha cambiado con exito !!!","pantalla_paciente.php");
    },
    error: function() {
      alerta("Error al modificar el turno.");
    }
  });
});
</script>
<script src="../js/alerta.js"></script>
<script src="../js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
