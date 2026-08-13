document.addEventListener('DOMContentLoaded', function () {
    const selectCurso = document.getElementById('filtroCurso');
    const selectMateria = document.getElementById('filtroMateria');
    const contenedorAlumnos = document.getElementById('contenedorAlumnos');
    const mensajeEstado = document.getElementById('mensajeEstado');

    if (!selectCurso || !selectMateria || !contenedorAlumnos) {
        console.error('calificaciones.js: faltan elementos principales en el HTML.');
        return;
    }

    const placeholderVacio = `
        <div style="padding: 40px; text-align: center; color: #666; border: 2px dashed rgba(0,0,0,0.06); background: #fff; border-radius: 12px;">
            <p>Por favor, seleccione un Curso y una Materia para desplegar la planilla de alumnos.</p>
        </div>`;

    const cargandoAlumnos = `
        <div style="padding: 30px; text-align: center; color: #666;">
            Cargando alumnos...
        </div>`;

    function mostrarMensaje(tipo, texto) {
        if (!mensajeEstado) return;
        const estilo = tipo === 'success'
            ? 'background: #e8f5e9; color: #2e7d32;'
            : 'background: #ffebee; color: #c62828;';

        mensajeEstado.innerHTML = '<div style="' + estilo + ' padding: 14px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">' + texto + '</div>';

        setTimeout(function () {
            mensajeEstado.innerHTML = '';
        }, 5000);
    }

    // Función para calcular el promedio de una fila específica
    function actualizarPromedioFila(fila) {
        if (!fila) return;
        
        const inputs = fila.querySelectorAll('.nota-criterio');
        const spanPromedio = fila.querySelector('.promedio-final');
        const inputHidden = fila.querySelector('.input-promedio-hidden');

        let suma = 0;
        let cantidad = 0;

        inputs.forEach(input => {
            let valorStr = input.value.replace(',', '.');
            let valor = parseFloat(valorStr);
            
            if (!isNaN(valor) && input.value.trim() !== '') {
                suma += valor;
                cantidad++;
            }
        });

        if (cantidad > 0) {
            let promedio = (suma / cantidad).toFixed(2);
            if (spanPromedio) spanPromedio.textContent = promedio;
            if (inputHidden) inputHidden.value = promedio;
        } else {
            if (spanPromedio) spanPromedio.textContent = '--';
            if (inputHidden) inputHidden.value = '';
        }
    }

    // Inicializar cálculo en todas las filas de la tabla actual
    function inicializarCalculoTabla() {
        document.querySelectorAll('.fila-alumno').forEach(fila => {
            actualizarPromedioFila(fila);
        });
    }

    // Escuchar cambios de escritura en los inputs de notas de manera global
    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('nota-criterio')) {
            actualizarPromedioFila(e.target.closest('.fila-alumno'));
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('nota-criterio')) {
            actualizarPromedioFila(e.target.closest('.fila-alumno'));
        }
    });

    // 1) Cambio de Curso -> Carga Materias
    selectCurso.addEventListener('change', function () {
        const idCurso = this.value;
        const contenedorVista = document.getElementById('vistaCalificaciones');
        const idDocente = contenedorVista ? contenedorVista.dataset.idDocente : '';

        selectMateria.innerHTML = '<option value="">-- Seleccionar Materia --</option>';
        selectMateria.disabled = true;
        contenedorAlumnos.innerHTML = placeholderVacio;

        if (!idCurso || !idDocente) return;

        fetch('/sgc/php/profesor/obtenerMaterias.php?id_curso=' + encodeURIComponent(idCurso) + '&id_docente=' + encodeURIComponent(idDocente))
            .then(function (response) { return response.text(); })
            .then(function (html) {
                selectMateria.innerHTML = html;
                selectMateria.disabled = false;
            })
            .catch(function (error) {
                console.error('Error al cargar materias:', error);
            });
    });

    let peticionEnCurso = false;

    // 2) Cambio de Materia -> Carga Alumnos
    selectMateria.addEventListener('change', function () {
        const idMateria = this.value;
        const idCurso = selectCurso.value;

        if (!idMateria || !idCurso) {
            contenedorAlumnos.innerHTML = placeholderVacio;
            return;
        }

        if (peticionEnCurso) return;
        peticionEnCurso = true;

        contenedorAlumnos.innerHTML = cargandoAlumnos;

        fetch('/sgc/php/profesor/obtenerAlumnos.php?id_curso=' + encodeURIComponent(idCurso) + '&id_materia=' + encodeURIComponent(idMateria))
            .then(function (response) { return response.text(); })
            .then(function (htmlAlumnos) {
                contenedorAlumnos.innerHTML = htmlAlumnos;
                inicializarCalculoTabla(); // <-- Calcula los promedios al renderizar los alumnos de la BD
                activarFormularioNotas();
            })
            .catch(function (error) {
                console.error('Error al cargar alumnos:', error);
                mostrarMensaje('error', 'No se pudieron cargar los alumnos.');
            })
            .finally(function () {
                peticionEnCurso = false;
            });
    });

    // 3) Guardar Notas
    function activarFormularioNotas() {
        const form = document.getElementById('formCalificaciones');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Asegurar que se recalcule todo antes de empaquetar el FormData
            document.querySelectorAll('.fila-alumno').forEach(fila => {
                actualizarPromedioFila(fila);
            });

            const formData = new FormData(form);
            const btnSubmit = form.querySelector('button[type="submit"]');
            const textoOriginal = btnSubmit.innerHTML;

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Guardando...';

            fetch('/sgc/php/profesor/guardarNotas.php', {
                method: 'POST',
                body: formData
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.success) {
                        mostrarMensaje('success', data.mensaje);
                    } else {
                        mostrarMensaje('error', data.mensaje);
                    }
                })
                .catch(function (error) {
                    console.error('Error al guardar notas:', error);
                    mostrarMensaje('error', 'Error de conexión al guardar las notas.');
                })
                .finally(function () {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = textoOriginal;
                });
        });
    }
});