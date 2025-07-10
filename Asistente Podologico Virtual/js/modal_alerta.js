
    function alerta(mensaje) {
        // Crear el contenedor del modal
        const modal = document.createElement("div");
        modal.style.position = "fixed";
        modal.style.zIndex = "1000";
        modal.style.left = "0";
        modal.style.top = "0";
        modal.style.width = "100%";
        modal.style.height = "100%";
        modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
        modal.style.display = "flex";
        modal.style.alignItems = "center";
        modal.style.justifyContent = "center";

        // Crear el contenido del modal
        const modalContent = document.createElement("div");
        modalContent.style.backgroundColor = "white";
        modalContent.style.padding = "20px";
        modalContent.style.borderRadius = "8px";
        modalContent.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.2)";
        modalContent.style.textAlign = "center";
        modalContent.style.width = "80%";
        modalContent.style.maxWidth = "300px";

        // Crear el mensaje
        const message = document.createElement("p");
        message.textContent = mensaje;

        // Crear el botón de OK
        const okButton = document.createElement("button");
        okButton.textContent = "OK";
        okButton.style.marginTop = "10px";
        okButton.style.padding = "8px 16px";
        okButton.style.border = "none";
        okButton.style.backgroundColor = "#007bff";
        okButton.style.color = "white";
        okButton.style.borderRadius = "4px";
        okButton.style.cursor = "pointer";

        // Agregar funcionalidad al botón de OK para cerrar el modal
        okButton.onclick = function () {
            document.body.removeChild(modal);
        };

        // Agregar el mensaje y el botón al contenido del modal
        modalContent.appendChild(message);
        modalContent.appendChild(okButton);

        // Agregar el contenido al modal y el modal al cuerpo del documento
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
    }
