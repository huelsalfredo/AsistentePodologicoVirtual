<?php
session_start();

include 'conexion_be.php';

if (isset($_GET['idPaciente'])) {
    $_SESSION['id_paciente'] = $_GET['idPaciente'];

    // Buscar datos del paciente
    $stmt = $conexion->prepare("SELECT nombres, apellido FROM paciente WHERE idPaciente = :idPaciente");
    $stmt->bindParam(':idPaciente', $_GET['idPaciente']);
    $stmt->execute();
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paciente) {
        $_SESSION['nombre_paciente'] = $paciente['nombres'] . ' ' . $paciente['apellido'];
        $_SESSION['modo_admin'] = true; // Marca que es el administrador usando la vista de paciente
    }
}

if (!isset($_SESSION['id_paciente'])) {
    echo '<script src="../js/alerta.js"></script>;
          <script>alerta("Debes iniciar sesión", "../index.php");</script>';
    exit;
}

$id_paciente = $_SESSION['id_paciente'];

include 'nav_superior_admin.php';

try {

$stmt = $conexion->prepare("SELECT t.idTurno, t.start, t.end, t.title, p.nombres, p.apellido 
    FROM turno t 
    INNER JOIN paciente p ON t.id_paciente = p.idPaciente 
    WHERE t.start < NOW() 
      AND t.id_paciente = :id_paciente
      AND t.idTurno NOT IN (
        SELECT id_turno FROM historiaclinica
      )
    ORDER BY t.start DESC");
    $stmt->bindParam(':id_paciente', $id_paciente);
    $stmt->execute();
    $turnoPendiente = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<script>console.error('Error al verificar turnos pendientes: " . $e->getMessage() . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Calendario</title>
  <link rel="icon" href="../Imagenes/Pardepies.jpg">
  <link rel="stylesheet" href="../Css/estilos.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- jQuery y Moment -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

  <!-- FullCalendar v3 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

  <!-- Idioma español para FullCalendar -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/es.js"></script>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../Css/estilos2.css">

</head>

<body>

  <div class="modal fade show" id="pacientesModal" tabindex="-1" aria-labelledby="pacientesModalLabel" aria-hidden="true" style="display: block;">
    <div class="container fondo">
      <div class="row">
        <div class="d-none d-lg-block col-lg"></div>
        <div class="col-12 col-lg-7 color01 subir-calendario">
          <div id="CalendarioWeb"></div>
        </div>
        <div class="d-none d-lg-block col-lg"></div>
      </div>
    </div>
  </div>

  <!-- Modal para mostrar info del paciente -->
  <div class="modal fade" id="modalInfoTurno" tabindex="-1" aria-labelledby="modalInfoTurnoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content bg-light">
        <div class="modal-body text-center">
          <p id="infoPacienteTurno" class="fw-bold mb-0"></p>
        </div>
      </div>
    </div>
  </div>

  <script>
    const modoAdmin = <?= (isset($_SESSION['modo_admin']) && $_SESSION['modo_admin']) ? 'true' : 'false' ?>;
    const esAdmin = modoAdmin === true || modoAdmin === 'true'; // Acepta string o booleano

    moment.locale('es');
    let fechaSeleccionada = null;

    $('#CalendarioWeb').fullCalendar({
      events: 'turnosTomados.php',
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      defaultView: 'agendaWeek',
      locale: 'es',
      allDaySlot: false,
      minTime: "08:00:00",
      maxTime: "22:00:00",
      slotLabelFormat: 'H:mm',
      slotDuration: '01:00:00',
      slotHeight: 10,

      eventRender: function(event, element) {
        if (event.title === 'CONTROL') {
          element.css('background-color', '#3366cc'); // Azul Francia
          element.css('border-color', '#3366cc');
        } else if (event.title === 'ATENCION') {
          element.css('background-color', '#00008b'); // Azul oscuro
          element.css('border-color', '#00008b');
        }
        element.find('.fc-time').text(event.nombrePaciente); // Mostrar solo nombre y apellido
      },
        
      eventAfterAllRender: function(view) {
        if ($('#btnVolver').length === 0) {
          const volverBtn = $('<button id="btnVolver" class="btn btn-warning btn-sm mx-2">Cerrar Calendario</button>');
          volverBtn.on('click', function () {
            window.location.href = 'pantalla_administrador.php'; 
          });

          // Insertar el botón entre "Hoy" y "Mes"
          $('.fc-left .fc-today-button').after(volverBtn);
        }
      },

      viewRender: function(view, element) {
        if (view.name === "agendaWeek") {
          const start = view.start;
          const end = view.end.clone().subtract(1, 'days');
          const desde = start.format("D");
          const hasta = end.format("D [de] MMMM [de] YYYY");
          const titulo = "Desde " + desde + " al " + hasta;
          $(".fc-center h2").text(titulo);
        }
      },

    eventClick: function(event) {

      const ahora = moment();
      const inicioTurno = moment(event.start);

      if (inicioTurno.isBefore(ahora)) {
        document.getElementById('nombrePaciente').innerText = event.nombrePaciente || 'No disponible';
        document.getElementById('idPaciente').innerText = event.idPaciente || '';
        document.getElementById('idTurno').value = event.id || '';
        document.getElementById('id_paciente').value = event.idPaciente || ''; 
        document.getElementById('fechaTurno').innerText = moment(event.start).format('DD/MM/YYYY');
        document.getElementById('horaTurno').innerText = moment(event.start).format('HH:mm');
        document.getElementById('motivoTurno').innerText = event.title || '';

        const modal = new bootstrap.Modal(document.getElementById('modalNotas'));
        modal.show();
      } else {
        alerta("No se puede cargar una Historia de un turno futuro");
      }
    },

    });
  </script>
  <script src="../js/alerta.js"></script>

  <!-- Modal para turnos anteriores -->
  <div class="modal fade" id="modalNotas" tabindex="-1" aria-hidden="true" C data-bs-keyboard="false">
   <div class="modal-dialog">
    <form method="POST" action="guardar_Historia.php">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title">Carga de Registro de Consulta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p><strong>Paciente:</strong> <span id="nombrePaciente"></span></p>
            <p style="display: none;"><strong>id:</strong> <span id="idPaciente"></span></p>            
            <p><strong>Fecha:</strong> <span id="fechaTurno">
            </span> - <strong>Hora:</strong> <span id="horaTurno"></span></p>          
            <p><strong>Motivo:</strong> <span id="motivoTurno"></span></p>

            <input type="hidden" name="idTurno" id="idTurno">
            <input type="hidden" name="id_paciente" id="id_paciente">

            <input type="hidden" name="id_podologo" value="4">

            <div class="mb-3">
              <label for="notas" class="form-label">Descripcion</label>
              <textarea class="form-control" name="notas" id="notas" required></textarea>
            </div>
            <div class="mb-3">
              <label for="tratamiento" class="form-label">Tratamiento</label>
              <textarea class="form-control" name="tratamiento" id="tratamiento" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

</body>
</html>

