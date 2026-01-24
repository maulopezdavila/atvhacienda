// Elementos del DOM
document.addEventListener('DOMContentLoaded', function() { // Espera a que el contenido del DOM esté completamente cargado
    const loginForm = document.getElementById('login-form'); // Obtiene el formulario de inicio de sesión
    const registerForm = document.getElementById('register-form'); // Obtiene el formulario de registro
    const loginSection = document.getElementById('login-section'); // Obtiene la sección de inicio de sesión
    const registerSection = document.getElementById('register-section'); // Obtiene la sección de registro
    const createAccountLink = document.getElementById('create-account-link'); // Obtiene el enlace para crear una cuenta
    const cancelRegisterBtn = document.getElementById('cancel-register'); // Obtiene el botón para cancelar el registro
    const modal = document.getElementById('message-modal'); // Obtiene el modal para mostrar mensajes
    const modalMessage = document.getElementById('modal-message'); // Obtiene el elemento para el mensaje dentro del modal
    const closeButton = document.querySelector('.close-button'); // Obtiene el botón para cerrar el modal

    // Funciones para mostrar/ocultar el modal
    function showModal(message) { // Función para mostrar el modal con un mensaje
        modalMessage.textContent = message; // Establece el contenido del mensaje en el modal
        modal.style.display = 'block'; // Muestra el modal
    }

    function hideModal() { // Función para ocultar el modal
        modal.style.display = 'none'; // Oculta el modal
    }

    // Función para limpiar formularios
    function limpiarFormularios() {
        loginForm.reset(); // Restablece el formulario de inicio de sesión
        registerForm.reset(); // Restablece el formulario de registro
    }


    // Alternar secciones
    createAccountLink.addEventListener('click', function(e) { // Agrega un evento al enlace de crear cuenta
        e.preventDefault(); // Previene el comportamiento por defecto del enlace
        loginSection.classList.remove('active'); // Remueve la clase activa de la sección de inicio de sesión
        registerSection.classList.add('active'); // Agrega la clase activa a la sección de registro
        limpiarFormularios(); // Limpia los formularios antes de cambiar
    });

    cancelRegisterBtn.addEventListener('click', function() { // Agrega un evento al botón de cancelar registro
        registerSection.classList.remove('active'); // Remueve la clase activa de la sección de registro
        loginSection.classList.add('active'); // Agrega la clase activa a la sección de inicio de sesión
        limpiarFormularios(); // Limpia los formularios antes de cambiar
    });

    // Manejador del formulario de inicio de sesión
    loginForm.addEventListener('submit', async function(e) { // Agrega un evento al enviar el formulario de inicio de sesión
        e.preventDefault(); // Previene el comportamiento por defecto del formulario
        
        const formData = new FormData(loginForm); // Crea un objeto FormData con los datos del formulario
        
        try {
            const response = await fetch('login.php', { // Realiza una solicitud POST al archivo login.php
                method: 'POST', // Método de la solicitud
                body: formData // Cuerpo de la solicitud con los datos del formulario
            });

            const data = await response.json(); // Convierte la respuesta a formato JSON
            
            if (data.success) { // Verifica si el inicio de sesión fue exitoso
                window.location.href = data.redirect; // Redirige a la página especificada (comentada en el PHP)
            } else {
                showModal(data.message); // Muestra el mensaje de error en el modal
            }
        } catch (error) {
            showModal('Error al procesar la solicitud'); // Muestra un mensaje de error si ocurre un problema
        }
    });

    // Manejador del formulario de registro
    registerForm.addEventListener('submit', async function(e) { // Agrega un evento al enviar el formulario de registro
        e.preventDefault(); // Previene el comportamiento por defecto del formulario
        
        const formData = new FormData(registerForm); // Crea un objeto FormData con los datos del formulario
        
        try {
            const response = await fetch('registro.php', { // Realiza una solicitud POST al archivo registro.php
                method: 'POST', // Método de la solicitud
                body: formData // Cuerpo de la solicitud con los datos del formulario
            });

            const data = await response.json(); // Convierte la respuesta a formato JSON
            
            if (data.success) { // Verifica si el registro fue exitoso
                showModal(data.message); // Muestra el mensaje de éxito en el modal
                setTimeout(() => { // Establece un temporizador para ocultar el registro y mostrar el inicio de sesión
                    registerSection.classList.remove('active'); // Remueve la clase activa de la sección de registro
                    loginSection.classList.add('active'); // Agrega la clase activa a la sección de inicio de sesión
                }, 2000); // Espera 2 segundos antes de cambiar de sección
            } else {
                showModal(data.message); // Muestra el mensaje de error en el modal
            }
        } catch (error) {
            showModal('Error al procesar el registro'); // Muestra un mensaje de error si ocurre un problema
        }
    });

    // Eventos de cierre del modal
    closeButton.addEventListener('click', hideModal); // Agrega un evento al botón de cerrar para ocultar el modal
    window.addEventListener('click', function(e) { // Agrega un evento al hacer clic en la ventana
        if (e.target === modal) { // Verifica si el clic fue en el modal
            hideModal(); // Oculta el modal
        }
    });
});