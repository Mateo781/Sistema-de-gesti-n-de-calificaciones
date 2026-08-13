<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "php/db.php";

// Seguridad: Solo acceso para administradores (rol 1)
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 1) { 
    die("Acceso denegado."); 
}

$filtro_rol = isset($_GET['filtro_rol']) ? intval($_GET['filtro_rol']) : 0;

// Obtener roles para el select
$roles = $pdo->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Consulta de usuarios con filtro opcional
$sql = "SELECT u.*, r.nombre as nombre_rol 
        FROM usuarios u 
        JOIN roles r ON u.id_rol = r.id";
$params = [];

if ($filtro_rol > 0) {
    $sql .= " WHERE u.id_rol = ?";
    $params[] = $filtro_rol;
}
$sql .= " ORDER BY u.apellido ASC, u.nombre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="auth-panel-wrapper">
    <div class="auth-panel">
        <div class="form-header" style="flex-direction: row; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="form-title">Gestión de Usuarios</h2>
                <p class="form-subtitle">Administración de cuentas, altas, roles y credenciales del sistema.</p>
            </div>
            <form method="GET" action="index.php">
                <input type="hidden" name="p" value="admin_usuarios">
                <select name="filtro_rol" onchange="this.form.submit()" class="form-select" style="width: auto; min-width: 180px;">
                    <option value="0">Todos los Roles</option>
                    <?php foreach($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $filtro_rol == $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if(isset($_GET['msg'])): 
            $es_error = (strpos($_GET['msg'], 'Error') !== false);
        ?>
            <div class="alert <?= $es_error ? 'alert-danger' : 'alert-success' ?>">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="users-layout">
            <!-- Formulario Alta -->
            <div class="card-section">
                <h3 class="section-title">Nuevo Usuario</h3>
                <form action="php/procesar_usuarios_admin.php?accion=crear" method="POST" class="user-form">
                    <div class="field-group">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan" required class="form-input">
                    </div>
                    
                    <div class="field-group">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" id="apellido" name="apellido" placeholder="Ej. Pérez" required class="form-input">
                    </div>

                    <div class="field-group">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text" id="dni" name="dni" placeholder="Sin puntos" required class="form-input">
                    </div>

                    <div class="field-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" placeholder="correo@abc.com" required class="form-input">
                    </div>

                    <div class="field-group">
                        <label for="id_rol" class="form-label">Rol del Sistema</label>
                        <select id="id_rol" name="id_rol" required class="form-select">
                            <?php foreach($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-hint">* Contraseña inicial configurada por defecto como el número de DNI.</span>
                    </div>

                    <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                </form>
            </div>

            <!-- Tabla Listado -->
            <div class="card-section card-section-grow">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Usuario / DNI</th>
                                <th>Rol</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($usuarios) > 0): ?>
                                <?php foreach($usuarios as $user): ?>
                                    <tr>
                                        <td>
                                            <span class="user-name"><?= htmlspecialchars($user['apellido'] . ", " . $user['nombre']) ?></span>
                                            <span class="user-dni"><?= htmlspecialchars($user['dni']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-default"><?= htmlspecialchars($user['nombre_rol']) ?></span>
                                        </td>
                                        <td style="text-align: center; white-space: nowrap;">
                                            <a href="php/procesar_usuarios_admin.php?accion=blanquear&id=<?= $user['id'] ?>" onclick="return confirm('¿Reiniciar clave a DNI?')" class="action-link action-warning" title="Blanquear Clave">Clave</a>
                                            <a href="php/procesar_usuarios_admin.php?accion=eliminar&id=<?= $user['id'] ?>" onclick="return confirm('¿Dar de baja este usuario?')" class="action-link action-danger" title="Eliminar Usuario">Baja</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="table-empty">No se encontraron usuarios registrados con este filtro.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>