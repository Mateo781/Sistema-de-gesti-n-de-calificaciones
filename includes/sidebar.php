<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_rol   = $_SESSION['usuario_rol']       ?? null; 
$nombre   = $_SESSION['usuario_nombre']    ?? 'Usuario';
$apellido = $_SESSION['usuario_apellido'] ?? 'Invitado';
$dni      = $_SESSION['usuario_dni']       ?? '00.000.000';

$nombre_completo = trim("$nombre $apellido");

// Sacamos las iniciales para el avatar, respetando acentos y ñ
$palabras = explode(' ', $nombre_completo);
$iniciales = '';
foreach ($palabras as $p) {
    if (!empty($p)) {
        $iniciales .= mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8');
    }
}
$iniciales = mb_substr($iniciales, 0, 2, 'UTF-8');

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

      <!-- Administración -->
      <?php if ($id_rol == 1): ?>
        <span class="nav-section-label">Panel de Administración</span>
        <a href="index.php?p=admin_usuarios" class="nav-item <?= ($p === 'admin_usuarios') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
          Gestionar Usuarios
        </a>
        <a href="index.php?p=admin_cursos" class="nav-item <?= ($p === 'admin_cursos') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
          Cursos y Materias
        </a>
        <a href="index.php?p=admin_config" class="nav-item <?= ($p === 'admin_config') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><circle cx="12" cy="12" r="3" /></svg>
          Configuración del Ciclo
        </a>

      <!-- Docente -->
      <?php elseif ($id_rol == 2): ?>
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

      <!-- Alumno -->
      <?php elseif ($id_rol == 3): ?>
        <span class="nav-section-label">Panel Alumno</span>
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
        <a href="index.php?p=alum_pendientes" class="nav-item <?= ($p === 'alum_pendientes') ? 'active' : '' ?>">
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

      <!-- Preceptor -->
      <?php elseif ($id_rol == 5): ?>
        <span class="nav-section-label">Panel de Preceptoría</span>
        <a href="index.php?p=preceptor_alumnos" class="nav-item <?= ($p === 'preceptor_alumnos') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Situación Académica
        </a>
        <a href="index.php?p=preceptor_control_rite" class="nav-item <?= ($p === 'preceptor_control_rite') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Control y Cierre RITE
        </a>
        <a href="index.php?p=preceptor_trayectoria" class="nav-item <?= ($p === 'preceptor_trayectoria') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>
          Trayectoria Educativa
        </a>
        <a href="index.php?p=preceptor_reportes" class="nav-item <?= ($p === 'preceptor_reportes') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          Reportes y Boletines
        </a>

      <!-- Jefatura -->
      <?php elseif ($id_rol == 6): ?>
        <span class="nav-section-label">Panel de Jefatura</span>
        <a href="index.php?p=jefe_crear_criterio" class="nav-item <?= ($p === 'jefe_crear_criterio') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
          Proponer Criterios
        </a>
        <a href="index.php?p=jefe_historial_criterios" class="nav-item <?= ($p === 'jefe_historial_criterios') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
          Historial de Propuestas
        </a>

      <!-- Dirección -->
      <?php elseif ($id_rol == 7): ?>
        <span class="nav-section-label">Panel de Dirección</span>
        <a href="index.php?p=director_criterios" class="nav-item <?= ($p === 'director_criterios') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          Validar Criterios
        </a>
        <a href="index.php?p=director_auditoria" class="nav-item <?= ($p === 'director_auditoria') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
          Auditoría de Cambios
        </a>
        <!-- Padre/tutor -->
      <?php elseif ($id_rol == 4): ?>
        <span class="nav-section-label">Panel Tutor</span>
        <a href="index.php?p=tutor_sit_academica"
          class="nav-item <?= ($p === 'tutor_sit_academica') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5" fill="none"/><line x1="10" y1="6" x2="10" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Situacion Academica
        </a>
        <a href="index.php?p=tutor_materiasp"
          class="nav-item <?= ($p === 'tutor_materiasp') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M10 6v4l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Materias Pendientes
        </a>
        <a href="index.php?p=tutor_calificaciones"
          class="nav-item <?= ($p === 'tutor_calificaciones') ? 'active' : '' ?>">
          <svg class="nav-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            Calificaciones
        </a>
      <?php endif; ?>
    </nav>

    

    <!-- Perfil de usuario y cierre de sesión -->
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

    if (menuBtn && dropdown) {
        // Toggle del menú desplegable
        menuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isExpanded = menuBtn.getAttribute('aria-expanded') === 'true';
            menuBtn.setAttribute('aria-expanded', !isExpanded);
            dropdown.classList.toggle('show');
        });

        // Cerrar si clicamos afuera
        document.addEventListener('click', function() {
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                menuBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>