<?php

session_start();

include 'conexion_be.php';

if (isset($_GET['idPaciente'])) {
    $_SESSION['id_paciente'] = $_GET['idPaciente'];

    // Busca datos del paciente
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
          <script>alerta("Debes iniciar sesión", "index.php");</script>';
    exit;
}

$id_paciente = $_SESSION['id_paciente'];

include 'nav_superior_paciente.php';

try {
    $stmt = $conexion->prepare("SELECT * FROM turno WHERE id_paciente = :id_paciente AND start > NOW() ORDER BY start ASC LIMIT 1");
    $stmt->bindParam(':id_paciente', $id_paciente);
    $stmt->execute();
    $turnoPendiente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($turnoPendiente) {
        $fechaTurno = date("d/m/Y", strtotime($turnoPendiente['start']));
        $horaTurno = date("H:i", strtotime($turnoPendiente['start']));
        $motivo = $turnoPendiente['title'];

        echo "<script src='../js/alerta.js'></script>;
              <script>
                  window.onload = function() {
                      alerta('Ya tenés un turno reservado para el día $fechaTurno a las $horaTurno hs. Motivo: $motivo', 'pantalla_paciente.php');
                  };
              </script>";

    }
} catch (PDOException $e) {
    echo "<script>console.error('Error al verificar turnos pendientes: " . $e->getMessage() . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Calendario Paciente</title>
  <link rel="icon" href="../Imagenes/Pardepies.jpg">

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
 <!--       <div class="d-none d-lg-block col-lg"></div>-->
        <div class="col-12 color01 subir-calendario">
          <div id="CalendarioWeb"></div>
        </div>
<!--      <div class="d-none d-lg-block col-lg"></div>-->
      </div>
    </div>
  </div>

  <!-- Modal Turno -->
  <div class="modal fade" id="modalTurno" tabindex="-1" aria-labelledby="modalTurnoLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-info"> <!-- color02 -->
          <h1 class="modal-title fs-5" id="modalTurnoLabel">Modal title</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body color01" id="modalTurnoBody">
          <!-- Texto de la fecha -->
          <p id="textoTurno" class="text-center fs-4 fw-semibold"></p>
          <p> ____________________________________________________________________  </p>
          <p id="textoTurno" class="text-center fs-7">
           Para poder confirmar su turno seleccione una opcion...</p>

          <div class="form-check"> 
            <input class="form-check-input" type="checkbox" id="checkControl" value="control">
            <label class="form-check-label fw-semibold" for="checkControl" >
              Atención podológica nueva?
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="checkNuevo" value="nuevo">
            <label class="form-check-label fw-semibold" for="checkNuevo">
              Control de atención podológica anterior?
            </label>
          </div>
        </div>

        <div class="modal-footer color02">
          <button type="button" class="btn btn-primary" id="btnConfirmar" disabled>Confirmar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <script>

    moment.locale('es');
    let fechaSeleccionada = null;

    $('#CalendarioWeb').fullCalendar({
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

      events: 'php/turnosTomados.php',
      
      eventAfterAllRender: function(view) {
        if ($('#btnVolver').length === 0) {
          const volverBtn = $('<button id="btnVolver" class="btn btn-warning btn-sm mx-2">Cerrar Calendario</button>');
          volverBtn.on('click', function () {
            window.location.href = 'index.php'; 
          });

          // Insertar el botón entre "Hoy" y "Mes"
          $('.fc-left .fc-today-button').after(volverBtn);
        }
      },

      dayClick: function(date, jsEvent, view) {
        const ahora = moment();  // Captura la fecha y hora actual
        const hoy = moment().startOf('day');  // Captura el dia actual
        const limite = moment().add(2, 'hours');  // Le suma 2 horas a la fecha y hora actual
      
          // Detectar si es domingo
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

        if (date.format("HH:mm")==="00:00") {
          alerta ("Pantalla solo de lectura... Seleccione Semana o dia");
            return; // Para evitar que se haga click en Mes
        }

        fechaSeleccionada = date.clone();

        const titulo = "Ud. desea su turno para ...";
        const cuerpo = "Día  " + date.format("dddd DD/MM/YYYY") + " a las " + date.format("HH:mm") + "hs";

        $('#modalTurnoLabel').text(titulo);
        $('#textoTurno').text(cuerpo);

        var myModal = new bootstrap.Modal(document.getElementById('modalTurno'));
        myModal.show();
      },

      eventRender: function(event, element) {
        const ahora = moment();
        const inicioEvento = moment(event.start);

        // Filtra los eventos anteriores a hoy
        if (ahora.format("YYYY-MM-DD HH:mm")>inicioEvento.format("YYYY-MM-DD HH:mm")){
          return false; // No mostrar este evento
        }
          // Muestra siempre el texto "RESERVADO" en pantalla
         element.find('.fc-title').text('RESERVADO');
          // Cambiar tamaño de fuente
         element.css('font-size', '12px');
      },

      events: 'turnosTomados.php',

      viewRender: function(view, element) {
        if (view.name === "agendaWeek") {
          const start = view.start;
          const end = view.end.clone().subtract(1, 'days');
          const desde = start.format("D");
          const hasta = end.format("D [de] MMMM [de] YYYY");
          const titulo = "Desde " + desde + " al " + hasta;
          $(".fc-center h2").text(titulo);
        }
      }
    });

  // Función para verificar si hay un checkbox seleccionado
    function actualizarEstadoBoton() {
      const seleccionado = $('#checkControl').is(':checked') || $('#checkNuevo').is(':checked');
      $('#btnConfirmar').prop('disabled', !seleccionado);
    }

    // Asocia un evento de cambio a los checkbox
    $('#checkControl').on('change', function () {
      if (this.checked) {
        $('#checkNuevo').prop('checked', false);
      }
      actualizarEstadoBoton();
    });

    $('#checkNuevo').on('change', function () {
      if (this.checked) {
        $('#checkControl').prop('checked', false);
      }
      actualizarEstadoBoton();
    });

    // También resetea los checkboxes y botón cuando se abre el modal
      $('#modalTurno').on('show.bs.modal', function () {
      $('#checkControl').prop('checked', false);
      $('#checkNuevo').prop('checked', false);
      $('#btnConfirmar').prop('disabled', true);
    });

  // Evento al hacer clic en Confirmar
      $('#btnConfirmar').click(function () {
        if (!fechaSeleccionada) return;

        const start = fechaSeleccionada.format('YYYY-MM-DD HH:mm:ss');
        const end = fechaSeleccionada.clone().add(1, 'hour').format('YYYY-MM-DD HH:mm:ss');

        // Para determinar el título según el checkbox seleccionado
        let title = '';
        if ($('#checkControl').is(':checked')) {
          title = 'ATENCION';
        } else if ($('#checkNuevo').is(':checked')) {
          title = 'CONTROL';
        }

        $.ajax({
          url: 'guardarTurno.php',
          method: 'POST',
          data: {
            start: start,
            end: end,
            title: title,
          },

          success: function (response) {
            alerta('Turno guardado correctamente','pantalla_paciente.php');
            $('#CalendarioWeb').fullCalendar('refetchEvents');
            fechaSeleccionada = null;
          },
          error: function (xhr, status, error) {
            alerta('Error al guardar el turno: ' + error);
          }
        });

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalTurno'));
        modal.hide();
      });
  </script>
  <script src="../js/alerta.js"></script>
</body>
</html>
