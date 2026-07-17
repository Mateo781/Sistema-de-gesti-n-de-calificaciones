'use strict';

/* ─── DATA ─────────────────────────────────────────────────── */

const EVALUACIONES = [
  { id:1,  materia:'Matemática',               tipo:'Parcial',        nota:5,  fecha:'2025-03-20', Cuatri:1, estado:'proceso'         },
  { id:2,  materia:'Matemática',               tipo:'Recuperatorio',  nota:6,  fecha:'2025-04-10', Cuatri:1, estado:'proceso'         },
  { id:3,  materia:'Matemática',               tipo:'Trabajo práctico',nota:5, fecha:'2025-05-05', Cuatri:2, estado:'intensificacion' },
  { id:4,  materia:'Lengua y Literatura',      tipo:'Parcial',        nota:8,  fecha:'2025-03-25', Cuatri:1, estado:'aprobada'        },
  { id:5,  materia:'Lengua y Literatura',      tipo:'Trabajo práctico',nota:9, fecha:'2025-04-22', Cuatri:2, estado:'aprobada'        },
  { id:6,  materia:'Historia',                 tipo:'Parcial',        nota:9,  fecha:'2025-03-18', Cuatri:1, estado:'aprobada'        },
  { id:7,  materia:'Historia',                 tipo:'Trabajo práctico',nota:8, fecha:'2025-05-12', Cuatri:2, estado:'aprobada'        },
  { id:8,  materia:'Geografía',                tipo:'Parcial',        nota:7,  fecha:'2025-03-28', Cuatri:1, estado:'aprobada'        },
  { id:9,  materia:'Geografía',                tipo:'Trabajo práctico',nota:7, fecha:'2025-04-30', Cuatri:2, estado:'aprobada'        },
  { id:10, materia:'Biología',                 tipo:'Parcial',        nota:4,  fecha:'2025-03-22', Cuatri:1, estado:'desaprobada'     },
  { id:11, materia:'Biología',                 tipo:'Recuperatorio',  nota:4,  fecha:'2025-04-15', Cuatri:1, estado:'desaprobada'     },
  { id:12, materia:'Física',                   tipo:'Parcial',        nota:7,  fecha:'2025-04-08', Cuatri:1, estado:'aprobada'        },
  { id:13, materia:'Física',                   tipo:'Trabajo práctico',nota:6, fecha:'2025-05-20', Cuatri:2, estado:'proceso'         },
  { id:14, materia:'Química',                  tipo:'Parcial',        nota:8,  fecha:'2025-03-17', Cuatri:1, estado:'aprobada'        },
  { id:15, materia:'Química',                  tipo:'Trabajo práctico',nota:9, fecha:'2025-05-23', Cuatri:2, estado:'aprobada'        },
  { id:16, materia:'Inglés',                   tipo:'Parcial',        nota:10, fecha:'2025-03-24', Cuatri:1, estado:'aprobada'        },
  { id:17, materia:'Inglés',                   tipo:'Oral',           nota:9,  fecha:'2025-04-28', Cuatri:2, estado:'aprobada'        },
  { id:18, materia:'Educación Física',         tipo:'Concepto',       nota:9,  fecha:'2025-04-01', Cuatri:1, estado:'aprobada'        },
  { id:19, materia:'Informática Aplicada',     tipo:'Trabajo práctico',nota:9, fecha:'2025-03-29', Cuatri:1, estado:'aprobada'        },
  { id:20, materia:'Informática Aplicada',     tipo:'Proyecto',       nota:10, fecha:'2025-05-15', Cuatri:2, estado:'aprobada'        },
  { id:21, materia:'Programación y Redes',     tipo:'Trabajo práctico',nota:8, fecha:'2025-04-17', Cuatri:1, estado:'aprobada'        },
  { id:22, materia:'Programación y Redes',     tipo:'Parcial',        nota:null,fecha:'2025-06-10',Cuatri:2, estado:'proceso'         },
  { id:23, materia:'Construcción de Ciudadanía',tipo:'Trabajo práctico',nota:null,fecha:'2025-06-20',Cuatri:2,estado:'proceso'        },
];

const ALERTS = [
  { type:'a-red', ico:'red', title:'Biología — Desaprobada', desc:'Promedio 4. Recuperatorio el 15 jun.', time:'Hace 2 días',
    svg:`<svg viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.4"/><line x1="7" y1="4.5" x2="7" y2="7.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="7" cy="9.5" r=".9" fill="currentColor"/></svg>` },
  { type:'a-yellow', ico:'yellow', title:'Recuperatorio pendiente', desc:'Matemática · 18 de junio, 14:00 hs.', time:'Programado',
    svg:`<svg viewBox="0 0 14 14" fill="none"><rect x="2" y="3" width="10" height="8.5" rx="1.5" stroke="currentColor" stroke-width="1.4"/><path d="M2 5.5h10" stroke="currentColor" stroke-width="1.2"/><path d="M5 2v2M9 2v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>` },
  { type:'a-cyan', ico:'cyan', title:'Intensificación activa', desc:'Matemática inscripta · dic. 2025.', time:'Vigente',
    svg:`<svg viewBox="0 0 14 14" fill="none"><path d="M7 2v9M4 7l3 4 3-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="2" y="11" width="10" height="1.5" rx=".75" stroke="currentColor" stroke-width="1.2"/></svg>` },
  { type:'a-blue', ico:'blue', title:'Reunión de padres', desc:'Miércoles 4 de junio · 18:00 hs.', time:'1 hora atrás',
    svg:`<svg viewBox="0 0 14 14" fill="none"><path d="M7 2l-5 3v6h3.5v-3h3v3H14V5L7 2z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>` },
];

const PROXIMAS = [
  { day:'10', mon:'Jun', materia:'Biología',          tipo:'Recuperatorio · 14:00 hs.',  chip:'recup'   },
  { day:'18', mon:'Jun', materia:'Matemática',        tipo:'Recuperatorio · 8:00 hs.',    chip:'recup'   },
  { day:'25', mon:'Jun', materia:'Física',            tipo:'Trabajo práctico grupal',      chip:'tp'      },
  { day:'02', mon:'Jul', materia:'Programación y Redes', tipo:'Parcial · 1° hora',         chip:'parcial' },
];

/* ─── STATE ────────────────────────────────────────────────── */

const state = {
  data: [...EVALUACIONES],
  filtered: [...EVALUACIONES],
  sortCol: 'fecha',
  sortDir: 'desc',
  page: 1,
  pageSize: 8,
  search: '',
  estado: '',
  Cuatri: '',
};

/* ─── HELPERS ──────────────────────────────────────────────── */

const $ = id => document.getElementById(id);

const fmtDate = iso => {
  const d = new Date(iso + 'T00:00:00');
  return d.toLocaleDateString('es-AR', { day:'2-digit', month:'short', year:'numeric' });
};

const notaClass = n => n === null ? '' : n >= 7 ? 'alta' : n >= 6 ? 'media' : 'baja';
const notaStr   = n => n === null ? '<span style="color:var(--t3)">—</span>' : n;

const TIPO_COLOR = {
  'Parcial':          '#e87c20',
  'Recuperatorio':    '#c0302b',
  'Trabajo práctico': '#2557d6',
  'Oral':             '#1f8c45',
  'Proyecto':         '#7c3aed',
  'Concepto':         '#0d6fa8',
};

function badgeHtml(estado) {
  const map = {
    aprobada:        ['badge-aprobada',       'Aprobada'],
    proceso:         ['badge-proceso',        'En proceso'],
    desaprobada:     ['badge-desaprobada',    'Desaprobada'],
    intensificacion: ['badge-intensificacion','Intensificación'],
  };
  const [cls, lbl] = map[estado] || ['badge-proceso', estado];
  return `<span class="badge ${cls}">${lbl}</span>`;
}

function CuatriLabel(t) {
  return `<span class="td-Cuatri">${t}° Cuatri.</span>`;
}

/* ─── FILTER + SORT ────────────────────────────────────────── */

function applyFilters() {
  let d = [...state.data];
  if (state.search) {
    const q = state.search.toLowerCase();
    d = d.filter(r => r.materia.toLowerCase().includes(q) || r.tipo.toLowerCase().includes(q));
  }
  if (state.estado) d = d.filter(r => r.estado === state.estado);
  if (state.Cuatri)   d = d.filter(r => String(r.Cuatri) === state.Cuatri);

  // Sistema de Ordenamiento
  d.sort((a, b) => {
    let va = a[state.sortCol];
    let vb = b[state.sortCol];
    if (state.sortCol === 'nota') {
      va = va ?? -1; vb = vb ?? -1;
    }
    if (va === null) return 1;
    if (vb === null) return -1;
    if (typeof va === 'string') return state.sortDir === 'asc' ? va.localeCompare(vb) : vb.localeCompare(va);
    return state.sortDir === 'asc' ? va - vb : vb - va;
  });

  state.filtered = d;
  state.page = 1;
}

/* ─── RENDER TABLE ─────────────────────────────────────────── */

function renderTable() {
  const tbody = $('evalBody');
  if (!tbody) return;

  const { filtered, page, pageSize } = state;
  const total  = filtered.length;
  const start  = (page - 1) * pageSize;
  const slice  = filtered.slice(start, start + pageSize);

  // Validaciones de existencia de elementos del DOM
  const rowCountEl = $('rowCount');
  if (rowCountEl) {
    rowCountEl.textContent = `${total} registro${total !== 1 ? 's' : ''} encontrado${total !== 1 ? 's' : ''}`;
  }

  const footerNoteEl = $('footerNote');
  if (footerNoteEl) {
    footerNoteEl.textContent = total > 0 ? `Mostrando ${start + 1}–${Math.min(start + pageSize, total)} de ${total}` : '';
  }

  if (total === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="td-empty" style="text-align:center; padding:32px;">
      <svg viewBox="0 0 40 40" width="36" height="36" fill="none" style="margin:0 auto 12px; display:block;"><rect x="8" y="6" width="24" height="28" rx="4" stroke="currentColor" stroke-width="1.8"/><line x1="14" y1="14" x2="26" y2="14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><line x1="14" y1="19" x2="26" y2="19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><line x1="14" y1="24" x2="20" y2="24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      No se encontraron registros con los filtros aplicados.
    </td></tr>`;
    renderPagination(0, 0);
    return;
  }

  tbody.innerHTML = slice.map((row, i) => {
    const tipoColor = TIPO_COLOR[row.tipo] || '#9aa0b0';
    return `
      <tr style="animation:fadeUp 200ms ${i * 30}ms both">
        <td class="td-materia">${row.materia}</td>
        <td>
          <span class="td-tipo">
            <span class="tipo-dot" style="background:${tipoColor}"></span>
            ${row.tipo}
          </span>
        </td>
        <td class="td-nota ${notaClass(row.nota)}">${notaStr(row.nota)}</td>
        <td class="td-fecha">${fmtDate(row.fecha)}</td>
        <td>${CuatriLabel(row.Cuatri)}</td>
        <td>${badgeHtml(row.estado)}</td>
      </tr>`;
  }).join('');

  renderPagination(total, page);
}

/* ─── PAGINATION ───────────────────────────────────────────── */

function renderPagination(total, current) {
  const el = $('pagination');
  if (!el) return;

  const pages = Math.ceil(total / state.pageSize);
  if (pages <= 1) { el.innerHTML = ''; return; }

  let html = `<button class="page-btn" data-p="${current - 1}" ${current === 1 ? 'disabled' : ''}>
    <svg viewBox="0 0 10 10" width="10" fill="none"><path d="M7 2L3 5l4 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
  </button>`;

  for (let p = 1; p <= pages; p++) {
    if (pages > 7 && p > 3 && p < pages - 2 && Math.abs(p - current) > 1) {
      if (p === 4) html += `<button class="page-btn" disabled style="border:none;background:none;color:var(--t3)">…</button>`;
      continue;
    }
    html += `<button class="page-btn ${p === current ? 'active' : ''}" data-p="${p}">${p}</button>`;
  }

  html += `<button class="page-btn" data-p="${current + 1}" ${current === pages ? 'disabled' : ''}>
    <svg viewBox="0 0 10 10" width="10" fill="none"><path d="M3 2l4 3-4 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
  </button>`;

  el.innerHTML = html;
  el.querySelectorAll('.page-btn[data-p]').forEach(btn => {
    btn.addEventListener('click', () => {
      state.page = +btn.dataset.p;
      renderTable();
      document.querySelector('.table-card')?.scrollIntoView({ behavior:'smooth', block:'nearest' });
    });
  });
}

/* ─── STAT CARDS ──────────────────────────────────────────── */

function renderStats() {
  const statAprob = $('statAprobadas');
  const statPend  = $('statPendientes');
  const statProm  = $('statPromedio');

  const aprobadas  = new Set(EVALUACIONES.filter(e => e.estado === 'aprobada').map(e => e.materia)).size;
  const pendientes = new Set(EVALUACIONES.filter(e => ['desaprobada','proceso','intensificacion'].includes(e.estado)).map(e => e.materia)).size;
  const notas      = EVALUACIONES.filter(e => e.nota !== null).map(e => e.nota);
  const prom       = notas.length ? (notas.reduce((a,b) => a+b, 0) / notas.length).toFixed(1) : '—';

  if (statAprob) statAprob.textContent = aprobadas;
  if (statPend)  statPend.textContent  = pendientes;
  if (statProm)  statProm.textContent  = prom;
}

/* ─── ALERTS ─────────────────────────────────────────────── */

function renderAlerts() {
  const el = $('alertList');
  if (!el) return;

  el.innerHTML = ALERTS.map(a => `
    <div class="alert-item ${a.type}">
      <div class="alert-ico ${a.ico}">${a.svg}</div>
      <div>
        <div class="alert-title">${a.title}</div>
        <div class="alert-desc">${a.desc}</div>
        <div class="alert-time">${a.time}</div>
      </div>
    </div>`).join('');
}

/* ─── PROXIMAS ───────────────────────────────────────────── */

function renderProximas() {
  const el = $('proxList');
  if (!el) return;

  el.innerHTML = PROXIMAS.map(p => `
    <div class="prox-item">
      <div class="prox-date">
        <span class="prox-day">${p.day}</span>
        <span class="prox-mon">${p.mon}</span>
      </div>
      <div class="prox-content">
        <div class="prox-materia">${p.materia}</div>
        <div class="prox-tipo">${p.tipo}</div>
      </div>
      <span class="prox-chip ${p.chip}">${p.chip === 'parcial' ? 'Parcial' : p.chip === 'tp' ? 'T.P.' : 'Recup.'}</span>
    </div>`).join('');
}

/* ─── PROMEDIOS LISTA ────────────────────────────────────── */

function renderPromedios() {
  const el = $('promediosList');
  if (!el) return;

  const byMateria = {};
  EVALUACIONES.forEach(e => {
    if (e.nota === null) return;
    if (!byMateria[e.materia]) byMateria[e.materia] = [];
    byMateria[e.materia].push(e.nota);
  });

  const arr = Object.entries(byMateria)
    .map(([mat, ns]) => ({ mat, avg: ns.reduce((a,b) => a+b,0)/ns.length }))
    .sort((a,b) => b.avg - a.avg)
    .slice(0, 6);

  el.innerHTML = arr.map(({ mat, avg }) => {
    const cls = avg >= 7 ? 'green' : avg >= 6 ? 'yellow' : 'red';
    const pct  = Math.round((avg / 10) * 100);
    const label = mat.length > 20 ? mat.slice(0,18) + '…' : mat;
    return `
      <div class="prom-row">
        <span class="prom-label" title="${mat}">${label}</span>
        <div class="prom-bar-wrap">
          <div class="prom-bar-fill prom-fill-${cls}" style="width:${pct}%"></div>
        </div>
        <span class="prom-val">${avg.toFixed(1)}</span>
      </div>`;
  }).join('');
}

/* ─── SORT ACTIONS ───────────────────────────────────────── */

function initSort() {
  document.querySelectorAll('.th-sortable').forEach(th => {
    th.addEventListener('click', () => {
      const col = th.dataset.col;
      if (state.sortCol === col) {
        state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
      } else {
        state.sortCol = col;
        state.sortDir = col === 'fecha' ? 'desc' : 'asc';
      }
      document.querySelectorAll('.th-sortable').forEach(t => t.classList.remove('asc','desc'));
      th.classList.add(state.sortDir);
      applyFilters();
      renderTable();
    });
  });
}

/* ─── CUATRIMESTRE TABS ───────────────────────────────────── */

function initCuatriTabs() {
  document.querySelectorAll('.Cuatri-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.Cuatri-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      
      // Corregido: dataset siempre mapea en minúsculas (data-cuatri -> dataset.cuatri)
      const targetCuatri = tab.dataset.cuatri || tab.dataset.Cuatri || 'all';
      state.Cuatri = targetCuatri === 'all' ? '' : targetCuatri;
      
      const selectEl = $('filterCuatrimestre');
      if (selectEl) selectEl.value = state.Cuatri;
      
      applyFilters();
      renderTable();
    });
  });
}

/* ─── INPUT FILTERS ──────────────────────────────────────── */

function initFilters() {
  let debounce;
  const searchInput = $('searchInput');
  const filterEstado = $('filterEstado');
  const filterCuatri = $('filterCuatrimestre');
  const filterReset = $('filterReset');

  searchInput?.addEventListener('input', e => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
      state.search = e.target.value.trim(); // ¡Ubicación del error corregido! Antes: .Cuatri()
      applyFilters();
      renderTable();
    }, 200);
  });

  filterEstado?.addEventListener('change', e => {
    state.estado = e.target.value;
    applyFilters();
    renderTable();
  });

  filterCuatri?.addEventListener('change', e => {
    state.Cuatri = e.target.value;
    document.querySelectorAll('.Cuatri-tab').forEach(t => {
      const tabVal = t.dataset.cuatri || t.dataset.Cuatri || 'all';
      t.classList.toggle('active', tabVal === (state.Cuatri || 'all'));
    });
    applyFilters();
    renderTable();
  });

  filterReset?.addEventListener('click', () => {
    state.search = ''; state.estado = ''; state.Cuatri = '';
    if (searchInput) searchInput.value = '';
    if (filterEstado) filterEstado.value = '';
    if (filterCuatri) filterCuatri.value = '';
    document.querySelectorAll('.Cuatri-tab').forEach(t => {
      const tabVal = t.dataset.cuatri || t.dataset.Cuatri || 'all';
      t.classList.toggle('active', tabVal === 'all');
    });
    applyFilters();
    renderTable();
  });
}

/* ─── FILTER BAR TOGGLE ─────────────────────────────────── */

function initFilterToggle() {
  const bar = $('filterBar');
  const btn = $('btnFilter');
  if (!bar || !btn) return;

  btn.addEventListener('click', () => {
    bar.classList.toggle('open');
    btn.classList.toggle('active');
    if (bar.classList.contains('open')) {
      $('searchInput')?.focus();
    }
  });
}

/* ─── EXPORT BUTTON ──────────────────────────────────────── */

function initExport() {
  const btn = $('btnExport');
  if (!btn) return;

  btn.addEventListener('click', function() {
    const orig = this.innerHTML;
    this.innerHTML = `<svg viewBox="0 0 16 16" width="13" height="13" fill="none"><path d="M8 2v8M5 7l3 3 3-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M2.5 12h11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg><span>Generando…</span>`;
    this.disabled = true;
    setTimeout(() => { this.innerHTML = orig; this.disabled = false; }, 1800);
  });
}

/* ─── MOBILE SIDEBAR ─────────────────────────────────────── */

function initMobileSidebar() {
  const sidebar = $('sidebar');
  const overlay = $('sidebarOverlay') || $('overlay');
  const ham     = $('menuToggle') || $('hamburger');
  if (!sidebar || !overlay) return;

  const open  = () => { 
    sidebar.classList.add('open'); 
    overlay.classList.add('open');
    overlay.classList.add('show'); 
    document.body.style.overflow = 'hidden'; 
  };
  const close = () => { 
    sidebar.classList.remove('open'); 
    overlay.classList.remove('open');
    overlay.classList.remove('show'); 
    document.body.style.overflow = ''; 
  };
  
  ham?.addEventListener('click', open);
  overlay?.addEventListener('click', close);
  
  document.querySelectorAll('.nav-link, .nav-item').forEach(l => {
    l.addEventListener('click', () => { if (window.innerWidth < 900) close(); });
  });
}

/* ─── INITIALIZATION ─────────────────────────────────────── */

function initDashboard() {
  renderStats();
  renderAlerts();
  renderProximas();
  renderPromedios();
  applyFilters();
  renderTable();
  initSort();
  initCuatriTabs();
  initFilters();
  initFilterToggle();
  initExport();
  initMobileSidebar();

  // Animación suave de barras de promedio diferida
  setTimeout(() => {
    document.querySelectorAll('.prom-bar-fill').forEach(el => {
      const w = el.style.width;
      el.style.width = '0';
      requestAnimationFrame(() => { 
        requestAnimationFrame(() => { el.style.width = w; }); 
      });
    });
  }, 300);
}

// Inicialización segura según el ciclo de vida del DOM
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initDashboard);
} else {
  initDashboard();
}