<?php
// Iniciamos la sesión
session_start();

// Verificamos si el usuario está logueado
if (!isset($_SESSION['cedula'])) {
    header("Location: index.php");
    exit();
}

// Incluimos la conexión a la base de datos
require_once 'conexion.php';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATV - Panel Principal</title>
    <link rel="stylesheet" href="dashboard_styles.css">
</head>
<body>
    <!-- Cabecera -->
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

    <!-- Barra de navegación -->
    <nav class="main-nav">
        <ul>
            <li class="dropdown">
                <a href="#" class="nav-link">Declaraciones</a>
                <div class="dropdown-content">
                    <a href="#" id="presentar-declaracion">Presentar declaración de impuestos</a>
                    <a href="#" id="consultar-declaracion">Consultar declaración de impuestos</a>
                </div>
            </li>
            <li><a href="#" class="nav-link">Comprobantes Electrónicos</a></li>
            <li><a href="#" class="nav-link">Registro Único Tributario</a></li>
            <li><a href="#" class="nav-link">Consultas del RUT</a></li>
            <li><a href="#" class="nav-link">Bienes Inmuebles</a></li>
            <li><a href="#" class="nav-link">Mantenimiento del Perfil</a></li>
        </ul>
        <div class="user-info">
            <span>Usuario: <?php echo $_SESSION['cedula']; ?></span>
            <a href="logout.php" class="btn-logout">Salir</a>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main>
        <!-- Sección de buzón electrónico -->
        <section id="buzon-electronico" class="section-active">
            <h2>Buzón Electrónico</h2>
            <div class="declaraciones-container">
                <?php
                // Consultamos las declaraciones del usuario
                $cedula = $_SESSION['cedula'];
                $sql = "SELECT * FROM declaraciones WHERE cedula = ? ORDER BY fecha_presentacion DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $cedula);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='declaracion-card'>";
                        echo "<h3>Declaración " . $row['tipo_formulario'] . "</h3>";
                        echo "<p>Período: " . $row['periodo_mes'] . "/" . $row['periodo_año'] . "</p>";
                        echo "<p>Fecha: " . date('d/m/Y H:i', strtotime($row['fecha_presentacion'])) . "</p>";
                        echo "<p>Estado: " . $row['estado'] . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No hay declaraciones presentadas.</p>";
                }
                ?>
            </div>
        </section>

        <!-- Sección de selección de formulario -->
        <section id="seleccion-formulario" class="section-hidden">
            <h2>Presentar declaraciones de impuestos</h2>
            <h3>Seleccione el formulario y el periodo que desea presentar</h3>
            
            <div class="form-container">
                <div class="form-group">
                    <label for="tipo-formulario">Formularios:</label>
                    <select id="tipo-formulario" name="tipo-formulario">
                        <option value="">Seleccione</option>
                        <option value="101-1">101 - 1 - Declaración Jurada del Impuesto sobre la Renta - Régimen Tradicional</option>
                        <option value="103-1">103 - 1 - Declaración Jurada de Retenciones en la Fuente del 2% - Impuesto a las Utilidades(*)</option>
                        <option value="103-1">103 - 1 - Declaración Jurada de Retenciones en la Fuente del 3% por Transporte, Comunidades, etc.</option>
                        <option value="103-1">103 - 1 - Declaración Jurada de Retenciones en la Fuente por Dividendos y otras Participaciones</option>
                        <option value="103-1">103 - 1 - Declaración Jurada de Retenciones en la Fuente por Intereses</option>
                        <option value="103-1">103 - 1 - Declaración Jurada de Retenciones en la Fuente por Pensiones Voluntarias</option>
                        <option value="103-2">103 - 2 - Declaración Jurada de Retenciones en la Fuente por Remesas al Exterior</option>
                        <option value="103-2">103 - 2 - Declaración Jurada de Retenciones en la Fuente por Remesas al Exterior V2</option>
                        <option value="103-1">103 - 1 - Declaración Jurada de Retenciones en la Fuente por Remesas del Capital Moviliario-Intereses</option>
                        <option value="103-2">103 - 2 - Declaración Jurada en Retenciones en la Fuente por Salarios, Jubilaciones y otros pagos laborales</option>
                        <option value="104-1">104 - 1 - Declaración Jurada del Impuesto Genera sobre las Venras</option>
                        <option value="104-2">104 - 2 - Declaración Jurada del Impuesto al Valor Agregado</option>
                        <
                    </select>
                </div>

                <div id="periodo-container" class="form-group hidden">
                    <label>Periodo:</label>
                    <div class="periodo-inputs">
                        <select id="periodo-año" name="periodo-año">
                            <?php
                            $año_actual = date('Y');
                            for ($i = $año_actual; $i >= $año_actual - 5; $i--) {
                                echo "<option value='$i'>$i</option>";
                            }
                            ?>
                        </select>
                        <select id="periodo-mes" name="periodo-mes">
                            <?php
                            $meses = array(
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
                                4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                                7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
                                10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            );
                            foreach ($meses as $num => $nombre) {
                                $selected = ($num == date('n')) ? 'selected' : '';
                                echo "<option value='$num' $selected>$nombre</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-buttons">
                    <button id="btn-llenar-formulario" class="btn-primary" disabled>Llenar Formulario</button>
                    <button id="btn-cancelar" class="btn-secondary">Cancelar</button>
                </div>
            </div>
        </section>

        <!-- Sección de información de declaración -->
        <section id="info-declaracion" class="section-hidden">
            <div class="buttons-top">
                <button id="btn-volver-menu" class="btn-secondary">Volver al menú declaración</button>
                <button id="btn-limpiar" class="btn-secondary">Limpiar datos</button>
            </div>

            <div class="info-container">
                <h2>Información de la Declaración</h2>
                <div class="info-details">
                    <p><strong>Cédula:</strong> <span id="info-cedula"></span></p>
                    <p><strong>Tipo de Declaración:</strong> <span id="info-tipo"></span></p>
                    <p><strong>Periodo:</strong> <span id="info-periodo"></span></p>
                </div>
            </div>

            <button id="btn-continuar" class="btn-primary">Continuar</button>
        </section>

        <!-- Sección de actividades económicas -->
        <section id="actividades-economicas" class="section-hidden">
            <h2>Actividades económicas</h2>
            <p class="descripcion">
                Corresponde a las actividades económicas que realiza la persona. Para finalizar debe completar una a una todas 
                las actividades registradas, independientemente si ha tenido o no actividad, caso contrario el sistema no le 
                permitirá continuar con el resto del formulario.
            </p>

            <div class="actividades-container">
                <div class="actividad-input">
                    <input type="text" id="nueva-actividad" placeholder="Ingrese la actividad económica">
                </div>

                <div id="formulario-actividad" class="hidden">
                    <h3>Detalle de Operaciones</h3>
                    
                    <div class="form-group">
                        <h4>A. Ingresos Gravados</h4>
                        <label>Total de Ventas o Prestación de Servicios Gravados:</label>
                        <input type="number" class="moneda" id="ventas-gravadas" step="0.01" min="0">
                        
                        <div class="desglose">
                            <label>Ventas nacionales:</label>
                            <input type="number" class="moneda" id="ventas-nacionales" step="0.01" min="0">
                            
                            <label>Exportaciones:</label>
                            <input type="number" class="moneda" id="exportaciones" step="0.01" min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <h4>B. Ingresos Exentos y No Sujetas</h4>
                        <label>Ingresos Exentos:</label>
                        <input type="number" class="moneda" id="ingresos-exentos" step="0.01" min="0">
                        
                        <label>Otros Ingresos No Sujetas a IVA:</label>
                        <input type="number" class="moneda" id="ingresos-no-sujetos" step="0.01" min="0">
                    </div>

                    <div class="form-group">
                        <h4>C. Compras y Gastos Relacionados</h4>
                        <label>Total de Compras y Gastos Deducibles:</label>
                        <input type="number" class="moneda" id="compras-deducibles" step="0.01" min="0">
                        
                        <label>IVA Soportado (Crédito Fiscal):</label>
                        <input type="number" class="moneda" id="credito-fiscal" step="0.01" min="0">
                    </div>
                </div>

                <div class="actividades-buttons">
                    <button id="btn-validar-guardar" class="btn-primary">Validar y Guardar</button>
                    <button id="btn-ir-consolidado" class="btn-primary" disabled>Ir al consolidado</button>
                </div>
            </div>
        </section>

        <!-- Sección de consolidado -->
        <section id="consolidado" class="section-hidden">
            <h2>Consolidado de Operaciones</h2>
            
            <div class="consolidado-container">
                <div class="form-group">
                    <h4>A. Ingresos y Ventas</h4>
                    <label>Total de Ventas/Servicios Gravados:</label>
                    <input type="number" class="moneda" id="total-ventas-gravadas" readonly>
                    
                    <label>Ingresos Exentos o No Sujetos:</label>
                    <input type="number" class="moneda" id="total-ingresos-exentos" readonly>
                </div>

                <div class="form-group">
                    <h4>B. Compras y Gastos Relacionados</h4>
                    <label>Total de Compras y Gastos Deducibles:</label>
                    <input type="number" class="moneda" id="total-compras-deducibles" readonly>
                    
                    <label>IVA Soportado (Crédito Fiscal):</label>
                    <input type="number" class="moneda" id="total-credito-fiscal" readonly>
                </div>

                <div class="consolidado-buttons">
                    <button id="btn-volver-menu-final" class="btn-secondary">Volver menú declaración</button>
                    <button id="btn-presentar" class="btn-primary">Presentar</button>
                </div>
            </div>
        </section>

        <!-- Sección de resumen -->
        <section id="resumen-declaracion" class="section-hidden">
            <h2>Resumen de la Declaración Jurada del Impuesto al Valor Agregado</h2>
    
            <div class="resumen-container">
            <!-- El contenido se llenará dinámicamente con JavaScript -->
            </div>

            <div class="form-buttons">
            <button id="btn-imprimir" class="btn-secondary">Imprimir Declaración</button>
            <button id="btn-inicio" class="btn-primary">Inicio</button>
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

    <script src="dashboard.js"></script>
</body>
</html>