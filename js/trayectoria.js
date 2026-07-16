// Estructura del Timeline principal
const TRAYECTORIA = [
  { year: '1°', label: '1° Año', status: 'green', statusLabel: 'Aprobado', avg: '8.2', connector: null, key: '1' },
  { year: '2°', label: '2° Año', status: 'green', statusLabel: 'Aprobado', avg: '7.9', connector: 'filled', key: '2' },
  { year: '3°', label: '3° Año', status: 'red', statusLabel: 'Con deuda', avg: '5.8', connector: 'filled', key: '3' },
  { year: '4°', label: '4° Año', status: 'yellow', statusLabel: 'En proceso', avg: '7.4', connector: 'partial', current: true, key: '4' },
  { year: '5°', label: '5° Año', status: 'gray', statusLabel: 'Pendiente', avg: '—', connector: 'dashed', key: '5' },
  { year: '6°', label: '6° Año', status: 'gray', statusLabel: 'Pendiente', avg: '—', connector: 'dashed', key: '6' },
  { year: '7°', label: '7° Año', status: 'gray', statusLabel: 'Pendiente', avg: '—', connector: 'dashed', key: '7' },
];

// Base de datos detallada de materias por año
const HISTORIAL = {
  '1': {
    title: '1° Año — Ciclo Básico',
    cycle: 'Ciclo Lectivo 2020',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 8.5, observaciones: 'Prof. Domínguez, R. — Aprobado en instancia regular' },
      { nombre: 'Lengua y Literatura', estado: 'aprobada', nota: 8.0, observaciones: 'Prof. Vega, C. — Eximido' }
    ]
  },
  '2': {
    title: '2° Año — Ciclo Básico',
    cycle: 'Ciclo Lectivo 2021',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 8.0, observaciones: 'Prof. Domínguez, R. — Aprobado' },
      { nombre: 'Lengua y Literatura', estado: 'aprobada', nota: 7.5, observaciones: 'Prof. Vega, C. — Aprobado' },
      { nombre: 'Historia', estado: 'aprobada', nota: 8.0, observaciones: 'Prof. Martínez, L. — Aprobado' }
    ]
  },
  '3': {
    title: '3° Año — Ciclo Básico',
    cycle: 'Ciclo Lectivo 2022',
    status: 'deuda',
    statusLabel: 'Con materias previas',
    materias: [
      { nombre: 'Matemática', estado: 'desaprobada', nota: 3.0, observaciones: 'Prof. Rodríguez, M. — Debe recursar o rendir en comisión especial' },
      { nombre: 'Lengua y Literatura', estado: 'aprobada', nota: 7.5, observaciones: 'Prof. Vega, C. — Aprobado' },
      { nombre: 'Ciencias Sociales', estado: 'aprobada', nota: 8.0, observaciones: 'Prof. Fernández, R. — Aprobado' },
      { nombre: 'Ciencias Naturales', estado: 'aprobada', nota: 7.0, observaciones: 'Prof. López, S. — Aprobado' }
    ]
  },
  '4': {
    title: '4° Año — Ciclo Superior',
    cycle: 'Ciclo Lectivo 2023',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 7.0, observaciones: 'Prof. Rodríguez, M. — Aprobado' },
      { nombre: 'Lengua y Literatura', estado: 'aprobada', nota: 7.0, observaciones: 'Prof. Vega, C. — Aprobado' },
      { nombre: 'Biología', estado: 'aprobada', nota: 8.0, observaciones: 'Prof. López, S. — Aprobado' },
      { nombre: 'Introducción a la Programación', estado: 'aprobada', nota: 8.5, observaciones: 'Prof. Pérez, D. — Aprobado' }
    ]
  },
  '5': {
    title: '5° Año — Ciclo Superior',
    cycle: 'Ciclo Lectivo 2024',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 7.5, observaciones: 'Prof. Rodríguez, M. — Aprobado' },
      { nombre: 'Lengua y Literatura', estado: 'aprobada', nota: 8.5, observaciones: 'Prof. Vega, C. — Eximido' },
      { nombre: 'Geografía', estado: 'aprobada', nota: 8.0, observaciones: 'Prof. Fernández, R. — Aprobado' },
      { nombre: 'Historia', estado: 'aprobada', nota: 7.0, observaciones: 'Prof. Martínez, L. — Aprobado' },
      { nombre: 'Programación I', estado: 'aprobada', nota: 9.0, observaciones: 'Prof. Herrera, V. — Destacado' }
    ]
  },
  '6': {
    title: '6° Año — Especialidad Programación',
    cycle: 'Ciclo Lectivo 2025',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 7.0, observaciones: 'Prof. Rodríguez, M. — Aprobado en período regular' },
      { nombre: 'Lengua y Literatura', estado: 'aprobada', nota: 8.0, observaciones: 'Prof. Vega, C. — Eximido' },
      { nombre: 'Química', estado: 'aprobada', nota: 7.5, observaciones: 'Prof. Torres, A. — Aprobado' },
      { nombre: 'Física', estado: 'aprobada', nota: 7.0, observaciones: 'Prof. Sánchez, J. — Aprobado' },
      { nombre: 'Análisis de Sistemas', estado: 'aprobada', nota: 9.0, observaciones: 'Prof. Pérez, D. — Destacado' },
      { nombre: 'Bases de Datos', estado: 'aprobada', nota: 8.0, observaciones: 'Prof. Herrera, V. — Aprobado' }
    ]
  },
  '7': {
    title: '7° Año — Especialidad Programación',
    cycle: 'Ciclo Lectivo Activo 2026',
    status: 'proceso',
    statusLabel: 'Cursando',
    materias: [
      { nombre: 'Matemática', estado: 'proceso', nota: 5.5, observaciones: 'Prof. Rodríguez, M. — Entregar TP integrador' },
      { nombre: 'Lengua y Literatura', estado: 'aprobada', nota: 8.0, observaciones: 'Prof. Vega, C. — Eximido' },
      { nombre: 'Química', estado: 'aprobada', nota: 8.5, observaciones: 'Prof. Torres, A. — Excelente desempeño' },
      { nombre: 'Física', estado: 'proceso', nota: 6.0, observaciones: 'Prof. Sánchez, J. — Falta evaluación práctica' },
      { nombre: 'Programación y Redes', estado: 'proceso', nota: null, observaciones: 'Prof. Herrera, V. — Nota parcial: 8' },
      { nombre: 'Informática Aplicada', estado: 'aprobada', nota: 9.5, observaciones: 'Prof. Pérez, D. — Proyecto Final aprobado' },
      { nombre: 'Construcción de Ciudadanía', estado: 'proceso', nota: null, observaciones: 'Prof. Díaz, N.' }
    ]
  }
};

let activeKey = null;

// Helpers de formateo de estilos dinámicos
function getNotaClass(n) {
  if (n === null) return 'nota-nd';
  if (n >= 7) return 'nota-alta';
  if (n >= 6) return 'nota-media';
  return 'nota-baja';
}

function getBadgeClass(estado) {
  if (estado === 'aprobada') return 'badge-aprobada';
  if (estado === 'proceso') return 'badge-proceso';
  if (estado === 'desaprobada') return 'badge-desaprobada';
  return 'badge-pendiente';
}

function getBadgeLabel(estado) {
  if (estado === 'aprobada') return 'Aprobada';
  if (estado === 'proceso') return 'En proceso';
  if (estado === 'desaprobada') return 'No aprobada';
  return 'Pendiente';
}

// Renderiza los nodos del Timeline superior
function renderTimeline() {
  const track = document.getElementById('timelineTrack');
  let html = '';
  
  TRAYECTORIA.forEach((node, i) => {
    if (i > 0) {
      html += `<div class="timeline-connector ${node.connector || 'dashed'}"></div>`;
    }
    const badge = node.current ? `<span class="node-current-badge">Cursando</span>` : '';
    const isActive = node.key === activeKey;
    
    html += `
      <div class="timeline-node${isActive ? ' active' : ''}" data-key="${node.key}" tabindex="0" role="button" aria-label="${node.label}: ${node.statusLabel}">
        <div class="node-circle ${node.status}">${badge}
          <span class="node-year">${node.year}</span>
        </div>
        <span class="node-label">${node.label}</span>
        <span class="node-status ${node.status}">${node.statusLabel}</span>
      </div>`;
  });
  
  track.innerHTML = html;

  // Escuchadores de eventos para interactividad
  track.querySelectorAll('.timeline-node').forEach(el => {
    el.addEventListener('click', () => selectYear(el.dataset.key));
    el.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        selectYear(el.dataset.key);
      }
    });
  });
}

// Handler de selección de año
function selectYear(key) {
  if (activeKey === key) return;
  activeKey = key;
  renderTimeline();
  renderDetail(key);
}

// Actualiza el panel informativo inferior
function renderDetail(key) {
  const data = HISTORIAL[key];
  if (!data) return;

  document.getElementById('detailTitle').textContent = data.title;
  document.getElementById('detailSub').textContent = data.cycle;

  const badge = document.getElementById('detailBadge');
  badge.className = `badge-status ${data.status}`;
  badge.textContent = data.statusLabel;

  const tbody = document.getElementById('detailBody');
  tbody.innerHTML = data.materias.map((m, i) => {
    const notaStr = m.nota !== null ? m.nota.toFixed(1) : '—';
    const notaCls = getNotaClass(m.nota);
    return `
    <tr style="animation-delay: ${i * 40}ms">
      <td><strong>${m.nombre}</strong></td>
      <td><span class="badge ${getBadgeClass(m.estado)}">${getBadgeLabel(m.estado)}</span></td>
      <td class="${notaCls}">${notaStr}</td>
      <td class="td-obs">${m.observaciones}</td>
    </tr>`;
  }).join('');
}

// Inicialización del Timeline al cargar
renderTimeline();

// Comportamiento "Drag to Scroll" con mouse para el contenedor horizontal
const wrapper = document.getElementById('timelineWrapper');
let isDown = false, startX, scrollLeft;

wrapper.addEventListener('mousedown', e => {
  isDown = true;
  wrapper.style.cursor = 'grabbing';
  startX = e.pageX - wrapper.offsetLeft;
  scrollLeft = wrapper.scrollLeft;
});

wrapper.addEventListener('mouseleave', () => {
  isDown = false;
  wrapper.style.cursor = 'grab';
});

wrapper.addEventListener('mouseup', () => {
  isDown = false;
  wrapper.style.cursor = 'grab';
});

wrapper.addEventListener('mousemove', e => {
  if (!isDown) return;
  e.preventDefault();
  const x = e.pageX - wrapper.offsetLeft;
  wrapper.scrollLeft = scrollLeft - (x - startX) * 1.2;
});