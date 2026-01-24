// Declaración de variables globales para almacenar datos
let actividadesEconomicas = []; // Array para almacenar las actividades económicas ingresadas por el usuario
let datosDeclaracion = {        // Objeto para almacenar la información de la declaración
    cedula: '',                // Cédula del usuario (inicialmente vacía)
    tipoFormulario: '',        // Tipo de formulario seleccionado
    periodo: {                 // Objeto para almacenar el periodo de la declaración
        año: '',               // Año del periodo
        mes: ''                // Mes del periodo
    }
};

// Sección para obtener y manejar los elementos del DOM cuando el documento esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Se obtienen las referencias a los distintos contenedores o secciones de la aplicación
    const sections = {
        buzon: document.getElementById('buzon-electronico'),        // Sección del buzón electrónico
        seleccion: document.getElementById('seleccion-formulario'),   // Sección para seleccionar el formulario
        info: document.getElementById('info-declaracion'),            // Sección para mostrar la información de la declaración
        actividades: document.getElementById('actividades-economicas'), // Sección para agregar actividades económicas
        consolidado: document.getElementById('consolidado'),          // Sección para mostrar el consolidado de la declaración
        resumen: document.getElementById('resumen-declaracion')       // Sección para el resumen final de la declaración
    };

    // Se obtienen las referencias a los botones de navegación principal
    const presentarDeclaracionBtn = document.getElementById('presentar-declaracion'); // Botón para presentar la declaración
    const consultarDeclaracionBtn = document.getElementById('consultar-declaracion'); // Botón para consultar declaraciones en el buzón

    // Elementos para la selección del formulario
    const tipoFormularioSelect = document.getElementById('tipo-formulario'); // Selector para elegir el tipo de formulario
    const periodoContainer = document.getElementById('periodo-container');   // Contenedor que muestra los campos de periodo (año y mes)
    const btnLlenarFormulario = document.getElementById('btn-llenar-formulario'); // Botón para llenar el formulario
    const btnCancelar = document.getElementById('btn-cancelar');             // Botón para cancelar la acción y volver al buzón

    // Elementos para la sección de información de declaración
    const btnVolverMenu = document.getElementById('btn-volver-menu'); // Botón para volver al menú de selección
    const btnLimpiar = document.getElementById('btn-limpiar');       // Botón para limpiar la información ingresada
    const btnContinuar = document.getElementById('btn-continuar');   // Botón para continuar a la sección de actividades económicas

    // Elementos de la sección de actividades económicas
    const nuevaActividadInput = document.getElementById('nueva-actividad'); // Input para ingresar una nueva actividad
    const formularioActividad = document.getElementById('formulario-actividad'); // Formulario para completar detalles de la actividad
    const btnValidarGuardar = document.getElementById('btn-validar-guardar');   // Botón para validar y guardar la actividad ingresada
    const btnIrConsolidado = document.getElementById('btn-ir-consolidado');       // Botón para pasar a la sección de consolidado

    // Elementos de la sección de consolidado
    const btnVolverMenuFinal = document.getElementById('btn-volver-menu-final'); // Botón para volver al menú principal desde el consolidado
    const btnPresentar = document.getElementById('btn-presentar');               // Botón para presentar la declaración final

    // Elementos de la sección de resumen final
    const btnInicio = document.getElementById('btn-inicio'); // Botón para regresar al inicio (buzón)

    const btnImprimir = document.getElementById('btn-imprimir');
    if (btnImprimir) {
        btnImprimir.addEventListener('click', function() {
            // Obtenemos el ID de la declaración de la respuesta del servidor
            const declaracionId = window.declaracionId; // Este valor se establece cuando se guarda la declaración
            if (declaracionId) {
                // Abrimos una nueva ventana para descargar el PDF
                window.open(`generar_pdf.php?id=${declaracionId}`, '_blank');
            }
        });
    }

    // Funciones de utilidad
    // Función para mostrar una sección y ocultar las demás
    function mostrarSeccion(seccionId) {
        // Itera sobre todas las secciones y las oculta
        Object.values(sections).forEach(section => {
            section.classList.add('section-hidden');      // Agrega clase para ocultar la sección
            section.classList.remove('section-active');   // Remueve clase activa
        });
        // Muestra la sección solicitada
        sections[seccionId].classList.remove('section-hidden');
        sections[seccionId].classList.add('section-active');
    }

    // Función para formatear un valor numérico a formato de moneda (código de Costa Rica)
    function formatoMoneda(valor) {
        return new Intl.NumberFormat('es-CR', {
            style: 'currency',
            currency: 'CRC'
        }).format(valor);
    }

    // EVENT LISTENERS PARA LA INTERACCIÓN DEL USUARIO

    // Evento para el botón de "presentar declaración" que muestra la sección de selección de formulario
    presentarDeclaracionBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Previene la acción por defecto del botón
        mostrarSeccion('seleccion'); // Muestra la sección de selección de formulario
    });

    // Evento para el botón de "consultar declaración" que muestra el buzón electrónico
consultarDeclaracionBtn.addEventListener('click', function(e) {
    e.preventDefault();
    mostrarSeccion('buzon');

    //Insertamos el mensaje de "próximamente"
    const mensajeProximamente = document.createElement('div');
    mensajeProximamente.textContent = 'Esta funcionalidad estará disponible próximamente.';
    mensajeProximamente.style.backgroundColor = '#fffae6';
    mensajeProximamente.style.border = '1px solid #ffd700';
    mensajeProximamente.style.padding = '10px';
    mensajeProximamente.style.marginTop = '10px';
    mensajeProximamente.style.color = '#665c00';
    mensajeProximamente.style.fontWeight = 'bold';
    mensajeProximamente.style.textAlign = 'center';

    const buzon = document.getElementById('buzon-electronico');
    
    // Si ya existe un mensaje anterior, eliminarlo
    const mensajeAnterior = buzon.querySelector('.mensaje-proximamente');
    if (mensajeAnterior) mensajeAnterior.remove();

    mensajeProximamente.classList.add('mensaje-proximamente');
    buzon.prepend(mensajeProximamente);

    // Opcional: Elimina el mensaje después de 5 segundos
    setTimeout(() => {
        mensajeProximamente.remove();
    }, 5000);
});


tipoFormularioSelect.addEventListener('change', function () {
    const formularioSeleccionado = this.value;

    if (formularioSeleccionado === '104-2') {
        // Permitir continuar normalmente
        periodoContainer.classList.remove('hidden');
        btnLlenarFormulario.disabled = false;
    } else if (formularioSeleccionado === '') {
        // Sin selección: ocultar
        periodoContainer.classList.add('hidden');
        btnLlenarFormulario.disabled = true;
    } else {
        // Mostrar mensaje de próximamente y bloquear
        periodoContainer.classList.add('hidden');
        btnLlenarFormulario.disabled = true;

        const mensaje = document.createElement('div');
        mensaje.textContent = 'Este formulario estará disponible próximamente.';
        mensaje.style.backgroundColor = '#fff3cd';
        mensaje.style.color = '#856404';
        mensaje.style.border = '1px solid #ffeeba';
        mensaje.style.padding = '10px';
        mensaje.style.marginTop = '10px';
        mensaje.style.textAlign = 'center';
        mensaje.style.borderRadius = '4px';

        // Insertamos el mensaje justo después del select
        const parent = this.parentElement;
        const mensajePrevio = parent.querySelector('.mensaje-proximamente');
        if (mensajePrevio) mensajePrevio.remove();

        mensaje.classList.add('mensaje-proximamente');
        parent.appendChild(mensaje);

        setTimeout(() => {
            mensaje.remove();
        }, 4000);
    }
});

    // Evento para el botón de "llenar formulario"
    btnLlenarFormulario.addEventListener('click', function() {
        // Guarda los datos ingresados en el objeto datosDeclaracion
        datosDeclaracion = {
            // Extrae la cédula del contenido de un span con la clase 'user-info'
            cedula: document.querySelector('.user-info span').textContent.split(': ')[1],
            tipoFormulario: tipoFormularioSelect.value, // Guarda el tipo de formulario seleccionado
            periodo: {
                año: document.getElementById('periodo-año').value, // Obtiene el año ingresado
                mes: document.getElementById('periodo-mes').value  // Obtiene el mes ingresado
            }
        };

        // Actualiza la información mostrada en la siguiente sección con los datos ingresados
        document.getElementById('info-cedula').textContent = datosDeclaracion.cedula;
        document.getElementById('info-tipo').textContent = '104 - 2 - Declaración Jurada del Impuesto al Valor Agregado';
        document.getElementById('info-periodo').textContent = `${datosDeclaracion.periodo.mes}/${datosDeclaracion.periodo.año}`;

        // Muestra la sección de información de declaración
        mostrarSeccion('info');
    });

    // Evento para el botón "cancelar" que regresa al buzón
    btnCancelar.addEventListener('click', function() {
        mostrarSeccion('buzon');
    });

    // Manejo de la información de la declaración
    // Evento para el botón "volver al menú" que regresa a la selección del formulario
    btnVolverMenu.addEventListener('click', function() {
        mostrarSeccion('seleccion');
    });

    // Evento para el botón "limpiar" que resetea los datos ingresados y regresa a la selección
    btnLimpiar.addEventListener('click', function() {
        actividadesEconomicas = []; // Reinicia el array de actividades económicas
        datosDeclaracion = {        // Reinicia el objeto de declaración
            cedula: '',
            tipoFormulario: '',
            periodo: { año: '', mes: '' }
        };
        tipoFormularioSelect.value = '';              // Resetea el selector del formulario
        periodoContainer.classList.add('hidden');       // Oculta el contenedor de periodo
        btnLlenarFormulario.disabled = true;            // Deshabilita el botón de llenar formulario
        mostrarSeccion('seleccion');                    // Muestra la sección de selección
    });

    // Evento para el botón "continuar" que avanza a la sección de actividades económicas
    btnContinuar.addEventListener('click', function() {
        mostrarSeccion('actividades');
    });

    // Manejo de las actividades económicas
    // Evento para detectar cuando se presiona la tecla "Enter" en el input de nueva actividad
    nuevaActividadInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && this.value.trim()) { // Si se presiona Enter y el input no está vacío
            formularioActividad.classList.remove('hidden'); // Muestra el formulario para ingresar detalles de la actividad
            this.disabled = true; // Deshabilita el input para evitar modificar mientras se llena el formulario
        }
    });

    // Evento para el botón "validar y guardar" que guarda la actividad ingresada
    btnValidarGuardar.addEventListener('click', function() {
        // Crea un objeto con la información de la actividad ingresada y convierte a número cada valor
        const actividad = {
            descripcion: nuevaActividadInput.value,
            ventasGravadas: parseFloat(document.getElementById('ventas-gravadas').value) || 0,
            ventasNacionales: parseFloat(document.getElementById('ventas-nacionales').value) || 0,
            exportaciones: parseFloat(document.getElementById('exportaciones').value) || 0,
            ingresosExentos: parseFloat(document.getElementById('ingresos-exentos').value) || 0,
            ingresosNoSujetos: parseFloat(document.getElementById('ingresos-no-sujetos').value) || 0,
            comprasDeducibles: parseFloat(document.getElementById('compras-deducibles').value) || 0,
            creditoFiscal: parseFloat(document.getElementById('credito-fiscal').value) || 0
        };

        actividadesEconomicas.push(actividad); // Agrega la actividad al array global

        // Limpia el input y el formulario para permitir agregar una nueva actividad
        nuevaActividadInput.value = '';
        nuevaActividadInput.disabled = false; // Habilita nuevamente el input de actividad
        formularioActividad.classList.add('hidden'); // Oculta el formulario de actividad
        
        // Limpia los valores de todos los inputs dentro del formulario de actividad
        document.querySelectorAll('#formulario-actividad input').forEach(input => input.value = '');
        
        btnIrConsolidado.disabled = false; // Habilita el botón para ir al consolidado
    });

    // Evento para el botón "ir a consolidado" que calcula totales y muestra la sección de consolidado
    btnIrConsolidado.addEventListener('click', function() {
        // Se utiliza reduce para sumar los totales de las actividades ingresadas
        const totales = actividadesEconomicas.reduce((acc, act) => {
            return {
                ventasGravadas: acc.ventasGravadas + act.ventasGravadas, // Suma de ventas gravadas
                ingresosExentos: acc.ingresosExentos + act.ingresosExentos + act.ingresosNoSujetos, // Suma de ingresos exentos y no sujetos
                comprasDeducibles: acc.comprasDeducibles + act.comprasDeducibles, // Suma de compras deducibles
                creditoFiscal: acc.creditoFiscal + act.creditoFiscal // Suma de crédito fiscal
            };
        }, {
            ventasGravadas: 0,
            ingresosExentos: 0,
            comprasDeducibles: 0,
            creditoFiscal: 0
        });

        // Actualiza los campos del consolidado con los totales calculados
        document.getElementById('total-ventas-gravadas').value = totales.ventasGravadas;
        document.getElementById('total-ingresos-exentos').value = totales.ingresosExentos;
        document.getElementById('total-compras-deducibles').value = totales.comprasDeducibles;
        document.getElementById('total-credito-fiscal').value = totales.creditoFiscal;

        // Muestra la sección de consolidado
        mostrarSeccion('consolidado');
    });

    // Manejo de la sección de consolidado
    // Evento para el botón "volver al menú final" que regresa a la selección del formulario
    btnVolverMenuFinal.addEventListener('click', function() {
        mostrarSeccion('seleccion');
    });

    // Evento para el botón "presentar" que envía los datos al servidor
    btnPresentar.addEventListener('click', function() {

        
        // Crea un objeto con todos los datos a enviar al servidor
        const datos = {
            declaracion: datosDeclaracion, // Datos de la declaración (cédula, tipo y periodo)
            actividades: actividadesEconomicas, // Array de actividades económicas ingresadas
            consolidado: {
                totalVentasGravadas: parseFloat(document.getElementById('total-ventas-gravadas').value),
                totalIngresosExentos: parseFloat(document.getElementById('total-ingresos-exentos').value),
                totalComprasDeducibles: parseFloat(document.getElementById('total-compras-deducibles').value),
                totalCreditoFiscal: parseFloat(document.getElementById('total-credito-fiscal').value)
            }
        };
        
        // Envío de datos al servidor utilizando fetch
        fetch('guardar_declaracion.php', { // URL del archivo PHP que procesa la declaración
            method: 'POST', // Método POST para enviar datos
            headers: {
                'Content-Type': 'application/json' // Indica que el cuerpo del mensaje está en formato JSON
            },
            body: JSON.stringify(datos) // Convierte el objeto datos a una cadena JSON
        })
        .then(response => response.json()) // Convierte la respuesta del servidor a objeto JSON
        .then(data => {
            // Si la respuesta indica éxito
            if (data.success) {
                window.declaracionId = data.declaracionId;
                mostrarSeccion('resumen'); // Muestra la sección de resumen de la declaración
                // Actualiza el contenedor de resumen con la información de la declaración
                const resumenContainer = document.querySelector('.resumen-container');
                resumenContainer.innerHTML = `
                    <div class="resumen-info">
                        <h3>Detalles de la Declaración</h3>
                        <p><strong>Cédula:</strong> ${datosDeclaracion.cedula}</p>
                        <p><strong>Periodo:</strong> ${datosDeclaracion.periodo.mes}/${datosDeclaracion.periodo.año}</p>
                        <p><strong>Tipo:</strong> Declaración Jurada del Impuesto al Valor Agregado</p>
                        
                        <h4>Totales</h4>
                        <p><strong>Total Ventas Gravadas:</strong> ${formatoMoneda(datos.consolidado.totalVentasGravadas)}</p>
                        <p><strong>Total Ingresos Exentos:</strong> ${formatoMoneda(datos.consolidado.totalIngresosExentos)}</p>
                        <p><strong>Total Compras Deducibles:</strong> ${formatoMoneda(datos.consolidado.totalComprasDeducibles)}</p>
                        <p><strong>Total Crédito Fiscal:</strong> ${formatoMoneda(datos.consolidado.totalCreditoFiscal)}</p>
                    </div>
                `;
            }
        });
    });

    // Manejo de la sección de resumen
    // Evento para el botón "inicio" que regresa al buzón electrónico
    btnInicio.addEventListener('click', function() {
        mostrarSeccion('buzon');
    });
    
});
