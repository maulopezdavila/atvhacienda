<?php
/**
 * Manejador de Inicio de Sesión de Usuario
 * Procesa las solicitudes de inicio de sesión de usuarios y gestiona sesiones
 */

// Iniciar sesión y incluir conexión a la base de datos
session_start(); // Inicia una nueva sesión o reanuda la existente
require_once 'conexion.php'; // Incluye el archivo de conexión a la base de datos

// Manejar la solicitud POST para el inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Verifica si la solicitud es de tipo POST
    try {
        // Obtener y sanitizar los datos de entrada
        $cedula = filter_var($_POST['cedula'], FILTER_SANITIZE_STRING); // Sanitiza la cédula
        $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL); // Sanitiza el correo electrónico
        $password = $_POST['password']; // Obtiene la contraseña sin sanitizar

        // Validar la entrada
        if (empty($cedula) || empty($correo) || empty($password)) { // Verifica si algún campo está vacío
            throw new Exception("Todos los campos son requeridos"); // Lanza una excepción si falta algún campo
        }

        // Verificar las credenciales del usuario
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE cedula = ? AND correo = ?"); // Prepara la consulta SQL
        $stmt->bind_param("ss", $cedula, $correo); // Vincula los parámetros a la consulta
        $stmt->execute(); // Ejecuta la consulta
        $result = $stmt->get_result(); // Obtiene el resultado de la consulta

        if ($result->num_rows === 0) { // Verifica si no se encontraron usuarios
            throw new Exception("Usuario no encontrado"); // Lanza una excepción si no se encuentra el usuario
        }

        $usuario = $result->fetch_assoc(); // Obtiene los datos del usuario como un array asociativo

        // Verificar la contraseña (con encriptación)
        // password_verify() compara automáticamente el texto plano ($password) con el hash ($usuario['contraseña'])
        if (!password_verify($password, $usuario['contraseña'])) { 
            // Si la comparación falla (contraseña incorrecta), detener ejecución y mostrar error
            throw new Exception("Contraseña incorrecta");
            }

        // Establecer variables de sesión
        $_SESSION['cedula'] = $usuario['cedula']; // Almacena la cédula en la sesión
        $_SESSION['correo'] = $usuario['correo']; // Almacena el correo en la sesión

        echo json_encode([ // Devuelve una respuesta JSON
            'success' => true, // Indica que el inicio de sesión fue exitoso
            'message' => 'Login exitoso', // Mensaje de éxito
            'redirect' => 'dashboard.php' // Redirección opcional (comentada)
        ]);

    } catch (Exception $e) { // Captura cualquier excepción lanzada
        echo json_encode(['success' => false, 'message' => $e->getMessage()]); // Devuelve un mensaje de error en formato JSON
    }
    exit(); // Termina el script
}
