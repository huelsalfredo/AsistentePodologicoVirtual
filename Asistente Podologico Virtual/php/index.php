<?php

    session_start();
    if(isset($_SESSION['paciente'])){        
        header ("location: pantalla_paciente.php");
        
 //   } else if (!isset($_SESSION['paciente']) || $_SESSION['rol'] !== 'administrador')  {
 //       header ("location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Imagenes/Pardepies.jpg">
    <title>Asistente Podológico Virtual</title>
    <link rel="stylesheet" href="../Css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>

<body>
    <nav class="nav-container">
        <div>
           <a href="#" class="nav-item">Turnero Podológico</a>
        </div>
        <div class="nav-right">
           <a href="quienes_somos.php" class="nav-item">Quienes somos</a>  
        </div>
    </nav> 

    <main>
        <div class="contenedor__todo">
            
            <div class="caja__trasera">
                <div class="caja__trasera-loguear">
                    <h3>¿Ya tienen una cuenta?</h3>
                    <p>Inicia sesion para sacar tu turno</p>
                    <button id="btn__iniciar-sesion">Iniciar sesión</button>
                </div>
                <div class="caja__trasera-registrar">                
                    <h3>¿Aún no tiene una cuenta?</h3>
                    <p>Registrate para que puedas iniciar sesión</p>
                    <button id="btn__registrarse">Registrarse</button>
                </div>
            </div>
            <!---------- Formularios de logueo y registro ------------>
            <div class="contenedor__loguear-registrar">

                <!---------- Formulario de logueo ------------>
                <form action="logueo_paciente_be.php" method="POST" class="formulario__loguear">
                    <h2>Iniciar sesión</h2>
                    <input type="text" placeholder="Correo Electrónico" name = "email" required>

                    <div class="input-contenedor-pass" style="display: flex; align-items: center;">
                        <input type="password" placeholder="Contraseña" name="contrasena" id="password1" required minlength="6" maxlength="15" style="flex: 1; height: 35px;">
                        <button type="button" onclick="togglePassword('password1', this)" style="background: white; border: 1px solid #ccc; border-left: none; color: #007bff; height: 30px; margin-top: 10px; padding: 0 10px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>

                    <div style="text-center; margin-bottom: -10px;">
                        <a href="olvidoPass.php" style="font-size: 0.9em; color: #0066cc; text-decoration: underline;">¿Olvidó su contraseña?</a>
                    </div>
                    <button type="submit">Ingresar</button>
                </form>

                <!---------- Formulario de registro ------------>
                <form action="registro_paciente_be.php" id="formulario__registrar" method = "POST" class="formulario__registrar">
                    <h2>Registrarse</h2>
                    <input type="text" placeholder="Apellido" name = "apellido" required>
                    <input type="text" placeholder="Nombre completo" name = "nombres" required>                   
                    <input type="text" placeholder="Correo Electrónico" name = "email" required>
                    <input type="text" placeholder="Nro. Celular (Sin 0 y sin 15)" name = "celular">
                    <input type="text" placeholder="D.N.I." name = "dni" required>
                    <input type="date" placeholder="Su fecha de nacimiento" name = "fechaNac" required>

                    <div class="input-contenedor-pass" style="display: flex; align-items: center;">
                        <input type="password" placeholder="Contraseña" name="contrasena" id="password" required minlength="6" maxlength="15" style="flex: 1; height: 35px;">
                        <button type="button" onclick="togglePassword('password', this)" style="background: white; border: 1px solid #ccc; border-left: none; color: #007bff; height: 30px; margin-top: 10px; padding: 0 10px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>

                    <div class="input-contenedor-pass" style="display: flex; align-items: center;">
                        <input type="password" placeholder="Reingrese Contraseña" name="contrasena2" id="password2" required minlength="6" maxlength="15" style="flex: 1; height: 35px;">
                        <button type="button" onclick="togglePassword('password2', this)" style="background: white; border: 1px solid #ccc; border-left: none; color: #007bff; height: 30px; margin-top: 10px; padding: 0 10px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                    
                    <script>
                        function togglePassword(id) {
                            const passwordInput = document.getElementById(id);
                            if (passwordInput.type === "password") {
                                passwordInput.type = "text";
                            } else {
                                passwordInput.type = "password";
                            }
                        }
                    </script>
                    <div class="error-message" id="error-message">_</div>

                    <button type="submit">Registrarse</button>  
                </form>
            </div>
        </div>
    </main>    

    <script src="../js/scripts.js"></script>

    <script>
        document.getElementById("formulario__registrar").addEventListener("submit", function(event) {
            var pass = document.getElementById("password").value;
            var pass2 = document.getElementById("password2").value;
            var errorMessage = document.getElementById("error-message");
            if (pass !== pass2) {
                event.preventDefault(); // Evitar el envío normal del formulario
                errorMessage.innerText = "Las contraseñas no coinciden.";
                errorMessage.style.color = "red";
            } else {
                errorMessage.innerText = "_";
                errorMessage.style.color = "white"; 
            }     
        });
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

</body>
</html>