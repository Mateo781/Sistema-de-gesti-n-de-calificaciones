<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_rol   = $_SESSION['usuario_rol']      ?? null; 
$nombre   = $_SESSION['usuario_nombre']   ?? 'Usuario';
$apellido = $_SESSION['usuario_apellido'] ?? 'Invitado';
$dni      = $_SESSION['usuario_dni']      ?? '00.000.000';

$nombre_completo = trim("$nombre $apellido");

// Generar iniciales del usuario
$palabras = explode(' ', $nombre_completo);
$iniciales = '';
foreach ($palabras as $p) {
    if (!empty($p)) {
        $iniciales .= strtoupper($p[0]);
    }
}
$iniciales = substr($iniciales, 0, 2);

$p = $_GET['p'] ?? 'inicio';
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="school-logo">
        <div class="logo-icon">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
            <rect width="28" height="28" rx="8" fill="#3b6fd4"/>
            <path d="M14 5L5 10v2h18v-2L14 5z" fill="white"/>
            <rect x="7" y="13" width="3" height="7" fill="white"/>
            <rect x="12.5" y="13" width="3" height="7" fill="white"/>
            <rect x="18" y="13" width="3" height="7" fill="white"/>
            <rect x="5" y="20" width="18" height="2" rx="1" fill="white"/>
          </svg>
        </div>
        <div class="logo-text">
          <span class="school-name">E.E.S.T. N° 1</span>
          <span class="school-subtitle">Vicente López</span>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <span class="nav-section-label">Principal</span>
      <a href="index.php?p=inicio" class="nav-item <?= ($p === 'inicio') ? 'active' : '' ?>">
        <svg class="nav-icon" viewBox="0 0 20 20"><path d="M3 9l7-6 7 6v8a1 1 0 01-1 1H4a1 1 0 01-1-1V9z" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linejoin="round"/><path d="M7 18v-6h6v6" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linejoin="round"/></svg>
        Inicio
      </a>

      <?php if ($id_rol == 2): ?>
        <span class="nav-section-label">Panel Docente</span>
        <a href="index.php?p=prof_calificaciones" class="nav-item <?= ($p === 'prof_calificaciones') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
          Cargar Calificaciones
        </a>
        <a href="index.php?p=prof_evaluaciones" class="nav-item <?= ($p === 'prof_evaluaciones') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
          Planificar Actividades
        </a>
        <a href="index.php?p=prof_intensificaciones" class="nav-item <?= ($p === 'prof_intensificaciones') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          RITE e Intensificación
        </a>

      <?php elseif ($id_rol == 3): ?>
        <a href="index.php?p=alum_actividades" class="nav-item <?= ($p === 'alum_actividades') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><rect x="3" y="2" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.5" fill="none"/><line x1="7" y1="7" x2="13" y2="7" stroke="currentColor" stroke-width="1.5"/><line x1="7" y1="10" x2="13" y2="10" stroke="currentColor" stroke-width="1.5"/></svg>
          Ver actividades
        </a>
        <a href="index.php?p=alum_calificaciones" class="nav-item <?= ($p === 'alum_calificaciones') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><rect x="3" y="2" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.5" fill="none"/><line x1="7" y1="7" x2="13" y2="7" stroke="currentColor" stroke-width="1.5"/><line x1="7" y1="10" x2="13" y2="10" stroke="currentColor" stroke-width="1.5"/></svg>
          Ver calificaciones
        </a>
        <a href="index.php?p=alum_situacion" class="nav-item <?= ($p === 'alum_situacion') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5" fill="none"/><line x1="10" y1="6" x2="10" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Situación académica
        </a>
        <a href="index.php?p=-alum_pendientes" class="nav-item <?= ($p === 'alum_pendientes') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M10 6v4l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          Materias pendientes <span class="nav-badge">3</span>
        </a>

        <span class="nav-section-label">Gestión</span>
        <a href="index.php?p=insc_intensificacion" class="nav-item <?= ($p === 'insc_intensificacion') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><path d="M4 3h12a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2z" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
          Inscribirse a intensificación
        </a>
        <a href="index.php?p=trayectoria" class="nav-item <?= ($p === 'trayectoria') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><path d="M4 16a2 2 0 11-4 0 2 2 0 014 0zm12-4a2 2 0 11-4 0 2 2 0 014 0z" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
          Trayectoria educativa
        </a>

        <span class="nav-section-label">Alertas</span>
        <a href="index.php?p=alertas" class="nav-item <?= ($p === 'alertas') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><path d="M10 3a5 5 0 00-5 5v4l-2 3h14l-2-3V8a5 5 0 00-5-5z" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
          Alertas académicas
        </a>
        <a href="index.php?p=rite" class="nav-item <?= ($p === 'rite') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><path d="M7 3h6a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
          Informes RITE
        </a>
      <?php endif; ?>
    </nav>

    <div class="sidebar-profile" style="position: relative;">
      <div class="profile-avatar"><?= htmlspecialchars($iniciales) ?></div>
      <div class="profile-info">
        <span class="profile-name"><?= htmlspecialchars($nombre_completo) ?></span>
        <span class="profile-dni">DNI <?= htmlspecialchars($dni) ?></span>
      </div>
      <button class="profile-menu-btn" id="profileMenuBtn" aria-label="Opciones de perfil">
        <svg viewBox="0 0 16 16" width="16" height="16"><circle cx="8" cy="3" r="1.2" fill="currentColor"/><circle cx="8" cy="8" r="1.2" fill="currentColor"/><circle cx="8" cy="13" r="1.2" fill="currentColor"/></svg>
      </button>
      <div class="profile-dropdown" id="profileDropdown">
        <a href="php/logout.php" class="dropdown-item logout-link">Cerrar sesión</a>
      </div>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('profileMenuBtn');
    const dropdown = document.getElementById('profileDropdown');

    // Desplegar menú de perfil
    menuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isExpanded = menuBtn.getAttribute('aria-expanded') === 'true';
        menuBtn.setAttribute('aria-expanded', !isExpanded);
        dropdown.classList.toggle('show');
    });

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function() {
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
            menuBtn.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>