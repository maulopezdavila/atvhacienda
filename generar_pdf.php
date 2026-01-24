<?php
// Incluimos la librería FPDF
require('fpdf/fpdf.php');
require_once 'conexion.php';

// Verificamos si se recibió el ID de la declaración
if (!isset($_GET['id'])) {
    die('ID de declaración no proporcionado');
}

$declaracionId = $_GET['id'];

// Creamos una clase personalizada que hereda de FPDF
class DeclaracionPDF extends FPDF {
    // Función para el encabezado del PDF
    function Header() {

        
        // Título del documento
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Ministerio de Hacienda', 0, 1, 'C');
        $this->Cell(0, 10, 'Declaración Jurada del Impuesto al Valor Agregado', 0, 1, 'C');
        
        // Línea separadora
        $this->Line(10, 40, 200, 40);
        $this->Ln(10);
    }

    // Función para el pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

try {
    // Obtenemos los datos de la declaración
    $stmt = $conn->prepare("
        SELECT d.*, c.* 
        FROM declaraciones d 
        LEFT JOIN consolidados c ON d.id = c.declaracion_id 
        WHERE d.id = ?
    ");
    $stmt->bind_param("i", $declaracionId);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $declaracion = $resultado->fetch_assoc();

    // Creamos el PDF
    $pdf = new DeclaracionPDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Información general
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Información General', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Cédula: ' . $declaracion['cedula'], 0, 1);
    $pdf->Cell(0, 10, 'Periodo: ' . $declaracion['periodo_mes'] . '/' . $declaracion['periodo_año'], 0, 1);
    $pdf->Cell(0, 10, 'Fecha de Presentación: ' . $declaracion['fecha_presentacion'], 0, 1);
    $pdf->Ln(10);

    // Actividades económicas
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Actividades Económicas', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    $stmt = $conn->prepare("SELECT * FROM actividades_economicas WHERE declaracion_id = ?");
    $stmt->bind_param("i", $declaracionId);
    $stmt->execute();
    $actividades = $stmt->get_result();

    while ($actividad = $actividades->fetch_assoc()) {
        $pdf->Cell(0, 10, 'Descripción: ' . $actividad['descripcion'], 0, 1);
        $pdf->Cell(0, 10, 'Ventas Gravadas: ' . number_format($actividad['ventas_gravadas'], 2), 0, 1);
        $pdf->Cell(0, 10, 'Ventas Exentas: ' . number_format($actividad['ventas_exentas'], 2), 0, 1);
        $pdf->Cell(0, 10, 'Compras Gravadas: ' . number_format($actividad['compras_gravadas'], 2), 0, 1);
        $pdf->Ln(5);
    }

    // Consolidado
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Consolidado', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Total Ventas Gravadas: ' . number_format($declaracion['total_ventas_gravadas'], 2), 0, 1);
    $pdf->Cell(0, 10, 'Total Ingresos Exentos: ' . number_format($declaracion['total_ingresos_exentos'], 2), 0, 1);
    $pdf->Cell(0, 10, 'Total Compras Deducibles: ' . number_format($declaracion['total_compras_deducibles'], 2), 0, 1);
    $pdf->Cell(0, 10, 'Total Crédito Fiscal: ' . number_format($declaracion['total_credito_fiscal'], 2), 0, 1);

    // Generamos el nombre del archivo
    $nombreArchivo = 'Declaracion_' . $declaracionId . '_' . date('Y-m-d') . '.pdf';

    // Enviamos el PDF al navegador
    $pdf->Output('D', $nombreArchivo);

} catch (Exception $e) {
    die('Error al generar el PDF: ' . $e->getMessage());
}
?>