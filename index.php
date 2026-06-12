<?php
// Determinar la página actual
$p = isset($_GET['p']) ? $_GET['p'] : 'inicio';

// Configuración de páginas
$pages = [
    'inicio' => [
        'title' => 'RITE — Panel del Alumno',
        'css' => 'css/styles.css',
        'content' => 'paginas/inicio.php',
        'js' => 'js/script.js'
    ],
    'calificaciones' => [
        'title' => 'RITE — Ver Calificaciones',
        'css' => 'css/calificaciones.css',
        'content' => 'paginas/calificaciones.php',
        'js' => 'js/calificaciones.js'
    ],
    'situacion' => [
        'title' => 'RITE — Situación Académica',
        'css' => 'css/situacion.css',
        'content' => 'paginas/situacion.php',
        'js' => 'js/situacion.js'
    ],
    'pendientes' => [
        'title' => 'RITE — Materias Pendientes',
        'css' => 'css/materias-pendientes.css',
        'content' => 'paginas/materias-pendientes.php',
        'js' => 'js/materias-pendientes.js'
    ]
];

// Fallback por si la página no existe
if (!array_key_exists($p, $pages)) {
    $p = 'inicio';
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
  
  <!-- Usar la misma línea de estilos de css/styles.css como base global -->
  <link rel="stylesheet" href="css/styles.css" />
  
  <!-- Estilos específicos de la sección activa -->
  <?php if ($p !== 'inicio'): ?>
    <link rel="stylesheet" href="<?php echo $currentPage['css']; ?>" />
  <?php endif; ?>
  
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet" />
</head>
<body>

  <!-- Sidebar / Navbar -->
  <?php require "./partials/navbar.php"; ?>

  <!-- Mobile overlays para compatibilidad con distintos scripts -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="overlay" id="overlay"></div>

  <!-- Content of the current page -->
  <?php include $currentPage['content']; ?>

  <!-- Scripts -->
  <script src="<?php echo $currentPage['js']; ?>"></script>
</body>
</html>