<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#1a2d5a" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <title>RITE — Panel del Alumno</title>
  <link rel="stylesheet" href="/dashboard/sgc/css/styles.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body>

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
          <span class="school-subtitle">Técnica N° 1 de Vicente López</span>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <span class="nav-section-label">Principal</span>
      <a href="#" class="nav-item active" data-section="inicio">
        <svg class="nav-icon" viewBox="0 0 20 20"><path d="M10 2L2 8v10h5v-5h6v5h5V8L10 2z" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linejoin="round"/></svg>
        Inicio
      </a>
      <a href="paginas/calificaciones.php" class="nav-item" data-section="calificaciones">
        <svg class="nav-icon" viewBox="0 0 20 20"><rect x="3" y="2" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.5" fill="none"/><line x1="7" y1="7" x2="13" y2="7" stroke="currentColor" stroke-width="1.5"/><line x1="7" y1="10" x2="13" y2="10" stroke="currentColor" stroke-width="1.5"/><line x1="7" y1="13" x2="11" y2="13" stroke="currentColor" stroke-width="1.5"/></svg>
        Ver calificaciones
      </a>
      <a href="#" class="nav-item" data-section="situacion">
        <svg class="nav-icon" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5" fill="none"/><line x1="10" y1="6" x2="10" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="10" cy="14" r="1" fill="currentColor"/></svg>
        Situación académica
      </a>
      <a href="paginas/materias-pendientes.php" class="nav-item" data-section="pendientes">
        <svg class="nav-icon" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M10 6v4l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        Materias pendientes
        <span class="nav-badge">3</span>
      </a>

      <span class="nav-section-label">Gestión</span>
      <a href="#" class="nav-item" data-section="intensificacion">
        <svg class="nav-icon" viewBox="0 0 20 20"><path d="M10 2v16M6 6l4-4 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/><rect x="4" y="10" width="12" height="8" rx="1" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
        Inscribirse a intensificación
      </a>
      <a href="#" class="nav-item" data-section="trayectoria">
        <svg class="nav-icon" viewBox="0 0 20 20"><circle cx="3" cy="10" r="2" stroke="currentColor" stroke-width="1.5" fill="none"/><circle cx="10" cy="10" r="2" stroke="currentColor" stroke-width="1.5" fill="none"/><circle cx="17" cy="10" r="2" stroke="currentColor" stroke-width="1.5" fill="none"/><line x1="5" y1="10" x2="8" y2="10" stroke="currentColor" stroke-width="1.5"/><line x1="12" y1="10" x2="15" y2="10" stroke="currentColor" stroke-width="1.5"/></svg>
        Trayectoria educativa
      </a>
      <a href="#" class="nav-item" data-section="informes">
        <svg class="nav-icon" viewBox="0 0 20 20"><rect x="3" y="2" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M7 6h6M7 9h6M7 12h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M13 14l1.5 1.5L17 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Informes RITE
      </a>
    </nav>

    <div class="sidebar-profile">
      <div class="profile-avatar">JM</div>
      <div class="profile-info">
        <span class="profile-name">Jheysmar Mendieta</span>
        <span class="profile-dni">DNI 95.382.269</span>
      </div>
      <button class="profile-menu-btn" aria-label="Opciones de perfil">
        <svg viewBox="0 0 16 16" width="16" height="16"><circle cx="8" cy="3" r="1.2" fill="currentColor"/><circle cx="8" cy="8" r="1.2" fill="currentColor"/><circle cx="8" cy="13" r="1.2" fill="currentColor"/></svg>
      </button>
    </div>
  </aside>
</body>