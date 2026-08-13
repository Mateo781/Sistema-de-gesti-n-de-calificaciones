<?php
session_start();

// Si no está logueado, patada al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: paginas/login.php");
    exit;
}

$id_rol = $_SESSION['usuario_rol'] ?? null;
$p = $_GET['p'] ?? 'inicio';

// Si entra al index seco, lo mandamos a su panel según el rol
if ($p == 'inicio') {
    switch ($id_rol) {
        case 1: $destino = 'admin_inicio'; break;       // Admin
        case 2: $destino = 'prof_inicio'; break;        // Docente
        case 3: $destino = 'alum_inicio'; break;        // Alumno
        case 4: $destino = 'tutor_inicio'; break;       // Tutor
        case 5: $destino = 'preceptor_inicio'; break;   // Preceptor
        case 6: $destino = 'jefe_inicio'; break;        // Jefe de Dep
        case 7: $destino = 'director_inicio'; break;    // Director
        default: $destino = 'alum_inicio'; break;       // Por las dudas, alumno
    }
    header("Location: index.php?p=$destino");
    exit;
}

// Mapeo de rutas de todo el sistema
$pages = [
    // --- SECCIÓN INICIOS DE CADA ROL ---
    'admin_inicio' => [
        'title' => 'RITE — Panel de Administración',
        'css' => 'css/adminstrador/inicio.css',
        'content' => 'paginas/administrador/admin_inicio.php',
        'roles_permitidos' => [1]
    ],
    'prof_inicio' => [
        'title' => 'RITE — Panel del Docente',
        'css' => 'css/styles.css',
        'content' => 'paginas/profesor/prof_inicio.php',
        'roles_permitidos' => [2]
    ],
    'alum_inicio' => [
        'title' => 'RITE — Panel del Alumno',
        'css' => 'css/suplente/styles.css',
        'content' => 'paginas/alumno/alum_inicio.php',
        'roles_permitidos' => [3]
    ],
    'tutor_inicio' => [
        'title' => 'RITE — Panel del Tutor',
        'css' => 'css/styles.css',
        'content' => 'paginas/tutor/tutor_inicio.php',
        'roles_permitidos' => [4]
    ],
    'preceptor_inicio' => [
        'title' => 'RITE — Panel de Preceptoría',
        'css' => 'css/styles.css',
        'content' => 'paginas/preceptor/preceptor_inicio.php',
        'roles_permitidos' => [5]
    ],
    'jefe_inicio' => [
        'title' => 'RITE — Jefatura de Departamento',
        'css' => 'css/jefe_departamento/inicio.css',
        'content' => 'paginas/jefe_departamento/jefe_inicio.php',
        'roles_permitidos' => [6]
    ],
    'director_inicio' => [
        'title' => 'RITE — Panel de Dirección',
        'css' => 'css/director/inicio.css',
        'content' => 'paginas/director/director_inicio.php', 
        'roles_permitidos' => [7]
    ],

    // --- SECCIÓN ADMINISTRADOR ---
    'admin_usuarios' => [
        'title' => 'RITE — Gestión de Usuarios',
        'css' => 'css/adminstrador/usuarios.css',
        'content' => 'paginas/administrador/admin_usuarios.php', 
        'roles_permitidos' => [1]
    ],
    'admin_cursos' => [
        'title' => 'RITE — Gestión de Cursos y Materias',
        'css' => 'css/adminstrador/cursos.css',
        'content' => 'paginas/administrador/admin_cursos.php', 
        'roles_permitidos' => [1]
    ],
    'admin_config' => [
        'title' => 'RITE — Configuración y Ciclo Lectivo',
        'css' => 'css/adminstrador/config.css',
        'content' => 'paginas/administrador/admin_config.php', 
        'roles_permitidos' => [1]
    ],

    // --- SECCIÓN ALUMNOS ---
    'alum_actividades' => [
        'title' => 'RITE — Ver Calificaciones',
        'css' => 'css/alumnos/alum_actividades.css',
        'content' => 'paginas/alumno/alum_actividades.php',
        'js' => 'js/calificaciones.js',
        'roles_permitidos' => [3]
    ],
    'alum_calificaciones' => [
        'title' => 'RITE — Ver Calificaciones',
        'css' => 'css/suplente/calificaciones.css',
        'content' => 'paginas/alumno/alum_calificaciones.php',
        'js' => 'js/calificaciones.js',
        'roles_permitidos' => [3]
    ],
    'alum_situacion' => [
        'title' => 'RITE — Situación Académica',
        'css' => 'css/suplente/situacion.css',
        'content' => 'paginas/alumno/alum_situacion.php',
        'js' => 'js/situacion.js',
        'roles_permitidos' => [3]
    ],
    'alum_pendientes' => [
        'title' => 'RITE — Materias Pendientes',
        'css' => 'css/suplente/materias-pendientes.css',
        'content' => 'paginas/alumno/alum_pendientes.php',
        'js' => 'js/pendientes.js',
        'roles_permitidos' => [3]
    ],
    'trayectoria' => [
        'title' => 'RITE — Trayectoria Educativa',
        'css' => 'css/suplente/trayectoria.css',
        'content' => 'paginas/alumno/alum_trayectoria.php',
        'js' => 'js/trayectoria.js',
        'roles_permitidos' => [3]
    ],
    'alertas' => [
        'title' => 'RITE — Alertas Académicas',
        'css' => 'css/styles.css',
        'content' => 'paginas/alumno/alum_alertas.php',
        'roles_permitidos' => [3]
    ],
    'proximas_evaluaciones' => [
        'title' => 'RITE — Próximas Evaluaciones',
        'css' => 'css/calificaciones.css',
        'content' => 'paginas/alumno/alum_proximas_evaluaciones.php',
        'js' => 'js/calificaciones.js',
        'roles_permitidos' => [3]
    ],
    'rite' => [
        'title' => 'RITE — Informes RITE',
        'css' => 'css/styles.css',
        'content' => 'paginas/alumno/alum_rite.php',
        'roles_permitidos' => [3]
    ],

    // --- SECCIÓN PROFESOR ---
    'prof_calificaciones' => [
        'title' => 'RITE — Carga de Calificaciones',
        'css' => 'css/profesor/prof_calificaciones.css',
        'content' => 'paginas/profesor/prof_calificaciones.php',
        'js' => 'js/profesor/profe_calificaciones.js',
        'roles_permitidos' => [2]
    ],
    'prof_evaluaciones' => [
        'title' => 'RITE — Planificar Actividades',
        'css' => 'css/profesor/prof_evaluaciones.css',
        'content' => 'paginas/profesor/prof_evaluaciones.php',
        'js' => 'js/calificaciones.js',
        'roles_permitidos' => [2]
    ],
    'prof_intensificaciones' => [
        'title' => 'RITE — Gestión de RITE e Intensificación',
        'css' => 'css/situacion.css',
        'content' => 'paginas/profesor/intensificaciones.php',
        'js' => 'js/situacion.js',
        'roles_permitidos' => [2]
    ],

    // --- SECCIÓN PRECEPTOR ---
    'preceptor_alumnos' => [
        'title' => 'Panel de Supervisión — Historial de Cambios',
        'css' => 'css/styles.css',
        'content' => 'paginas/preceptor/preceptor_alumnos.php', 
        'roles_permitidos' => [5]
    ],
    'preceptor_control_rite' => [
        'title' => 'Panel de Supervisión — Historial de Cambios',
        'css' => 'css/styles.css',
        'content' => 'paginas/preceptor/preceptor_control_rite.php', 
        'roles_permitidos' => [5]
    ],
    'preceptor_trayectoria' => [
        'title' => 'Panel de Supervisión — Historial de Cambios',
        'css' => 'css/styles.css',
        'content' => 'paginas/preceptor/preceptor_trayectoria.php', 
        'roles_permitidos' => [5]
    ],
    'preceptor_reportes' => [
        'title' => 'Panel de Supervisión — Historial de Cambios',
        'css' => 'css/styles.css',
        'content' => 'paginas/preceptor/preceptor_reportes.php', 
        'roles_permitidos' => [5]
    ],

    // --- SECCIÓN JEFE DE DEPARTAMENTO ---
    'jefe_crear_criterio' => [
        'title' => 'Panel de Supervisión — Historial de Cambios',
        'css' => 'css/jefe_departamento/crear_criterios.css',
        'content' => 'paginas/jefe_departamento/jefe_crear_criterio.php', 
        'roles_permitidos' => [6]
    ],
    'jefe_historial_criterios' => [
        'title' => 'Panel de Supervisión — Historial de Cambios',
        'css' => 'css/jefe_departamento/historias_criterios.css',
        'content' => 'paginas/jefe_departamento/jefe_historial_criterios.php', 
        'roles_permitidos' => [6]
    ],

    // --- SECCIÓN DIRECTOR ---
    'director_auditoria' => [
        'title' => 'Panel de Supervisión — Historial de Cambios',
        'css' => 'css/director/auditoria.css',
        'content' => 'paginas/director/director_auditoria.php', 
        'roles_permitidos' => [7]
    ],
    'director_criterios' => [
        'title' => 'Panel de Supervisión — Historial de Cambios',
        'css' => 'css/director/criterios.css',
        'content' => 'paginas/director/director_criterios.php', 
        'roles_permitidos' => [7]
    ],
    //Seccion padres/tutores
    'tutor_inicio' => [
        'title' => 'Inicio — tutor',
        'css' => 'css/padre-tutor/tutor_inicio.css',
        'content' => 'paginas/tutor/tutor_inicio.php',
        'roles_permitidos' => [4]
    ],

    'tutor_calificaciones' =>[
        'title' => 'Calificaciones — Tutor',
        'css' => 'css/padre-tutor/tutor_calificacion.css',
        'content' => 'paginas/tutor/tutor_calificaciones.php',
        'roles_permitidos' => [4]
    ],

    'tutor_materiasp' =>[
        'title' => 'Materias Pendientes — Tutor',
        'css' => 'css/padre-tutor/tutor_matpendientes.css',
        'content' => 'paginas/tutor/tutor_materiasp.php',
        'roles_permitidos' => [4]
    ],
    'tutor_sit_academica' =>[
        'title' => 'Situacion Academica — Tutor',
        'css' => 'css/padre-tutor/tutor_sit_academica.css',
        'content' => 'paginas/tutor/tutor_sit_academica.php',
        'roles_permitidos' => [4]
    ]
];

// Si meten cualquier fruta en la URL, lo mandamos al inicio de su rol
if (!array_key_exists($p, $pages)) {
    switch ($id_rol) {
        case 1: $destino_fallback = 'admin_inicio'; break;
        case 2: $destino_fallback = 'prof_inicio'; break;
        case 3: $destino_fallback = 'alum_inicio'; break;
        case 4: $destino_fallback = 'tutor_inicio'; break;
        case 5: $destino_fallback = 'preceptor_inicio'; break;
        case 6: $destino_fallback = 'jefe_inicio'; break;
        case 7: $destino_fallback = 'director_inicio'; break;
        default: $destino_fallback = 'alum_inicio'; break;
    }
    header("Location: index.php?p=$destino_fallback");
    exit;
}

$currentPage = $pages[$p];

// Control de roles por página (que no se filtren)
$roles_permitidos = $currentPage['roles_permitidos'] ?? [];
if (!in_array($id_rol, $roles_permitidos)) {
    header("HTTP/1.1 403 Forbidden");
    echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>
            <h2>Acceso Denegado (Error 403)</h2>
            <p>No tienes los permisos requeridos para visualizar esta sección.</p>
            <a href='index.php?p=alum_inicio'>Volver al inicio</a>
          </div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#1a2d5a" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <title><?php echo $currentPage['title']; ?></title>
  
  <link rel="stylesheet" href="css/sidebar.css" />
  <style>
      body, .app-container {
          display: flex;
          flex-direction: row;
          min-height: 100vh;
          width: 100%;
          margin: 0;
      }
      .main-content {
          flex: 1;
          min-width: 0;
          display: flex;
          flex-direction: column;
      }
  </style>
  
  <!-- Evitamos duplicar el sidebar.css si justo coincide -->
  <?php if ($currentPage['css'] !== 'css/sidebar.css'): ?>
    <link rel="stylesheet" href="<?php echo $currentPage['css']; ?>" />
  <?php endif; ?>
  
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet" />
</head>
<body>

  <?php require "./includes/sidebar.php"; ?>

  <!-- Overlays para mobile y menús flotantes -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="overlay" id="overlay"></div>

  <!-- Contenedor principal para respetar el flex row con el sidebar -->
  <div class="main-content">
      <!-- Aquí se incluye el topbar que me pasaste -->
      <?php include "./includes/topbar.php"; ?>

      <!-- Inyección dinámica del contenido de la página -->
      <?php include $currentPage['content']; ?>
  </div>

  <script src="<?php echo $currentPage['js']; ?>"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        if (sidebar && sidebarOverlay) {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        }
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', toggleSidebar); // Cierra al hacer clic fuera del menú
    }
});
</script>  

</body>
</html>