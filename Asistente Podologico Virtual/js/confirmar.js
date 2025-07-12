function confirmar(mensaje, titulo = "¿Estás seguro?") {
    return new Promise((resolve) => {
        // Evita duplicados
        if (document.getElementById("confirmarModal")) return;

        // Fondo modal
        const modal = document.createElement("div");
        modal.id = "confirmarModal";
        modal.style.position = "fixed";
        modal.style.top = "0";
        modal.style.left = "0";
        modal.style.width = "100%";
        modal.style.height = "100%";
        modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
        modal.style.display = "flex";
        modal.style.alignItems = "center";
        modal.style.justifyContent = "center";
        modal.style.zIndex = "99999";
        modal.style.backdropFilter = "blur(1px)";

        // Contenido modal
        const modalContent = document.createElement("div");
        modalContent.style.backgroundColor = "#fff";
        modalContent.style.padding = "20px";
        modalContent.style.borderRadius = "8px";
        modalContent.style.width = "320px";
        modalContent.style.textAlign = "center";
        modalContent.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.2)";

        // Título
        const tituloElem = document.createElement("h5");
        tituloElem.textContent = titulo;
        tituloElem.style.marginBottom = "10px";

        // Mensaje con saltos de línea
        const message = document.createElement("p");
        message.innerHTML = mensaje.replace(/\n/g, "<br>");

        // Botón Aceptar
        const btnAceptar = document.createElement("button");
        btnAceptar.textContent = "Aceptar";
        btnAceptar.style.margin = "10px";
        btnAceptar.style.padding = "5px 20px";
        btnAceptar.style.border = "none";
        btnAceptar.style.backgroundColor = "#28a745";
        btnAceptar.style.color = "white";
        btnAceptar.style.borderRadius = "8px";
        btnAceptar.style.cursor = "pointer";

        // Botón Cancelar
        const btnCancelar = document.createElement("button");
        btnCancelar.textContent = "Cancelar";
        btnCancelar.style.margin = "10px";
        btnCancelar.style.padding = "5px 20px";
        btnCancelar.style.border = "none";
        btnCancelar.style.backgroundColor = "#dc3545";
        btnCancelar.style.color = "white";
        btnCancelar.style.borderRadius = "8px";
        btnCancelar.style.cursor = "pointer";

        // Eventos
        btnAceptar.onclick = () => {
            document.body.removeChild(modal);
            resolve(true);
        };

        btnCancelar.onclick = () => {
            document.body.removeChild(modal);
            resolve(false);
        };

        // Armar modal
        modalContent.appendChild(tituloElem);
        modalContent.appendChild(message);
        modalContent.appendChild(btnAceptar);
        modalContent.appendChild(btnCancelar);

        modal.appendChild(modalContent);
        document.body.appendChild(modal);
    });
}
