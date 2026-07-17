<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: paginas/login.php");
    exit;
}

$id_rol = $_SESSION['usuario_rol'] ?? null;
$p = $_GET['p'] ?? 'inicio';

// Redirección inicial según el rol
if ($p == 'inicio') {
    if ($id_rol == 2) {
        $destino = 'prof_inicio';
    } elseif ($id_rol == 5) {
        $destino = 'dir_inicio';
    } elseif ($id_rol == 6) {
        $destino = 'jefe_inicio';
    } else {
        $destino = 'alum_inicio'; // Fallback para alumnos (3) u otros
    }
    
    header("Location: index.php?p=$destino");
    exit;
}

// Configuración de rutas del sistema
$pages = [
    // ==========================================
    // ALUMNO (Rol 3)
    // ==========================================
    'alum_inicio' => [
        'title' => ($id_rol == 3) ? 'RITE — Panel del Docente' : 'RITE — Panel del Alumno', // Mantenido de tu código original
        'css' => 'css/styles.css',
        'content' => 'paginas/alumno/alum_inicio.php',
        'js' => 'js/script.js',
        'roles_permitidos' => [3]
    ],
    'alum_actividades' => [
        'title' => 'RITE — Ver Calificaciones',
        'css' => 'css/alumnos/alum_actividades.css',
        'content' => 'paginas/alumno/alum_actividades.php',
        'js' => 'js/calificaciones.js',
        'roles_permitidos' => [3]
    ],
    'alum_calificaciones' => [
        'title' => 'RITE — Ver Calificaciones',
        'css' => 'css/calificaciones.css',
        'content' => 'paginas/alumno/alum_calificaciones.php',
        'js' => 'js/calificaciones.js',
        'roles_permitidos' => [3]
    ],
    'alum_situacion' => [
        'title' => 'RITE — Situación Académica',
        'css' => 'css/situacion.css',
        'content' => 'paginas/alumno/alum_situacion.php',
        'js' => 'js/situacion.js',
        'roles_permitidos' => [3]
    ],
    'alum_pendientes' => [
        'title' => 'RITE — Materias Pendientes',
        'css' => 'css/pendientes.css',
        'content' => 'paginas/alumno/alum_pendientes.php',
        'js' => 'js/pendientes.js',
        'roles_permitidos' => [3]
    ],
    'trayectoria' => [
        'title' => 'RITE — Trayectoria Educativa',
        'css' => 'css/trayectoria.css',
        'content' => 'paginas/alumno/alum_trayectoria.php',
        'js' => 'js/trayectoria.js',
        'roles_permitidos' => [3]
    ],
    'alertas' => [
        'title' => 'RITE — Alertas Académicas',
        'css' => 'css/styles.css',
        'content' => 'paginas/alumno/alum_alertas.php',
        'js' => 'js/script.js',
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
        'js' => 'js/script.js',
        'roles_permitidos' => [3]
    ],

    // ==========================================
    // PROFESOR (Rol 2)
    // ==========================================
    'prof_inicio' => [
        'title' => ($id_rol == 2) ? 'RITE — Panel del Docente' : 'RITE — Panel del Alumno', // Mantenido de tu código original
        'css' => 'css/profesor/dashboard.css',
        'content' => 'paginas/profesor/prof_dashboard.php',
        'js' => 'js/profesor/dashboard.js',
        'roles_permitidos' => [2]
    ],
    'prof_calificaciones' => [
        'title' => 'RITE — Carga de Calificaciones',
        'css' => 'css/profesor/prof_calificaciones.css',
        'content' => 'paginas/profesor/prof_calificaciones.php',
        'js' => 'js/calificaciones.js',
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
        'css' => 'css/profesor/prof_intensificaciones.css',
        'content' => 'paginas/profesor/intensificaciones.php',
        'js' => 'js/profesor/intensificaciones.js',
        'roles_permitidos' => [2]
    ],

    // ==========================================
    // DIRECTOR (Rol 5)
    // ==========================================
    'dir_inicio' => [
        'title' => 'RITE — Panel del Director',
        'css' => 'css/dashboardDirector-jefeArea.css',
        'content' => 'paginas/director/inicio_directo.php',
        'js' => 'js/director/dashboard.js',
        'roles_permitidos' => [5]
    ],

    // ==========================================
    // JEFE DE ÁREA (Rol 6)
    // ==========================================
    'jefe_inicio' => [
        'title' => 'RITE — Panel del Jefe de Área',
        'css' => 'css/dashboardDirector-jefeArea.css',
        'content' => 'paginas/jefeArea/jefe_area_inicio.php',
        'js' => 'js/jefeArea/dashboard.js',
        'roles_permitidos' => [6]
    ]
];

// Validar si la página existe
if (!array_key_exists($p, $pages)) {
    $p = 'inicio';
}

if (!isset($pages[$p])) {
    header("Location: index.php?p=alum_inicio");
    exit;
}

// Validar permisos de rol
$roles_permitidos = $pages[$p]['roles_permitidos'] ?? [];

if (!in_array($id_rol, $roles_permitidos)) {
    header("HTTP/1.1 403 Forbidden");
    echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>
            <h2>Acceso Denegado (Error 403)</h2>
            <p>No tienes los permisos requeridos para visualizar esta sección.</p>
            <a href='index.php'>Volver al inicio</a>
          </div>";
    exit;
}

$currentPage = $pages[$p];
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
        /* Fuerza a que el Sidebar y el Main Content se alineen lado a lado */
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
  <?php if ($currentPage['css'] !== 'css/sidebar.css'): ?>
    <link rel="stylesheet" href="<?php echo $currentPage['css']; ?>" />
  <?php endif; ?>
  
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet" />
</head>
<body>

  <?php require "./includes/sidebar.php"; ?>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="overlay" id="overlay"></div>

  <!-- Carga del contenido dinámico -->
  <?php include $currentPage['content']; ?>

  <script src="<?php echo $currentPage['js']; ?>"></script>
</body>
</html>