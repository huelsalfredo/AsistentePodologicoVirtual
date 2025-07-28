function alerta(mensaje, urlRedireccion = null) {
    // Verifica si ya existe un modal en la página para evitar duplicados
    if (document.getElementById("alertaModal")) return;

    // Crear el fondo del modal
    const modal = document.createElement("div");
    modal.id = "alertaModal"; // Asignar un ID para poder identificar el modal
    modal.style.position = "fixed"; // Posicionamiento fijo en la pantalla
    modal.style.top = "0"; // Ajustar el fondo en la parte superior
    modal.style.left = "0"; // Ajustar el fondo a la izquierda
    modal.style.width = "100%"; // Ancho completo
    modal.style.height = "100%"; // Alto completo
    modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)"; // Fondo semitransparente
    modal.style.display = "flex"; // Usar flexbox para centrar contenido
    modal.style.alignItems = "center"; // Centrar verticalmente
    modal.style.justifyContent = "center"; // Centrar horizontalmente
    modal.style.zIndex = "99999"; // Asegurarse de que el modal esté por encima de otros elementos
    modal.style.backdropFilter = "blur(1px)"; // para un desenfoque sutil

    // Crear el contenido del modal
    const modalContent = document.createElement("div");
    modalContent.style.backgroundColor = "#fff"; // Fondo blanco para el contenido
    modalContent.style.padding = "20px"; // Espaciado interno
    modalContent.style.borderRadius = "8px"; // Bordes redondeados
    modalContent.style.width = "300px"; // Ancho fijo
    modalContent.style.textAlign = "center"; // Centrar texto
    modalContent.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.2)"; // Sombra para dar profundidad

    // Crear el mensaje dentro del modal
    const message = document.createElement("p");
    message.innerHTML = mensaje; // Establecer el texto del mensaje

    // Crear el botón "OK"
    const okButton = document.createElement("button");
    okButton.textContent = "Aceptar"; // Texto del botón
    okButton.style.fontSize = "13px";
    okButton.style.marginTop = "10px"; // Espaciado superior
    okButton.style.padding = "5px 30px"; // Espaciado interno del botón
    okButton.style.border = "none"; // Sin borde
    okButton.style.backgroundColor = "#007bff"; // Color de fondo azul
    okButton.style.color = "white"; // Texto blanco
    okButton.style.borderRadius = "12px"; // Bordes redondeados
    okButton.style.cursor = "pointer"; // Cambiar el cursor al pasar por encima

    // Evento para cerrar el modal al hacer clic en "OK"
    okButton.onclick = function() {
        document.body.removeChild(modal); // Eliminar el modal del DOM
        // Redirige si se proporciona una URL
        if (urlRedireccion) {
            window.location.href = urlRedireccion; // Redirigir a la URL especificada
        }
    };

    // Añadir el mensaje y el botón al contenido del modal
    modalContent.appendChild(message); // Añadir el mensaje al contenido
    modalContent.appendChild(okButton); // Añadir el botón al contenido

    // Añadir el contenido al modal y el modal al documento
    modal.appendChild(modalContent); // Añadir el contenido al modal
    document.body.appendChild(modal); // Añadir el modal al cuerpo del documento
}
