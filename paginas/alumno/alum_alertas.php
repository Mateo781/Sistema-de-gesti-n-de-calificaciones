<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (($_SESSION['usuario_rol'] ?? 0) != 3) {
    die("Acceso denegado.");
}

require_once __DIR__ . '/../../php/db.php';

$id_alumno = $_SESSION['usuario_id'] ?? 0;

try {
    $sql = "SELECT o.id, o.observacion, o.fecha, 
                   u_al.nombre AS alumno_nombre, u_al.apellido AS alumno_apellido,
                   u_prec.nombre AS preceptor_nombre, u_prec.apellido AS preceptor_apellido
            FROM observaciones o
            INNER JOIN usuarios u_al ON o.id_alumno = u_al.id
            INNER JOIN usuarios u_prec ON o.id_preceptor = u_prec.id
            WHERE o.id_alumno = :id_alumno
            ORDER BY o.fecha DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_alumno' => $id_alumno]);
    $alertas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar las alertas: " . $e->getMessage());
}

$total_alertas = count($alertas);
?>

<main class="main-content">
  <header class="top-header">
    <div class="header-left">
      <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
        <svg viewBox="0 0 20 20" width="20" height="20"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      </button>
      <div class="header-title-block">
        <h1 class="header-title">Mis Alertas</h1>
        <div class="header-breadcrumb">
          <span class="breadcrumb-year">Panel de Alumno</span>
          <span class="breadcrumb-sep">•</span>
          <span class="breadcrumb-division">Historial Personal</span>
        </div>
      </div>
    </div>
    <div class="header-right">
      <div class="header-meta">
        <span class="ciclo-badge">Ciclo 2026</span>
      </div>
    </div>
  </header>

  <div class="content-body">
      <aside class="right-panel">
        <section class="panel-section" aria-label="Alertas académicas">
          <div class="panel-section-header">
            <h3 class="panel-title">Alertas registradas</h3>
            <span class="alert-count" id="alertCount"><?= $total_alertas ?></span>
          </div>
          
          <div class="alerts-list" id="alertsList">
            <?php if ($total_alertas > 0): ?>
                <?php foreach ($alertas as $alerta): ?>
                    <div class="alert-item" style="border-left: 4px solid #e74c3c; padding: 10px; margin-bottom: 10px; background: #fdf2f2; border-radius: 4px;">
                        <div class="alert-item-header" style="display: flex; justify-content: space-between; font-size: 0.85em; color: #666; margin-bottom: 5px;">
                            <strong><?= htmlspecialchars($alerta['alumno_apellido'] . ', ' . $alerta['alumno_nombre']) ?></strong>
                            <span><?= date('d/m/Y H:i', strtotime($alerta['fecha'])) ?></span>
                        </div>
                        <p class="alert-text" style="margin: 0; font-size: 0.95em; color: #333;">
                            <?= htmlspecialchars($alerta['observacion']) ?>
                        </p>
                        <small style="display: block; margin-top: 5px; font-size: 0.75em; color: #888; text-align: right;">
                            Por: <?= htmlspecialchars($alerta['preceptor_apellido'] . ' ' . $alerta['preceptor_nombre']) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-alerts" style="color: #777; font-size: 0.9em; text-align: center; padding: 20px;">
                    No tenés alertas académicas registradas en el sistema.
                </p>
            <?php endif; ?>
          </div>
        </section>
      </aside>
  </div>
</main>