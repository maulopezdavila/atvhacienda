<?php
/**
 * Manejador de Registro de Usuario
 * Procesa las solicitudes de registro de nuevos usuarios
 */

// Iniciar sesión y incluir conexión a la base de datos
session_start(); // Inicia una nueva sesión o reanuda la existente
require_once 'conexion.php'; // Incluye el archivo de conexión a la base de datos

// Manejar la solicitud POST para el registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Verifica si la solicitud es de tipo POST
    try {
        // Obtener y sanitizar los datos de entrada
        $cedula = filter_var($_POST['cedula'], FILTER_SANITIZE_STRING); // Sanitiza la cédula
        $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL); // Sanitiza el correo electrónico
        $password = $_POST['password']; // Obtiene la contraseña sin sanitizar
        $fecha = date('Y-m-d H:i:s'); // Obtiene la fecha de registro

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { // Verifica si el correo es válido
            throw new Exception("Correo electrónico inválido"); // Lanza una excepción si el correo no es válido
        }

        // Verificar si el usuario ya existe
        $stmt = $conn->prepare("SELECT cedula FROM usuarios WHERE cedula = ? OR correo = ?"); // Prepara la consulta SQL
        $stmt->bind_param("ss", $cedula, $correo); // Vincula los parámetros a la consulta
        $stmt->execute(); // Ejecuta la consulta
        $result = $stmt->get_result(); // Obtiene el resultado de la consulta

        if ($result->num_rows > 0) { // Verifica si ya existe un usuario con la misma cédula o correo
            throw new Exception("Usuario ya registrado con esta cédula o correo"); // Lanza una excepción si el usuario ya está registrado
        }

        // Hash de la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT); // Aquí se debería aplicar un hash a la contraseña (actualmente ahora si se hace)

        // Insertar nuevo usuario
        $stmt = $conn->prepare("INSERT INTO usuarios (cedula, correo, contraseña, fecha_registro) VALUES (?, ?, ?, ?)"); // Prepara la consulta SQL para insertar un nuevo usuario
        $stmt->bind_param("ssss", $cedula, $correo, $password_hash, $fecha); // Vincula los parámetros a la consulta
        
        if ($stmt->execute()) { // Ejecuta la consulta de inserción
            echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente']); // Devuelve un mensaje de éxito en formato JSON
        } else {
            throw new Exception("Error al registrar usuario"); // Lanza una excepción si hay un error al registrar
        }

    } catch (Exception $e) { // Captura cualquier excepción lanzada
        echo json_encode(['success' => false, 'message' => $e->getMessage()]); // Devuelve un mensaje de error en formato JSON
    }
    exit(); // Termina el script
}