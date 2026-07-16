'use strict';

const TRAYECTORIA = [
  { year: '1°',  label: '1° Año',  status: 'green',  statusLabel: 'Aprobado',    avg: '8.2', connector: null },
  { year: '2°',  label: '2° Año',  status: 'green',  statusLabel: 'Aprobado',    avg: '7.9', connector: 'filled' },
  { year: '3°',  label: '3° Año',  status: 'red',    statusLabel: 'Con deuda',   avg: '5.8', connector: 'filled' },
  { year: '4°',  label: '4° Año',  status: 'yellow', statusLabel: 'En proceso',  avg: '7.4', connector: 'partial', current: true },
  { year: '5°',  label: '5° Año',  status: 'gray',   statusLabel: 'Pendiente',   avg: '—',   connector: 'dashed' },
  { year: '6°',  label: '6° Año',  status: 'gray',   statusLabel: 'Pendiente',   avg: '—',   connector: 'dashed' },
  { year: '7°',  label: '7° Año',  status: 'gray',   statusLabel: 'Pendiente',   avg: '—',   connector: 'dashed' },
];

const MATERIAS = [
  { materia: 'Matemática',            docente: 'Prof. Rodríguez, M.',  b1: 6,    b2: 5,    estado: 'intensificacion' },
  { materia: 'Lengua y Literatura',          docente: 'Prof. Gómez, P.',      b1: 8,    b2: 8,    estado: 'aprobada' },
  { materia: 'Historia',                     docente: 'Prof. Martínez, L.',   b1: 9,    b2: 8,    estado: 'aprobada' },
  { materia: 'Geografía',                    docente: 'Prof. Fernández, R.',  b1: 7,    b2: 7,    estado: 'aprobada' },
  { materia: 'Biología',                     docente: 'Prof. López, S.',      b1: 4,    b2: null, estado: 'desaprobada' },
  { materia: 'Física',                       docente: 'Prof. Sánchez, J.',    b1: 7,    b2: null, estado: 'proceso' },
  { materia: 'Química',                      docente: 'Prof. Torres, A.',     b1: 8,    b2: 9,    estado: 'aprobada' },
  { materia: 'Inglés',                       docente: 'Prof. Wilson, C.',     b1: 10,   b2: 9,    estado: 'aprobada' },
  { materia: 'Educación Física',             docente: 'Prof. Ramos, G.',      b1: 9,    b2: 9,    estado: 'aprobada' },
  { materia: 'Informática Aplicada',         docente: 'Prof. Pérez, D.',      b1: 9,    b2: 10,   estado: 'aprobada' },
  { materia: 'Programación y Redes',         docente: 'Prof. Herrera, V.',    b1: 8,    b2: null, estado: 'proceso' },
  { materia: 'Construcción de Ciudadanía',   docente: 'Prof. Díaz, N.',       b1: null, b2: null, estado: 'pendiente' },
];

const ALERTAS = [
  {
    type: 'alert-red',
    iconType: 'red',
    iconSvg: `<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" style="color:#c0302b"><circle cx="8" cy="8" r="6.5" stroke-width="1.4"/><line x1="8" y1="5" x2="8" y2="9" stroke-width="1.8" stroke-linecap="round"/><circle cx="8" cy="11.2" r=".9" fill="currentColor"/></svg>`,
    title: 'Matemática — Desaprobado',
    desc: 'Promedio inferior a 6. Requiere inscripción a intensificación.',
    time: 'Hace 2 días',
  },
  {
    type: 'alert-yellow',
    iconType: 'yellow',
    iconSvg: `<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" style="color:#b07d0a"><circle cx="8" cy="8" r="6.5" stroke-width="1.4"/><path d="M8 5v4" stroke-width="1.8" stroke-linecap="round"/><circle cx="8" cy="11" r=".9" fill="currentColor"/></svg>`,
    title: 'Recuperatorio — Biología',
    desc: 'Fecha: 15 de junio de 2025. Turno tarde 14:00 hs.',
    time: 'Programado',
  },
  {
    type: 'alert-cyan',
    iconType: 'cyan',
    iconSvg: `<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" style="color:#0d6fa8"><rect x="2.5" y="3.5" width="11" height="9" rx="1.5" stroke-width="1.4"/><path d="M5 3.5V2M11 3.5V2" stroke-width="1.4" stroke-linecap="round"/><line x1="2.5" y1="6.5" x2="13.5" y2="6.5" stroke-width="1.2"/></svg>`,
    title: 'Intensificación activa',
    desc: 'Matemática — inscripta para diciembre 2025.',
    time: 'Vigente',
  },
  {
    type: 'alert-blue',
    iconType: 'blue',
    iconSvg: `<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" style="color:#2650a8"><path d="M8 2L2 6v8h4v-4h4v4h4V6L8 2z" stroke-width="1.4" stroke-linejoin="round"/></svg>`,
    title: 'Mensaje de preceptoría',
    desc: 'Reunión de padres: miércoles 4 de junio, 18:00 hs.',
    time: '1 hora atrás',
  },
];

const INFORMES = [
  {
    badge: 'urgente',
    badgeLabel: 'Urgente',
    title: 'Informe RITE — 3° Bimestre 2025',
    meta: 'Emitido: 20 mayo 2025 · Dirección',
    iconSvg: `<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="2.5" y="1.5" width="11" height="13" rx="1.5"/><line x1="5" y1="5.5" x2="11" y2="5.5"/><line x1="5" y1="8" x2="11" y2="8"/><line x1="5" y1="10.5" x2="8.5" y2="10.5"/></svg>`,
  },
  {
    badge: 'nuevo',
    badgeLabel: 'Nuevo',
    title: 'Seguimiento académico — Mayo',
    meta: 'Emitido: 28 mayo 2025 · Preceptoría',
    iconSvg: `<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M8 2L3 5v6l5 3 5-3V5L8 2z"/><polyline points="8,10 8,6 11,8"/></svg>`,
  },
  {
    badge: 'leido',
    badgeLabel: 'Leído',
    title: 'Observación de Docente — Física',
    meta: 'Emitido: 15 mayo 2025 · Prof. Sánchez',
    iconSvg: `<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="8" cy="8" r="2.5"/><path d="M1.5 8S4 3.5 8 3.5 14.5 8 14.5 8 12 12.5 8 12.5 1.5 8 1.5 8z"/></svg>`,
  },
];

function getNotaClass(nota) {
  if (nota === null) return 'nota-nd';
  if (nota >= 7) return 'nota-alta';
  if (nota >= 6) return 'nota-media';
  return 'nota-baja';
}

function formatNota(n) {
  return n === null ? '<span style="color:#9ca3af">—</span>' : n;
}

function getBadge(estado) {
  const map = {
    aprobada:       ['badge-aprobada',       'Aprobada'],
    proceso:        ['badge-proceso',        'En proceso'],
    desaprobada:    ['badge-desaprobada',    'Desaprobada'],
    intensificacion:['badge-intensificacion','Intensificación'],
    pendiente:      ['badge-pendiente',      'Pendiente'],
  };
  const [cls, label] = map[estado] || map.pendiente;
  return `<span class="badge ${cls}">${label}</span>`;
}

function renderTimeline() {
  const track = document.querySelector('.timeline-track');
  if (!track) return;

  let html = '';

  TRAYECTORIA.forEach((node, i) => {
    if (i > 0) {
      html += `<div class="timeline-connector ${node.connector || 'dashed'}"></div>`;
    }

    const currentBadge = node.current
      ? `<span class="node-current-badge">Cursando</span>`
      : '';

    html += `
      <div class="timeline-node${node.current ? ' node-current' : ''}" 
           data-year="${node.label}"
           data-status="${node.statusLabel}"
           data-avg="${node.avg}"
           tabindex="0"
           role="button"
           aria-label="${node.label}: ${node.statusLabel}${node.avg !== '—' ? ', promedio ' + node.avg : ''}">
        <div class="node-circle ${node.status}">
          ${currentBadge}
          <span class="node-year">${node.year}</span>
          <span class="node-avg">${node.avg !== '—' ? node.avg : '·'}</span>
        </div>
        <span class="node-label">${node.label}</span>
        <span class="node-status ${node.status}">${node.statusLabel}</span>
      </div>
    `;
  });

  track.innerHTML = html;

  const currentNode = track.querySelector('.node-current');
  if (currentNode) {
    setTimeout(() => {
      currentNode.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }, 400);
  }
}

function renderTable(filter = 'all') {
  const tbody = document.getElementById('evalBody') || document.getElementById('gradesBody');
  if (!tbody) return;

  const filtered = filter === 'all'
    ? MATERIAS
    : MATERIAS.filter(m => m.estado === filter);

  if (filtered.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" style="text-align:center; padding: 32px; color: var(--text-muted); font-size: 13px;">
          No hay materias en esta categoría.
        </td>
      </tr>`;
    return;
  }

  tbody.innerHTML = filtered.map(m => {
    const notas = [m.b1, m.b2].filter(n => n !== null);
    const prom =  notas.length === 0 ? null : notas.reduce((a, b) => a + b, 0) / notas.length;
    const promStr = prom !== null ? prom.toFixed(1) : '—';
    
    let promCls = 'prom-baja';
    if (prom >= 7) promCls = 'prom-alta';
    else if (prom >= 6) promCls = 'prom-media';

    return `
      <tr>
        <td class="td-materia">${m.materia}</td>
        <td class="td-docente">${m.docente}</td>
        <td class="td-nota ${getNotaClass(m.b1)}">${formatNota(m.b1)}</td>
        <td class="td-nota ${getNotaClass(m.b2)}">${formatNota(m.b2)}</td>
        <td class="td-prom ${promCls}">${promStr}</td>
        <td>${getBadge(m.estado)}</td>
      </tr>`;
  }).join('');
}

function renderAlerts() {
  // Soporta tanto 'alertList' como 'alertsList' según el HTML provisto
  const list = document.getElementById('alertList') || document.getElementById('alertsList');
  if (!list) return;

  list.innerHTML = ALERTAS.map(a => `
    <div class="alert-card ${a.type}">
      <div class="alert-icon ${a.iconType}">
        ${a.iconSvg}
      </div>
      <div class="alert-content">
        <div class="alert-title">${a.title}</div>
        <div class="alert-desc">${a.desc}</div>
        <div class="alert-time">${a.time}</div>
      </div>
    </div>
  `).join('');
}

function renderInformes() {
  const list = document.getElementById('informesList');
  if (!list) return;

  list.innerHTML = INFORMES.map(inf => `
    <div class="informe-item">
      <div class="informe-icon">${inf.iconSvg}</div>
      <div class="informe-body">
        <div class="informe-title">${inf.title}</div>
        <div class="informe-meta">${inf.meta}</div>
      </div>
      <span class="informe-badge ${inf.badge}">${inf.badgeLabel}</span>
    </div>
  `).join('');
}

function initNav() {
  const navItems = document.querySelectorAll('.nav-item');
  navItems.forEach(item => {
    item.addEventListener('click', function(e) {
      navItems.forEach(i => i.classList.remove('active'));
      this.classList.add('active');
    });
  });
}

function initFilters() {
  const btns = document.querySelectorAll('.filter-btn');
  btns.forEach(btn => {
    btn.addEventListener('click', function() {
      btns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      renderTable(this.dataset.filter);
    });
  });
}

function initMobileSidebar() {
  const toggle  = document.getElementById('menuToggle') || document.getElementById('hamburger');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  toggle?.addEventListener('click', openSidebar);
  overlay?.addEventListener('click', closeSidebar);

  document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', () => {
      if (window.innerWidth < 900) closeSidebar();
    });
  });
}

function initExport() {
  const btn = document.querySelector('.btn-export');
  if (!btn) return;
  btn.addEventListener('click', () => {
    btn.textContent = '⏳ Generando...';
    btn.disabled = true;
    setTimeout(() => {
      btn.disabled = false;
      btn.innerHTML = `
        <svg viewBox="0 0 16 16" width="14" height="14" fill="none">
          <path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <path d="M3 11v1a1 1 0 001 1h8a1 1 0 001-1v-1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        Exportar PDF`;
    }, 1800);
  });
}

function initTimelineKeyboard() {
  document.querySelector('.timeline-track')?.addEventListener('keydown', e => {
    if (e.key === 'Enter' || e.key === ' ') {
      const node = e.target.closest('.timeline-node');
      if (node) {
        e.preventDefault();
        showNodeTooltip(node);
      }
    }
  });
}

function showNodeTooltip(nodeEl) {
  nodeEl.querySelector('.node-circle').style.transform = 'scale(1.15)';
  setTimeout(() => {
    nodeEl.querySelector('.node-circle').style.transform = '';
  }, 300);
}

function initTimelineTouch() {
  const wrapper = document.querySelector('.timeline-scroll-wrapper');
  if (!wrapper) return;

  let isDown = false;
  let startX;
  let scrollLeft;

  wrapper.addEventListener('mousedown', e => {
    isDown = true;
    wrapper.style.cursor = 'grabbing';
    startX = e.pageX - wrapper.offsetLeft;
    scrollLeft = wrapper.scrollLeft;
  });
  wrapper.addEventListener('mouseleave', () => {
    isDown = false;
    wrapper.style.cursor = '';
  });
  wrapper.addEventListener('mouseup', () => {
    isDown = false;
    wrapper.style.cursor = '';
  });
  wrapper.addEventListener('mousemove', e => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - wrapper.offsetLeft;
    const walk = (x - startX) * 1.2;
    wrapper.scrollLeft = scrollLeft - walk;
  });
}

document.addEventListener('DOMContentLoaded', () => {
  renderTimeline();
  renderTable();
  renderAlerts();
  renderInformes();
  initNav();
  initFilters();
  initMobileSidebar();
  initExport();
  initTimelineKeyboard();
  initTimelineTouch();

  // Animación suave de filas de la tabla cargada
  setTimeout(() => {
    const rows = document.querySelectorAll('#evalBody tr') || document.querySelectorAll('#gradesBody tr');
    rows.forEach((row, i) => {
      row.style.opacity = '0';
      row.style.transform = 'translateY(6px)';
      row.style.transition = `opacity 250ms ease ${i * 40}ms, transform 250ms ease ${i * 40}ms`;
      requestAnimationFrame(() => {
        row.style.opacity = '1';
        row.style.transform = 'translateY(0)';
      });
    });
  }, 200);
});