<?php

    session_start();
    
    if (isset($_SESSION['paciente'])) {
        header("Location: pantalla_paciente.php");
        exit;
    }

    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador') {
        header("Location: pantalla_administrador.php");
        exit;
    }

    // if (isset($_SESSION['paciente'], $_SESSION['rol'])) {
    //     echo "Paciente: {$_SESSION['paciente']} y su rol es {$_SESSION['rol']}";
    // } else {
    //     echo "Sesión no iniciada correctamente - index.php";
    // }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Podológico Virtual</title>
    <link rel="icon" href="../Imagenes/Pardepies.jpg">
    
    <link rel="stylesheet" href="../Css/estilos.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .modal-custom {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-body input {
            margin-bottom: 10px;
        }
        .toggle-password-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
        }
        .modal-footer .btn-link {
            font-weight: bold;
            color: #007bff;
        }
        .position-relative {
            position: relative;
        }
        
        /* Fondo del modal */
        .form-container {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px 25px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
        }

        /* Título del formulario */
        .form-container h2 {
            font-size: 20px;
            font-weight: bold;
            color: #005a9e;
            margin-bottom: 15px;
        }

        /* Texto de instrucciones */
        .form-container p {
            margin-bottom: 15px;
            line-height: 1.5;
        }

        /* Etiquetas de los campos */
        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        /* Inputs y selects */
        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"],
        .form-container input[type="date"] {
            width: 100%;
            padding: 3px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        /* Placeholder gris claro */
        .form-container input::placeholder {
            color: #888;
        }

        /* Botón de registrarse */
        .form-container button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #0078d4;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button[type="submit"]:hover {
            background-color: #005fa3;
        }

        /* Texto inferior (inicia sesión) */
        .form-container .form-footer {
            margin-top: 10px;
            text-align: center;
            font-size: 13px;
        }

        .form-container .form-footer a {
            color: #005a9e;
            text-decoration: none;
            font-weight: 600;
        }

    </style>
</head>

<body>
    <nav class="nav-container" style="padding: 0px 80px 0px 10px;">
        <div style="display: flex; align-items: center;">
            
            <a href="#" class="nav-item">
                <img src="../Imagenes/Logo.jpg" alt="Logo" style="height:52px;  width: auto">
                <a href="#" class="nav-item">Turnero Podológico</a>
            </a>
        </div>
        <div class="nav-right">
            <a href="quienes_somos.php" class="nav-item">Quienes somos</a>  
        </div>
    </nav>

    <!-- Botón para abrir modal de logueo -->
    <script>
        window.onload = () => {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }
    </script>

    <!---------------- Modal de Logueo ---------------------->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow rounded">
        <div class="modal-header text-white" style="background-color: #007bff;">
            <h5 class="modal-title mx-auto">Inicia sesión</h5>
            <!-- <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button> -->
        </div>

        <form action="logueoPaciente.php" method="POST">
            <div class="modal-body text-center p-4">
            <img src="../Imagenes/Logo.jpg" alt="Logo" style="max-height: 50px; margin-bottom: 10px;">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
            </div>
            <div class="mb-3 position-relative">
                <input type="password" name="contrasena" class="form-control" id="loginPass" placeholder="Contraseña" required>
                <button type="button" class="toggle-password-btn" onclick="togglePassword('loginPass', this)">
                <i class="bi bi-eye-slash"></i>
                </button>
            </div>
            <div class="text-end">
                <a href="olvidoPass.php" class="text-decoration-none small">¿Olvidó su contraseña?</a>
            </div>
            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">Inicia sesión</button>
            </div>

            <div class="mt-4 text-muted small">
                <i class="bi bi-person-circle"></i> ¿Necesita ayuda? Envíe un correo a:  
                <br><strong>customer.service.esperanza@gmail.com</strong>
            </div>
            </div>

            <div class="modal-footer justify-content-center">
            <span class="small">¿No es un miembro? <a href="#" class="fw-bold text-primary" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Registrarse</a></span>
            </div>
        </form>
        </div>
    </div>
    </div>

    <!-------------------- Modal de Registro ------------------------->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #007bff;>
                    <h5 class="modal-title">Registrarse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
         
                <div class="form-container">
                    <h2>Registro</h2>
                    <p>
                        Llena el siguiente formulario y luego haz clic en el botón 'Registrarse'.
                        Recibirás un correo de confirmación registro. Los campos con * son obligatorios.
                        Cualquier problema con tu proceso de registro, por favor, contáctanos 
                        <br><strong>customer.service.esperanza@gmail.com</strong>
                    </p>
                    <form id="formulario__registrar" method="POST" action="registroPaciente.php">
                        <label for="apellido">Apellido *</label>
                        <input type="text" id="apellido" name="apellido" placeholder="Apellido" required>

                        <label for="nombre">Nombres *</label>
                        <input type="text" id="nombres" name="nombres" placeholder="Nombres" required>

                        <label for="correo">Correo electrónico *</label>
                        <input type="text" id="correo" name="correo" placeholder="Correo electrónico" required>

                        <label for="celular">Nro. de Celular *</label>
                        <input type="text" placeholder="Nro. Celular (Sin 0 y sin 15)" name = "celular" required title="Nro. de Celular">
                        
                        <label for="dni">D.N.I. *</label>
                        <input type="text" placeholder="D.N.I." name = "dni" required title="D.N.I.">
                        
                        <label for="fechaNac">Fecha de Nacimiento *</label>
                        <input type="date" placeholder="Su fecha de nacimiento" name = "fechaNac" required title="Fecha de Nacimiento">

                        <!-- Contraseña -->
                        <label for="password">Contraseña *</label>
                        <div class="mb-3 position-relative">
                            <input type="password" placeholder="Contraseña" name="contrasena" id="password" class="form-control" required minlength="6" maxlength="15">
                            <button type="button" class="toggle-password-btn" onclick="togglePassword('password', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>

                        <!-- Reingrese Contraseña -->
                        <label for="password2">Reingrese su Contraseña *</label>
                        <div class="mb-3 position-relative">
                            <input type="password" placeholder="Reingrese Contraseña" name="contrasena2" id="password2" class="form-control" required minlength="6" maxlength="15">
                            <button type="button" class="toggle-password-btn" onclick="togglePassword('password2', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>

                        <button type="submit">Registrarse</button>
                    </form>

                    <div class="form-footer">
                        ¿Ya tienes una cuenta? <a href="index.php">Inicia sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/alerta.js"></script>
</body>
</html>

<script>
    document.getElementById("formulario__registrar").addEventListener("submit", async function(event) {
        event.preventDefault();

        const dniInput = document.querySelector('input[name="dni"]');
        const emailInput = document.querySelector('input[name="correo"]');
        const celularInput = document.querySelector('input[name="celular"]');
        const pass1Input = document.getElementById("password");
        const pass2Input = document.getElementById("password2");
        const errorMessage = document.getElementById("error-message");

        const dni = dniInput.value.trim();
        const email = emailInput.value.trim();
        const celular = celularInput.value.trim();
        const pass1 = pass1Input.value;
        const pass2 = pass2Input.value;

        const dniRegex = /^\d{8}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const celularRegex = /^[1-9]\d{7,11}$/;

        // Validación de correo
        if (!emailRegex.test(email)) {
            alert("Ingrese un correo electrónico válido.");
            emailInput.focus();
            return;
        } 

        const emailExiste = await existeEnBD("email", email);
        if (emailExiste === "existe") {
            alert("El correo ya está registrado.");
            emailInput.focus();
            return;
        }

        // Validación de número de celular
        if (celular !== "" && !celularRegex.test(celular)) {
            alert("El celular debe tener entre 8 y 12 dígitos numéricos y no comenzar con 0.");     
            celularInput.focus();
            return;
        } 

        // Validación de DNI
        if (!dniRegex.test(dni)) {
            alert("El DNI debe contener exactamente 8 dígitos numéricos.");
            dniInput.focus();
            return;
        } 

        const dniExiste = await existeEnBD("dni", dni);
        if (dniExiste === "existe") {
            alert("El DNI ya está registrado.");
            dniInput.focus();
            return;
        }

        // Validación que las contraseñas sean iguales
        if (pass1 !== pass2) {
            alert("Las contraseñas no coinciden.");
            pass2Input.focus();
            return;
        }

        // Validación del largo de la contraseña
        if (pass1.length < 6 || pass1.length > 15) {
            alert("La contraseña debe tener entre 6 y 15 caracteres.");
            errorMessage.style.color = "red";
            pass1Input.focus();
            return;
        }

        // Recolectar datos del formulario
        const formData = new FormData(document.getElementById("formulario__registrar"));

        try {
            const response = await fetch("registroPaciente.php", {
                method: "POST",
                body: formData
            });

            const result = await response.text();

            if (response.ok) {
                if (result.includes("Registro exitoso")) {
                    alerta("Paciente almacenado con éxito", "index.php");
                } else {
                    alerta("No se pudo registrar el paciente. Intente nuevamente.", "index.php");
                }
            } else {
                errorMessage.innerText = "Ocurrió un error en el servidor.";
                errorMessage.style.color = "red";
            }

        } catch (error) {
            console.error("Error:", error);
            alert("No se pudo enviar el formulario. Verifique su conexión.");
        }
    });
    </script>

    <script>
        const existeEnBD = async (tipo, valor) => {
            const form = new FormData();
            form.append("tipo", tipo);
            form.append("valor", valor);

            const response = await fetch("verificarUsuario.php", {
                method: "POST",
                body: form
            });

            return await response.text();
        };
    </script>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }
        }
    </script>
    <!-- <script src="../js/alerta.js"></script> -->
</body>
</html>