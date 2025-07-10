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
    echo '<script src="../js/alerta.js"></script>';
    echo '<script>alerta("Debes iniciar sesión", "index.php");</script>';
    exit;
}

$id_paciente = $_SESSION['id_paciente'];
include 'nav_superior_admin.php';

try {
    $stmt = $conexion->prepare("SELECT * FROM turno WHERE id_paciente = :id_paciente AND start > NOW() ORDER BY start ASC LIMIT 1");
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- jQuery y Moment -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

  <!-- FullCalendar v3 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/es.js"></script>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="../Css/estilos2.css">
  <style>
    #popupTurno {
      transform-origin: top left;
      animation: expandPopup 0.2s ease-out;
      font-size: 0.85rem;
      padding: 8px;
      display: inline-block;
      max-width: 90vw; /* Evita que se desborde en pantallas chicas */
    }

    @keyframes expandPopup {
      0% {
        transform: scale(0);
        opacity: 0;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }
  </style>

</head>

<body>
  <div class="modal fade show" id="pacientesModal" tabindex="-1" aria-hidden="true" style="display: block;">
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

  <!-- Popup personalizado sobre el turno clickeado -->
  <div id="popupTurno" class="card p-2 position-absolute d-none color02" style="z-index: 9999; max-width: 300px; font-size: 0.85rem;">
    <div class="card-body p-2">
      <p class="fw-bold mb-1" id="popupPaciente"></p>
      <p class="mb-1" id="popupFecha"></p>
      <p class="mb-1" id="popupMotivo"></p>
      
      <div class="d-flex justify-content-end mt-2">
        <button class="btn btn-secondary btn-sm px-2 py-0" style="font-size: 0.75rem;" onclick="cerrarPopup()">Cerrar</button>
      </div>
    </div>
  </div>

  <script>
    const modoAdmin = <?= (isset($_SESSION['modo_admin']) && $_SESSION['modo_admin']) ? 'true' : 'false' ?>;
    const esAdmin = modoAdmin === true || modoAdmin === 'true';

    moment.locale('es');

    function cerrarPopup() {
      $('#popupTurno').addClass('d-none');
    }

    $(document).on('click', function (e) {
      if (!$(e.target).closest('#popupTurno, .fc-event').length) {
        cerrarPopup();
      }
    });

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
      maxTime: "18:00:00",
      slotLabelFormat: 'H:mm',
      slotDuration: '01:00:00',
      slotHeight: 10,

      eventRender: function(event, element) {
        // Colores por tipo
        if (event.title === 'CONTROL') {
          element.css({ backgroundColor: '#3366cc', borderColor: '#3366cc' });
        } else if (event.title === 'ATENCION') {
          element.css({ backgroundColor: '#00008b', borderColor: '#00008b' });
        }

        element.find('.fc-time').text(event.nombrePaciente); // Solo nombre

        // Al hacer clic, mostrar popup cerca del evento
        element.on('click', function(e) {
          const inicio = moment(event.start).format('DD/MM/YYYY HH:mm');

          $('#popupPaciente').text(`Paciente: ${event.nombrePaciente}`);
          $('#popupFecha').text(`Turno: ${inicio}hs`);
          $('#popupMotivo').text(`Motivo: ${event.title}`);

          const offset = $(this).offset();
          const scrollTop = $(window).scrollTop();
          const popup = $('#popupTurno');

          popup.css({
            top: (offset.top - scrollTop) + 'px',
            left: offset.left + 'px'
          }).removeClass('d-none');
        });
      },

      eventAfterAllRender: function(view) {
        if ($('#btnVolver').length === 0) {
          const volverBtn = $('<button id="btnVolver" class="btn btn-warning btn-sm mx-2">Cerrar Calendario</button>');
          volverBtn.on('click', function () {
            window.location.href = 'pantalla_administrador.php';
          });
          $('.fc-left .fc-today-button').after(volverBtn);
        }
      },

      viewRender: function(view, element) {
        if (view.name === "agendaWeek") {
          const start = view.start;
          const end = view.end.clone().subtract(1, 'days');
          const desde = start.format("D");
          const hasta = end.format("D [de] MMMM [de] YYYY");
          $(".fc-center h2").text(`Desde ${desde} al ${hasta}`);
        }
      }
    });
  </script>

  <script src="../js/alerta.js"></script>
</body>
</html>
