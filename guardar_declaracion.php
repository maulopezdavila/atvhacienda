<?php
// Iniciamos la sesión
session_start();

// Verificamos si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Incluimos la conexión a la base de datos
require_once 'conexion.php';

// Obtenemos los datos enviados
$datos = json_decode(file_get_contents('php://input'), true);

try {
    // Iniciamos una transacción
    $conn->begin_transaction();

    // Insertamos la declaración
    $stmt = $conn->prepare("INSERT INTO declaraciones (cedula, tipo_formulario, periodo_año, periodo_mes, fecha_presentacion, estado) VALUES (?, ?, ?, ?, NOW(), 'PRESENTADA')");
    $stmt->bind_param("ssii", 
        $_SESSION['cedula'],
        $datos['declaracion']['tipoFormulario'],
        $datos['declaracion']['periodo']['año'],
        $datos['declaracion']['periodo']['mes']
    );
    $stmt->execute();
    $declaracionId = $conn->insert_id;

    // Insertamos las actividades económicas
    foreach ($datos['actividades'] as $actividad) {
        $stmt = $conn->prepare("INSERT INTO actividades_economicas (declaracion_id, descripcion, ventas_gravadas, ventas_exentas, ventas_no_sujetas, compras_gravadas, credito_fiscal) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddddd",
            $declaracionId,
            $actividad['descripcion'],
            $actividad['ventasGravadas'],
            $actividad['ingresosExentos'],
            $actividad['ingresosNoSujetos'],
            $actividad['comprasDeducibles'],
            $actividad['creditoFiscal']
        );
        $stmt->execute();
    }

    // Insertamos el consolidado
    $stmt = $conn->prepare("INSERT INTO consolidados (declaracion_id, total_ventas_gravadas, total_ingresos_exentos, total_compras_deducibles, total_credito_fiscal) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idddd",
        $declaracionId,
        $datos['consolidado']['totalVentasGravadas'],
        $datos['consolidado']['totalIngresosExentos'],
        $datos['consolidado']['totalComprasDeducibles'],
        $datos['consolidado']['totalCreditoFiscal']
    );
    $stmt->execute();

    // Confirmamos la transacción
    $conn->commit();

    // Devolvemos respuesta exitosa
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Declaración guardada exitosamente',
        'declaracionId' => $declaracionId
    ]);

} catch (Exception $e) {
    // Si hay error, revertimos la transacción
    $conn->rollback();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la declaración: ' . $e->getMessage()
    ]);
}

$conn->close();
?>