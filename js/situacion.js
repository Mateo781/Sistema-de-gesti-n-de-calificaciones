/* ══════════════════════════════════════════════════════════
   RITE · Situación Académica — js/situacion.js
══════════════════════════════════════════════════════════ */
'use strict';

// Helpers
const $ = id => document.getElementById(id);

// ─── TRAYECTORIA ACADÉMICA DATA (MOCK BASE DE DATOS) ─────────
const HISTORIAL_ACADEMICO = {
  '7': {
    title: '7° Año — Especialidad Programación',
    cycle: 'Ciclo Lectivo Activo 2026',
    status: 'proceso',
    statusLabel: 'Cursando',
    materias: [
      { nombre: 'Matemática', estado: 'proceso', nota: 5.5, condicion: 'Intensificación dic', observaciones: 'Prof. Rodríguez, M. - Entregar TP integrador' },
      { nombre: 'Lengua y Literatura', estado: 'aprobada', nota: 8.0, condicion: 'Regular', observaciones: 'Prof. Gómez, P. - Eximido' },
      { nombre: 'Química', estado: 'aprobada', nota: 8.5, condicion: 'Regular', observaciones: 'Prof. Torres, A. - Excelente desempeño' },
      { nombre: 'Física', estado: 'proceso', nota: 6.0, condicion: 'Intensificación dic', observaciones: 'Prof. Sánchez, J. - Falta evaluación práctica' },
      { nombre: 'Programación y Redes', estado: 'proceso', nota: null, condicion: 'Cursando', observaciones: 'Prof. Herrera, V. - Nota parcial: 8' },
      { nombre: 'Informática Aplicada', estado: 'aprobada', nota: 9.5, condicion: 'Regular', observaciones: 'Prof. Pérez, D. - Proyecto Final aprobado' },
      { nombre: 'Construcción de Ciudadanía', estado: 'proceso', nota: null, condicion: 'Cursando', observaciones: 'Prof. Díaz, N.' }
    ]
  },
  '6': {
    title: '6° Año — Especialidad Programación',
    cycle: 'Ciclo Lectivo 2025',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 7.0, condicion: 'Regular', observaciones: 'Aprobado en período regular' },
      { nombre: 'Lengua', estado: 'aprobada', nota: 8.0, condicion: 'Regular', observaciones: 'Eximido' },
      { nombre: 'Química', estado: 'aprobada', nota: 7.5, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Física', estado: 'aprobada', nota: 7.0, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Análisis de Sistemas', estado: 'aprobada', nota: 9.0, condicion: 'Regular', observaciones: 'Destacado' },
      { nombre: 'Bases de Datos', estado: 'aprobada', nota: 8.0, condicion: 'Regular', observaciones: 'Aprobado' }
    ]
  },
  '5': {
    title: '5° Año — Especialidad Programación',
    cycle: 'Ciclo Lectivo 2024',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 7.5, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Lengua', estado: 'aprobada', nota: 8.5, condicion: 'Regular', observaciones: 'Eximido' },
      { nombre: 'Geografía', estado: 'aprobada', nota: 8.0, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Historia', estado: 'aprobada', nota: 7.0, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Programación I', estado: 'aprobada', nota: 9.0, condicion: 'Regular', observaciones: 'Destacado' }
    ]
  },
  '4': {
    title: '4° Año — Ciclo Superior',
    cycle: 'Ciclo Lectivo 2023',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 7.0, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Lengua', estado: 'aprobada', nota: 7.0, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Biología', estado: 'aprobada', nota: 8.0, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Introducción a la Programación', estado: 'aprobada', nota: 8.5, condicion: 'Regular', observaciones: 'Aprobado' }
    ]
  },
  '3': {
    title: '3° Año — Ciclo Básico',
    cycle: 'Ciclo Lectivo 2022',
    status: 'deuda',
    statusLabel: 'Con Materias Previas',
    materias: [
      { nombre: 'Matemática', estado: 'desaprobada', nota: 3.0, condicion: 'Pendiente / Recursar', observaciones: 'Debe recursar o rendir en comisión especial' },
      { nombre: 'Lengua', estado: 'aprobada', nota: 7.5, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Ciencias Sociales', estado: 'aprobada', nota: 8.0, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Ciencias Naturales', estado: 'aprobada', nota: 7.0, condicion: 'Regular', observaciones: 'Aprobado' }
    ]
  },
  '2': {
    title: '2° Año — Ciclo Básico',
    cycle: 'Ciclo Lectivo 2021',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 8.0, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Lengua', estado: 'aprobada', nota: 7.5, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Historia', estado: 'aprobada', nota: 8.0, condicion: 'Regular', observaciones: 'Aprobado' }
    ]
  },
  '1': {
    title: '1° Año — Ciclo Básico',
    cycle: 'Ciclo Lectivo 2020',
    status: 'aprobado',
    statusLabel: 'Aprobado',
    materias: [
      { nombre: 'Matemática', estado: 'aprobada', nota: 8.5, condicion: 'Regular', observaciones: 'Aprobado' },
      { nombre: 'Lengua', estado: 'aprobada', nota: 8.0, condicion: 'Regular', observaciones: 'Aprobado' }
    ]
  }
};

const RECUPERACIONES = [
  { materia: 'Matemática (3° Año)', tipo: 'Recursada', detalle: 'Comisión especial de apoyo los días miércoles 14:00 hs.', plazo: 'Régimen de recursada 2026', badge: 'recursada' },
  { materia: 'Matemática (7° Año)', tipo: 'Intensificación', detalle: 'Instancia presencial de diciembre 2025.', plazo: 'Del 01/12 al 15/12', badge: 'intensificacion' },
  { materia: 'Física (7° Año)', tipo: 'Intensificación', detalle: 'Entrega de portafolio y examen práctico.', plazo: 'Del 01/12 al 15/12', badge: 'intensificacion' }
];

// State variables
let selectedYear = '7';

// ─── RENDER FUNCTIONS ───────────────────────────────────────
function renderCycleTabs() {
  const tabsContainer = $('cycleTabs');
  if (!tabsContainer) return;

  const years = Object.keys(HISTORIAL_ACADEMICO).sort((a, b) => b - a); // Descending order
  tabsContainer.innerHTML = years.map(yr => {
    const activeClass = yr === selectedYear ? 'active' : '';
    return `<button class="cycle-tab ${activeClass}" data-year="${yr}">${yr}° Año</button>`;
  }).join('');

  // Add event listeners
  tabsContainer.querySelectorAll('.cycle-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      selectedYear = tab.dataset.year;
      renderCycleTabs();
      renderTrajectoryDetails();
    });
  });
}

function renderTrajectoryDetails() {
  const data = HISTORIAL_ACADEMICO[selectedYear];
  if (!data) return;

  // Header update
  $('selectedCycleTitle').textContent = data.title;
  $('selectedCycleSubtitle').textContent = data.cycle;
  
  // Status badge update
  const badge = $('cycleStatusBadge');
  badge.className = `badge-status-cycle ${data.status}`;
  badge.textContent = data.statusLabel;

  // Table Body render
  const tbody = $('trajectoryBody');
  tbody.innerHTML = data.materias.map((mat, i) => {
    const formattedNota = mat.nota !== null ? mat.nota.toFixed(1) : '—';
    
    // Status column html
    let statusClass = 'badge-pendiente';
    let statusText = 'Pendiente';
    if (mat.estado === 'aprobada') { statusClass = 'badge-aprobada'; statusText = 'Aprobada'; }
    else if (mat.estado === 'proceso') { statusClass = 'badge-proceso'; statusText = 'En proceso'; }
    else if (mat.estado === 'desaprobada') { statusClass = 'badge-desaprobada'; statusText = 'No aprobada'; }

    return `
      <tr style="animation: fadeUp 200ms ${i * 40}ms both">
        <td class="td-materia"><strong>${mat.nombre}</strong></td>
        <td><span class="badge ${statusClass}">${statusText}</span></td>
        <td class="td-nota">${formattedNota}</td>
        <td class="td-condicion">${mat.condicion}</td>
        <td class="td-observaciones">${mat.observaciones}</td>
      </tr>
    `;
  }).join('');
}

function renderStatsSummary() {
  // Let's compute statistics dynamically across all cycles
  let totalMaterias = 0;
  let aprobadas = 0;
  let deudas = 0;
  let totalNotasSum = 0;
  let totalNotasCount = 0;

  Object.values(HISTORIAL_ACADEMICO).forEach(yr => {
    yr.materias.forEach(mat => {
      totalMaterias++;
      if (mat.estado === 'aprobada') {
        aprobadas++;
      }
      if (mat.estado === 'desaprobada') {
        deudas++;
      }
      if (mat.nota !== null) {
        totalNotasSum += mat.nota;
        totalNotasCount++;
      }
    });
  });

  const promedioGral = totalNotasCount > 0 ? (totalNotasSum / totalNotasCount).toFixed(1) : '—';

  // Current year stats (7° año)
  const currentYearData = HISTORIAL_ACADEMICO['7'];
  const curAprobadas = currentYearData.materias.filter(m => m.estado === 'aprobada').length;
  const curTotal = currentYearData.materias.length;

  $('promedioAcumulado').textContent = promedioGral;
  $('aprobadasTotales').textContent = `${curAprobadas} / ${curTotal}`;
  $('materiasDeuda').textContent = deudas;
}

function renderRecoveryList() {
  const listEl = $('recoveryList');
  if (!listEl) return;

  listEl.innerHTML = RECUPERACIONES.map(rec => `
    <div class="recovery-item">
      <div class="recovery-header">
        <span class="recovery-materia">${rec.materia}</span>
        <span class="recovery-badge ${rec.badge}">${rec.tipo}</span>
      </div>
      <div class="recovery-detail">${rec.detalle}</div>
      <div class="recovery-date">Período: ${rec.plazo}</div>
    </div>
  `).join('');
}

// ─── INITIALIZATION ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  renderCycleTabs();
  renderTrajectoryDetails();
  renderStatsSummary();
  renderRecoveryList();

  // Mobile sidebar toggle callback
  const hamburger = $('hamburger');
  const sidebar = $('sidebar');
  const overlay = $('overlay');

  if (hamburger && sidebar && overlay) {
    hamburger.addEventListener('click', () => {
      sidebar.classList.add('open');
      overlay.classList.add('show');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
    });
  }
});
