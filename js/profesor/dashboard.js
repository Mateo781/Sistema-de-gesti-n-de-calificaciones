document.addEventListener('DOMContentLoaded', function () {

    // ------------------------------------------------------------
    // 0) Datos reales inyectados desde PHP
    // ------------------------------------------------------------
    let datos = { promedioPorCurso: { labels: [], data: [] }, estadoCarga: {}, evolucion: { labels: [], data: [] }, eventosCalendario: [] };
    const dataEl = document.getElementById('dashboardData');
    if (dataEl) {
        try {
            datos = JSON.parse(dataEl.textContent);
        } catch (e) {
            console.error('No se pudo parsear dashboardData:', e);
        }
    }

    const colores = {
        blue700: '#1E4FD8',
        sky400: '#0EA5E9',
        indigo: '#6366F1',
        amber: '#D97706',
        green: '#15A26B',
        red: '#DC2626',
        grayBorder: '#E3E8F0',
        textMuted: '#64748B'
    };

    // ------------------------------------------------------------
    // 1) Loader de carga
    // ------------------------------------------------------------
    window.addEventListener('load', function () {
        const overlay = document.getElementById('loaderOverlay');
        if (overlay) {
            setTimeout(() => overlay.classList.add('hidden'), 250);
        }
    });

    // ------------------------------------------------------------
    // 2) Reloj en tiempo real
    // ------------------------------------------------------------
    const relojEl = document.getElementById('relojEnVivo');
    function actualizarReloj() {
        if (!relojEl) return;
        const ahora = new Date();
        const hh = String(ahora.getHours()).padStart(2, '0');
        const mm = String(ahora.getMinutes()).padStart(2, '0');
        const ss = String(ahora.getSeconds()).padStart(2, '0');
        relojEl.textContent = `${hh}:${mm}:${ss}`;
    }
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    // ------------------------------------------------------------
    // 3) Contadores animados en las tarjetas de estadísticas
    // ------------------------------------------------------------
    document.querySelectorAll('.stat-number').forEach(function (el) {
        const targetRaw = el.getAttribute('data-count');
        const esDecimal = el.hasAttribute('data-decimal');
        const target = parseFloat(targetRaw) || 0;

        if (target === 0) {
            el.textContent = esDecimal ? '0.00' : '0';
            return;
        }

        const duracion = 900;
        const inicio = performance.now();

        function frame(ahora) {
            const progreso = Math.min((ahora - inicio) / duracion, 1);
            const valorActual = target * progreso;
            el.textContent = esDecimal ? valorActual.toFixed(2) : Math.round(valorActual).toString();
            if (progreso < 1) {
                requestAnimationFrame(frame);
            } else {
                el.textContent = esDecimal ? target.toFixed(2) : Math.round(target).toString();
            }
        }
        requestAnimationFrame(frame);
    });

    // ------------------------------------------------------------
    // 4) Gráficos (Chart.js) con datos reales
    // ------------------------------------------------------------
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = colores.textMuted;

        // Gráfico 1: Promedio por curso (barras)
        const canvasBarras = document.getElementById('chartPromedioCurso');
        if (canvasBarras && datos.promedioPorCurso.labels.length) {
            new Chart(canvasBarras, {
                type: 'bar',
                data: {
                    labels: datos.promedioPorCurso.labels,
                    datasets: [{
                        label: 'Promedio',
                        data: datos.promedioPorCurso.data,
                        backgroundColor: colores.blue700,
                        borderRadius: 6,
                        maxBarThickness: 46
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 10, grid: { color: colores.grayBorder } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Gráfico 2: Estado de carga de calificaciones (doughnut)
        const canvasDoughnut = document.getElementById('chartEstadoCarga');
        if (canvasDoughnut) {
            const ec = datos.estadoCarga || {};
            new Chart(canvasDoughnut, {
                type: 'doughnut',
                data: {
                    labels: ['Completadas', 'Pendientes', 'Sin comenzar'],
                    datasets: [{
                        data: [ec.completadas || 0, ec.pendientes || 0, ec.sin_comenzar || 0],
                        backgroundColor: [colores.green, colores.amber, colores.grayBorder],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '68%',
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 16, font: { size: 12 } } } }
                }
            });
        }

        // Gráfico 3: Evolución del promedio (línea)
        const canvasLinea = document.getElementById('chartEvolucion');
        if (canvasLinea && datos.evolucion.labels.length) {
            new Chart(canvasLinea, {
                type: 'line',
                data: {
                    labels: datos.evolucion.labels,
                    datasets: [{
                        label: 'Promedio',
                        data: datos.evolucion.data,
                        borderColor: colores.sky400,
                        backgroundColor: 'rgba(14, 165, 233, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: colores.sky400,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 10, grid: { color: colores.grayBorder } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }

    // ------------------------------------------------------------
    // 5) Buscador + paginación de la tabla de cursos
    // ------------------------------------------------------------
    const tabla = document.getElementById('tablaCursos');
    const buscador = document.getElementById('buscadorCursos');
    const paginacionEl = document.getElementById('paginacionCursos');
    const FILAS_POR_PAGINA = 5;

    if (tabla) {
        const filas = Array.from(tabla.querySelectorAll('tbody tr'));
        let filasFiltradas = filas.slice();
        let paginaActual = 1;

        function renderPagina() {
            const totalPaginas = Math.max(1, Math.ceil(filasFiltradas.length / FILAS_POR_PAGINA));
            paginaActual = Math.min(paginaActual, totalPaginas);

            filas.forEach(f => f.classList.add('row-hidden'));

            const inicio = (paginaActual - 1) * FILAS_POR_PAGINA;
            const visibles = filasFiltradas.slice(inicio, inicio + FILAS_POR_PAGINA);
            visibles.forEach(f => f.classList.remove('row-hidden'));

            if (!paginacionEl) return;
            paginacionEl.innerHTML = '';

            if (totalPaginas <= 1) return;

            for (let i = 1; i <= totalPaginas; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                if (i === paginaActual) btn.classList.add('active');
                btn.addEventListener('click', function () {
                    paginaActual = i;
                    renderPagina();
                });
                paginacionEl.appendChild(btn);
            }
        }

        if (buscador) {
            buscador.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                filasFiltradas = filas.filter(f => f.textContent.toLowerCase().includes(q));
                paginaActual = 1;
                renderPagina();
            });
        }

        renderPagina();
    }

    // ------------------------------------------------------------
    // 6) Botón actualizar datos
    // ------------------------------------------------------------
    const btnActualizar = document.getElementById('btnActualizar');
    if (btnActualizar) {
        btnActualizar.addEventListener('click', function () {
            this.classList.add('spinning');
            window.location.reload();
        });
    }

    // ------------------------------------------------------------
    // 7) Botón pantalla completa
    // ------------------------------------------------------------
    const btnFullscreen = document.getElementById('btnFullscreen');
    if (btnFullscreen) {
        btnFullscreen.addEventListener('click', function () {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
                this.querySelector('i').className = 'fa-solid fa-compress';
            } else {
                document.exitFullscreen();
                this.querySelector('i').className = 'fa-solid fa-expand';
            }
        });
    }

    // ------------------------------------------------------------
    // 8) Scroll reveal (IntersectionObserver)
    // ------------------------------------------------------------
    const elementosReveal = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window && elementosReveal.length) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        elementosReveal.forEach(el => observer.observe(el));
    } else {
        elementosReveal.forEach(el => el.classList.add('visible'));
    }

    // ------------------------------------------------------------
    // 9) Calendario mensual con eventos reales (evaluaciones)
    // ------------------------------------------------------------
    const calendarGrid = document.getElementById('calendarGrid');
    const calMesLabel = document.getElementById('calMesLabel');
    const calendarDetail = document.getElementById('calendarDetail');

    if (calendarGrid) {
        const eventosPorFecha = {};
        (datos.eventosCalendario || []).forEach(ev => {
            if (!eventosPorFecha[ev.fecha]) eventosPorFecha[ev.fecha] = [];
            eventosPorFecha[ev.fecha].push(ev);
        });

        const nombresMes = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        const nombresDow = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

        const hoy = new Date();
        let mesActual = hoy.getMonth();
        let anioActual = hoy.getFullYear();

        function formatFecha(anio, mes, dia) {
            return `${anio}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
        }

        function renderCalendario() {
            calendarGrid.innerHTML = '';
            calMesLabel.textContent = `${nombresMes[mesActual]} ${anioActual}`;

            nombresDow.forEach(d => {
                const el = document.createElement('div');
                el.className = 'cal-dow';
                el.textContent = d;
                calendarGrid.appendChild(el);
            });

            const primerDia = new Date(anioActual, mesActual, 1).getDay();
            const diasEnMes = new Date(anioActual, mesActual + 1, 0).getDate();

            for (let i = 0; i < primerDia; i++) {
                const vacio = document.createElement('div');
                vacio.className = 'cal-day empty';
                calendarGrid.appendChild(vacio);
            }

            for (let dia = 1; dia <= diasEnMes; dia++) {
                const fechaStr = formatFecha(anioActual, mesActual, dia);
                const celda = document.createElement('div');
                celda.className = 'cal-day';
                celda.textContent = dia;

                const esHoy = anioActual === hoy.getFullYear() && mesActual === hoy.getMonth() && dia === hoy.getDate();
                if (esHoy) celda.classList.add('today');

                if (eventosPorFecha[fechaStr]) {
                    celda.classList.add('has-event');
                    celda.addEventListener('click', function () {
                        mostrarDetalleDia(fechaStr, eventosPorFecha[fechaStr]);
                    });
                }

                calendarGrid.appendChild(celda);
            }
        }

        function mostrarDetalleDia(fechaStr, eventos) {
            if (!calendarDetail) return;
            const [anio, mes, dia] = fechaStr.split('-');
            let html = `<strong>${dia}/${mes}/${anio}</strong>`;
            eventos.forEach(ev => {
                html += `<div class="calendar-detail-item">${ev.titulo} — ${ev.materia} (${ev.curso})</div>`;
            });
            calendarDetail.innerHTML = html;
        }

        document.getElementById('calPrev').addEventListener('click', function () {
            mesActual--;
            if (mesActual < 0) { mesActual = 11; anioActual--; }
            renderCalendario();
        });
        document.getElementById('calNext').addEventListener('click', function () {
            mesActual++;
            if (mesActual > 11) { mesActual = 0; anioActual++; }
            renderCalendario();
        });

        renderCalendario();
    }

    // ------------------------------------------------------------
    // 10) Acceso rápido "Calendario": scroll suave al panel
    // ------------------------------------------------------------
    const btnVerCalendario = document.getElementById('btnVerCalendario');
    if (btnVerCalendario) {
        btnVerCalendario.addEventListener('click', function () {
            const panel = document.getElementById('panelCalendario');
            if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    // ------------------------------------------------------------
    // 11) Tooltips simples (title nativo ya cubre los botones de ícono;
    //     acá agregamos uno liviano para las tarjetas de estadísticas)
    // ------------------------------------------------------------
    document.querySelectorAll('.stat-card').forEach(card => {
        const label = card.querySelector('.stat-label');
        if (label) card.setAttribute('title', label.textContent);
    });

});