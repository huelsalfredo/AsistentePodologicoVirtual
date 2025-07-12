<?php

include 'conexion_be.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla Administrador</title>
    <link rel="stylesheet" href="../Css/estilos.css">
    <!-- bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>

<!-- Modal Buscar Paciente -->
<div class="modal fade" id="modalBuscarPacientes" tabindex="-1" aria-labelledby="buscarPacientesLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title">Buscar Paciente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="buscarPacienteInput" class="form-control" placeholder="Buscar por nombre, apellido o DNI">
        <ul id="resultadosPacientes" class="list-group mt-3"></ul>
      </div>
    </div>
  </div>
</div>

<?php include 'nav_superior_admin.php'; ?>

<!-- Popper.js y Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>

<script>
function BuscarPacientes(onPacienteSeleccionado) {
  const modalElement = document.getElementById("modalBuscarPacientes");
  const buscarInput = document.getElementById("buscarPacienteInput");
  const resultadosList = document.getElementById("resultadosPacientes");

  const modal = new bootstrap.Modal(modalElement);
  modal.show();

  buscarInput.value = "";
  resultadosList.innerHTML = "";

  // Limpia posibles duplicados anteriores del evento
  buscarInput.removeEventListener("input", buscarInput._handler);

  buscarInput._handler = function () {
    const query = buscarInput.value.trim();

    if (query.length > 0) {
      fetch("buscarPaciente.php?query=" + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
          resultadosList.innerHTML = "";

          if (data.length > 0) {
            data.forEach(paciente => {
              const li = document.createElement("li");
              li.textContent = `${paciente.nombres} ${paciente.apellido} - ${paciente.dni} - ${paciente.email}`;
              li.className = "list-group-item list-group-item-action";
              li.dataset.id = paciente.idPaciente;

              li.addEventListener("click", () => {
                modal.hide();

                setTimeout(() => {

                  const texto = `<strong>${paciente.nombres} ${paciente.apellido}</strong><br>DNI: ${paciente.dni}<br>✉️ ${paciente.email}`;
                  const textoHTML = texto.replace(/\n/g, "<br>");

                  confirmar(textoHTML, "Confirmar selección").then(respuesta => {
                      if (respuesta) {
                          onPacienteSeleccionado(paciente.idPaciente, paciente);
                      }
                  });


                  buscarInput.value = "";
                  resultadosList.innerHTML = "";
                }, 100);
              });

              resultadosList.appendChild(li);
            });
          } else {
            resultadosList.innerHTML = '<li class="list-group-item">No se encontraron resultados</li>';
          }
        });
    } else {
      resultadosList.innerHTML = "";
    }
  };

  buscarInput.addEventListener("input", buscarInput._handler);
}

</script>

<script src="../js/confirmar.js"></script>

</body>

</html>
