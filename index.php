<?php
//session_start();
//Redirect if already logged in
//if (isset($_SESSION['cedula'])) {
//    header("Location: dashboard.php");
//    exit();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATV - Administración Tributaria Virtual</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-left">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c6/Logo_del_Ministerio_de_Hacienda.svg/1107px-Logo_del_Ministerio_de_Hacienda.svg.png" alt="Ministerio de Hacienda">
            </div>
            <div class="logo-right">
                <img src="https://vui.cr/wp-content/uploads/2023/02/logo20hacienda-p-500-1.png" alt="ATV">
            </div>
        </div>
    </header>

    <div class="blue-stripe"></div>

    <main>
        <!-- SECCIÓN DE LOGIN -->
        <section id="login-section" class="active">
            <div class="welcome-message">
                <h1>Bienvenido(a) al portal</h1>
                <h2>Administración Tributaria Virtu@l</h2>
            </div>

            <div class="form-container">
                <form id="login-form">
                    <div class="form-group">
                        <label for="login-cedula">N° Identificación:</label>
                        <input type="text" id="login-cedula" name="cedula">
                        <span class="help-icon" title="Ingrese su número de identificación en formato X-XXXX-XXXX">?</span>
                    </div>

                    <div class="form-group">
                        <label for="login-correo">Correo:</label>
                        <input type="email" id="login-correo" name="correo" required>
                        <span class="help-icon" title="Ingrese su correo electrónico registrado">?</span>
                    </div>

                    <div class="form-group">
                        <label for="login-password">Contraseña:</label>
                        <input type="password" id="login-password" name="password" required>
                        <span class="help-icon" title="Ingrese su contraseña">?</span>
                    </div>

                    <div class="form-links">
                        <a href="#" id="create-account-link">Crear cuenta de usuario</a>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-primary">Ingresar</button>
                    </div>
                </form>
            </div>

            <div class="info-message">
                <p>Estimado usuario:</p>
                <p>El registro de factura electrónica ya está disponible en este sitio.</p>
            </div>
        </section>

        <!-- SECCIÓN DE REGISTRO -->
        <section id="register-section">
            <h1>Crear cuenta de usuario</h1>
            <p class="subtitle">Ingrese los datos del documento de identificación</p>

            <div class="form-container">
                <form id="register-form">
                    <div class="form-group">
                        <label for="register-cedula">N° Identificación:</label>
                        <input type="text" id="register-cedula" name="cedula">
                        <span class="help-icon" title="Ingrese su número de identificación en formato X-XXXX-XXXX">?</span>
                        <div class="format-hint">Formato Válido: X-XXXX-XXXX</div>
                    </div>

                    <div class="form-group">
                        <label for="register-correo">Correo:</label>
                        <input type="email" id="register-correo" name="correo" required>
                        <span class="help-icon" title="Ingrese un correo electrónico válido">?</span>
                    </div>

                    <div class="form-group">    
                        <label for="register-password">Contraseña:</label>
                        <input type="password" id="register-password" name="password" required minlength="8">
                        <span class="help-icon" title="La contraseña debe tener al menos 8 caracteres">?</span>
                    </div>

                    <div class="required-field-note">
                        <span class="required-mark">*</span> Campo requerido
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-primary">Aceptar</button>
                        <button type="button" id="cancel-register" class="btn-secondary">Cancelar</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-info">
                <p>Ministerio de Hacienda</p>
                <p>República de Costa Rica</p>
                <p>San José, Avenida 2da</p>
                <p>Calle 1 y 3, diagonal al Teatro Nacional</p>
                <p>Para información y asistencia ingrese <a href="#">aquí</a></p>
                <p>Central telefónica: 2539-4000 opción 1</p>
                <p>Horario de atención: Lunes a viernes de 8:00 a.m. a 4:00 p.m.</p>
            </div>
            <div class="social-icons">
                <a href="#" class="social-icon">F</a>
                <a href="#" class="social-icon">T</a>
                <a href="#" class="social-icon">Y</a>
                <a href="#" class="social-icon">M</a>
            </div>
        </div>
    </footer>

    <div id="message-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <p id="modal-message"></p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>