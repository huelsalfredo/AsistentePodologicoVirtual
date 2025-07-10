document.getElementById("btn__registrarse").addEventListener("click", registrar);
document.getElementById("btn__iniciar-sesion").addEventListener("click", iniciarSesion);
window.addEventListener("resize", anchoPagina);


// Declaracion de variables
var contenedor_loguear_registrar = document.querySelector(".contenedor__loguear-registrar")
var formulario_loguear = document.querySelector(".formulario__loguear")
var formulario_registrar = document.querySelector(".formulario__registrar")
var caja_trasera_loguear = document.querySelector(".caja__trasera-loguear")
var caja_trasera_registrar = document.querySelector(".caja__trasera-registrar")

function anchoPagina() {
    if(window.innerWidth > 850) {
        caja_trasera_loguear.style.display = "block";
        caja_trasera_registrar.style.display = "block"; }
    else {
        caja_trasera_registrar.style.display = "block";
        caja_trasera_registrar.style.opacity ="1";
        caja_trasera_loguear.style.display = "none";
        formulario_loguear.style.display = "block";
        formulario_registrar.style.display = "none";
        contenedor_loguear_registrar.style.left ="0"; }
}

anchoPagina(); 

function iniciarSesion () {
    if (window.innerWidth > 850) {
        formulario_registrar.style.display = "none";
        contenedor_loguear_registrar.style.left = "10px";
        formulario_loguear.style.display = "block";
        caja_trasera_registrar.style.opacity = "1";
        caja_trasera_loguear.style.opacity = "0";}
    else {
        formulario_registrar.style.display = "none";
        contenedor_loguear_registrar.style.left = "0px";
        formulario_loguear.style.display = "block";
        caja_trasera_registrar.style.display = "block";
        caja_trasera_loguear.style.display = "none"; 
    }
}

function registrar () {
    if(window.innerWidth > 850) {
        formulario_registrar.style.display = "block";
        contenedor_loguear_registrar.style.left = "410px";
        formulario_loguear.style.display = "none";
        caja_trasera_registrar.style.opacity = "0";
        caja_trasera_loguear.style.opacity = "1"; }
    else {
        formulario_registrar.style.display = "block";
        contenedor_loguear_registrar.style.left = "0px";
        formulario_loguear.style.display = "none";
        caja_trasera_registrar.style.display ="none"
        caja_trasera_loguear.style.display = "block";
        caja_trasera_loguear.style.opacity = "1"; }
}

/******************************************************************************* */
// Función para formatear la fecha en YYYY-MM-DD
function formatDate(date) {
    // garantiza que el día del mes esté representado con dos dígitos, añadiendo un cero al principio si es necesario
    let day = ("0" + date.getDate()).slice(-2); 
    
    //garantiza que el mes esté representado con dos dígitos, añadiendo un cero al principio si es necesario
    let month = ("0" + (date.getMonth() + 1)).slice(-2); 

    let year = date.getFullYear();  // devuelve el año
    return `${year}-${month}-${day}`; // retorna  yyyy-mm-dd
}

// Lista de días festivos (formato YYYY-MM-DD)
const holidays = [
    "2024-01-01", "2024-04-02", "2024-05-01", "2024-05-25", "2024-06-20", // Añade aquí todos los días festivos
    "2024-07-09", "2024-08-17", "2024-10-12", "2024-12-08", "2024-12-25" // Ejemplo de días festivos
];

// Establecer límites para el campo de fecha
document.addEventListener('DOMContentLoaded', (event) => {

    // Obtiene el elemento HTML cuyo id es "fecha"
    let fechaInput = document.getElementById('fecha'); 

    // Obtiene el elemento HTML cuyo id es "fecha-error"
    let fechaError = document.getElementById('fecha-error');  

    // "today" contendrá la fecha y hora actuales del sistema en el momento de su creación.
    let today = new Date(); 

    // "minDate" contendrá los minutos de la hora actual del sistema en el momento de su creación.
    let minDate = new Date(today);
    
    // aumenta la fecha mínima en un día.
    minDate.setDate(minDate.getDate() + 1);
    
    // contendrá la misma fecha y hora que "today"
    let maxDate = new Date(today);
    
    // aumente la fecha máxima a 3 meses desde "today"
    maxDate.setMonth(maxDate.getMonth() + 3);

    // fija la fecha mínima permitida en el campo de entrada de fecha
    fechaInput.min = formatDate(minDate); 
    
    // fija la fecha máxima permitida en el campo de entrada de fecha
    fechaInput.max = formatDate(maxDate); 

    // Deshabilitar días no permitidos (s+abados, domingos y festivos)
    fechaInput.addEventListener('input', function() {
        let selectedDate = new Date(this.value + 'T00:00:00'); // Asegurar que la hora sea a medianoche UTC
        let dayOfWeek = selectedDate.getUTCDay(); // 0 (domingo) - 6 (sábado)
        let formattedDate = formatDate(selectedDate); // proporciona una representación legible de la fecha

        if (dayOfWeek === 0 ) {
            fechaError.textContent = "La fecha seleccionada es domingo. Por favor, elige otro día.";
            fechaError.style.display = "block";
            this.setCustomValidity("La fecha seleccionada es domingo.");
        } else if (dayOfWeek === 6) {
                    fechaError.textContent = "La fecha seleccionada es sábado. Por favor, elige otro día.";
                    fechaError.style.display = "block";
                    this.setCustomValidity("La fecha seleccionada es sábado.");}
                else if (holidays.includes(formattedDate)) {
                        fechaError.textContent = "La fecha seleccionada es un día feriado. Por favor, elige otro día.";
                        fechaError.style.display = "block";
                        this.setCustomValidity("La fecha seleccionada es un día feriado.");
                    } else {
                        fechaError.style.display = "none";
                        this.setCustomValidity("");
                        }
    });
});

// Manejar el envío del formulario
document.getElementById('turnoForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar el envío normal del formulario

    // Obtiene el valor de "fecha" y se asigna a fecha
    let fecha = document.getElementById('fecha').value;  

    // Obtiene el valor de "hora" y se asigna a hora
    let hora = document.getElementById('hora').value;  

    // Redirigir a login.html con los parámetros de fecha y hora
    window.location.href = `login.html?fecha=${fecha}&hora=${hora}`;
});

//*************************************************************** */
function SiPacienteLogueado (urlRedireccion = null) {
if (!isset($_SESSION['id_paciente'])) {
    alerta("Debes iniciar sesión", urlRedireccion);
    exit;
}}