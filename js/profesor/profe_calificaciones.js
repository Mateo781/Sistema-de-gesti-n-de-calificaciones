document.addEventListener('DOMContentLoaded', function () {
    const selectCurso = document.getElementById('filtroCurso');
    const selectMateria = document.getElementById('filtroMateria');
    const contenedorAlumnos = document.getElementById('contenedorAlumnos');
    const mensajeEstado = document.getElementById('mensajeEstado');

    // Si alguno de estos IDs no existe en el HTML, el script se corta acá silenciosamente.
    if (!selectCurso || !selectMateria || !contenedorAlumnos) {
        console.error('calificaciones.js: no se encontraron los elementos #filtroCurso, #filtroMateria o #contenedorAlumnos en el HTML.');
        return;
    }

    const placeholderVacio = `
        <div class="table-card" style="padding: 40px; text-align: center; color: var(--t2); border: 2px dashed rgba(0,0,0,0.06);">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px; opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <p>Por favor, seleccione un Curso y una Materia para desplegar la planilla de alumnos.</p>
        </div>`;

    const cargandoAlumnos = `
        <div class="table-card" style="padding: 30px; text-align: center; color: var(--t2);">
            Cargando alumnos...
        </div>`;

    function mostrarMensaje(tipo, texto) {
        const estilo = tipo === 'success'
            ? 'background: #e8f5e9; color: #2e7d32;'
            : 'background: #ffebee; color: #c62828;';

        mensajeEstado.innerHTML =
            '<div style="' + estilo + ' padding: 14px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">'
            + texto + '</div>';

        setTimeout(function () {
            mensajeEstado.innerHTML = '';
        }, 5000);
    }

    // ---------------------------------------------------------
    // 1) Al cambiar el CURSO: cargar materias (AJAX) y limpiar alumnos
    // ---------------------------------------------------------
    selectCurso.addEventListener('change', function () {
        const idCurso = this.value;

        // Reset de materia y alumnos
        selectMateria.innerHTML = '<option value="">-- Seleccionar Materia --</option>';
        selectMateria.disabled = true;
        contenedorAlumnos.innerHTML = placeholderVacio;

        if (!idCurso) {
            return;
        }

        fetch('php/profesor/obtenerMaterias.php?id_curso=' + encodeURIComponent(idCurso))
            .then(function (response) {
                if (!response.ok) throw new Error('HTTP ' + response.status + ' al pedir obtenerMaterias.php');
                return response.text();
            })
            .then(function (html) {
                selectMateria.innerHTML = html;
                selectMateria.disabled = false;
            })
            .catch(function (error) {
                console.error('Error al cargar materias:', error);
                mostrarMensaje('error', 'No se pudieron cargar las materias.');
            });
    });

    // ---------------------------------------------------------
    // 2) Al cambiar la MATERIA: cargar alumnos (AJAX)
    // ---------------------------------------------------------
    selectMateria.addEventListener('change', function () {
        const idMateria = this.value;
        const idCurso = selectCurso.value;

        if (!idMateria || !idCurso) {
            contenedorAlumnos.innerHTML = placeholderVacio;
            return;
        }

        contenedorAlumnos.innerHTML = cargandoAlumnos;

        fetch('php/profesor/obtenerAlumnos.php?id_curso=' + encodeURIComponent(idCurso) + '&id_materia=' + encodeURIComponent(idMateria))
            .then(function (response) {
                if (!response.ok) throw new Error('HTTP ' + response.status + ' al pedir obtenerAlumnos.php');
                return response.text();
            })
            .then(function (html) {
                contenedorAlumnos.innerHTML = html;
                activarFormularioNotas();
            })
            .catch(function (error) {
                console.error('Error al cargar alumnos:', error);
                mostrarMensaje('error', 'No se pudieron cargar los alumnos.');
            });
    });

    // ---------------------------------------------------------
    // 3) Guardar notas por AJAX (el form se crea dinámicamente,
    //    por eso se activa el listener cada vez que se inyecta la tabla)
    // ---------------------------------------------------------
    function activarFormularioNotas() {
        const form = document.getElementById('formCalificaciones');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            const btnSubmit = form.querySelector('button[type="submit"]');
            const textoOriginal = btnSubmit.innerHTML;

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Guardando...';

            fetch('php/profesor/guardarNotas.php', {
                method: 'POST',
                body: formData
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status + ' al pedir guardarNotas.php');
                    return response.json();
                })
                .then(function (data) {
                    if (data.success) {
                        mostrarMensaje('success', data.mensaje);
                        form.reset();
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