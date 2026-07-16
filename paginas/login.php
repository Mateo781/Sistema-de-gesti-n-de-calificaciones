<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error_login   = $_SESSION['error_login']   ?? null;
$error_registro = $_SESSION['error_registro'] ?? null;
$exito_registro = $_SESSION['exito_registro'] ?? null;
$form_activo    = $_SESSION['form_activo']    ?? 'login';

unset($_SESSION['error_login'], $_SESSION['error_registro'], $_SESSION['exito_registro'], $_SESSION['form_activo']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SGC — Acceso al sistema</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/login.css">
</head>
<body>

<div class="auth-shell">

  <aside class="auth-brand">
    <div class="brand-logo">
      <div class="brand-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
        </svg>
      </div>
      <div class="brand-name">SGC<span>Sistema de Gestión de Calificaciones</span></div>
    </div>

    <div class="brand-body">
      <h1 class="brand-tagline">Gestión académica <em>clara y ordenada</em></h1>
      <p class="brand-sub">Seguimiento de calificaciones, trayectorias y períodos en un solo lugar para docentes, alumnos y administradores.</p>
    </div>

    <div class="brand-features">
      <div class="feat-item">
        <div class="feat-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
          </svg>
        </div>
        <span class="feat-text">Registro de calificaciones por materia y período</span>
      </div>
      <div class="feat-item">
        <div class="feat-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <span class="feat-text">Roles diferenciados: administrador, docente, alumno, director y jefe de área</span>
      </div>
      <div class="feat-item">
        <div class="feat-ico">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <span class="feat-text">Trayectorias, intensificaciones y recursadas</span>
      </div>
    </div>
  </aside>

  <main class="auth-panel">
    <div class="auth-form-wrap">

      <div class="auth-tabs">
        <button class="auth-tab <?= $form_activo==='login'?'active':'' ?>" onclick="switchTab('login')">Iniciar sesión</button>
        <button class="auth-tab <?= $form_activo==='registro'?'active':'' ?>" onclick="switchTab('registro')">Crear cuenta</button>
      </div>

      <div class="form-section <?= $form_activo==='login'?'active':'' ?>" id="sec-login">
        <div class="form-header">
          <h2 class="form-title">Bienvenido de vuelta</h2>
          <p class="form-subtitle">Ingresá tu email y contraseña para acceder al sistema.</p>
        </div>

        <?php if($error_login): ?>
        <div class="alert alert-error">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <?= htmlspecialchars($error_login) ?>
        </div>
        <?php endif; ?>

        <?php if($exito_registro): ?>
        <div class="alert alert-success">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          <?= htmlspecialchars($exito_registro) ?>
        </div>
        <?php endif; ?>

        <form action="../php/auth_login.php" method="POST">
          <div class="field-group">
            <div class="field">
              <label for="login_email">Email</label>
              <div class="input-wrap">
                <input class="field-input" type="email" id="login_email" name="email" placeholder="tu@email.com" required autocomplete="email">
                <span class="input-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
              </div>
            </div>
            <div class="field">
              <label for="login_pass">Contraseña</label>
              <div class="input-wrap">
                <input class="field-input" type="password" id="login_pass" name="password" placeholder="Tu contraseña" required autocomplete="current-password">
                <span class="input-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
              </div>
            </div>
          </div>

          <div class="forgot-row">
            <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
          </div>

          <button type="submit" class="btn-submit">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Ingresar al sistema
          </button>
        </form>

        <div class="auth-footer">
          ¿No tenés cuenta? <a href="#" onclick="switchTab('registro')" style="color:var(--accent);font-weight:600">Creá una acá</a>
        </div>
      </div>

      <div class="form-section <?= $form_activo==='registro'?'active':'' ?>" id="sec-registro">
        <div class="form-header">
          <h2 class="form-title">Crear cuenta</h2>
          <p class="form-subtitle">Completá tus datos para registrarte. Un administrador puede asignarte permisos adicionales.</p>
        </div>

        <?php if($error_registro): ?>
        <div class="alert alert-error">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <?= htmlspecialchars($error_registro) ?>
        </div>
        <?php endif; ?>

        <form action="../php/auth_registro.php" method="POST">
          <div class="field-group">
            <div class="field-row">
              <div class="field">
                <label for="reg_nombre">Nombre</label>
                <div class="input-wrap">
                  <input class="field-input" type="text" id="reg_nombre" name="nombre" placeholder="María" required>
                  <span class="input-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                </div>
              </div>
              <div class="field">
                <label for="reg_apellido">Apellido</label>
                <div class="input-wrap">
                  <input class="field-input" type="text" id="reg_apellido" name="apellido" placeholder="González" required>
                  <span class="input-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                </div>
              </div>
            </div>

            <div class="field">
              <label for="reg_dni">DNI</label>
              <div class="input-wrap">
                <input class="field-input" type="text" id="reg_dni" name="dni" placeholder="30123456" required maxlength="20">
                <span class="input-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>
              </div>
            </div>

            <div class="field">
              <label for="reg_email">Email</label>
              <div class="input-wrap">
                <input class="field-input" type="email" id="reg_email" name="email" placeholder="tu@email.com" required autocomplete="email">
                <span class="input-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
              </div>
            </div>

            <div class="field">
              <label for="reg_rol">Rol en el sistema</label>
              <select class="field-select" id="reg_rol" name="id_rol" required>
                <option value="" disabled selected>Seleccioná tu rol</option>
                <option value="2">Docente</option>
                <option value="3">Alumno</option>
                <option value="4">Preceptor</option>
                <!-- Ajuste de los campos solicitados -->
                <option value="5">Jefe de Área</option>
                <option value="6">Director</option>
                <!-- -------------------------------- -->
                <option value="7">Tutor</option>
                <option value="8">Administrador</option>
              </select>
            </div>

            <div class="field">
              <label for="reg_pass">Contraseña</label>
              <div class="input-wrap">
                <input class="field-input" type="password" id="reg_pass" name="password" placeholder="Mínimo 8 caracteres" required minlength="8" oninput="checkStrength(this.value)">
                <span class="input-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
              </div>
              <div class="pw-strength">
                <div class="pw-bar" id="bar1"></div>
                <div class="pw-bar" id="bar2"></div>
                <div class="pw-bar" id="bar3"></div>
                <div class="pw-bar" id="bar4"></div>
                <span class="pw-label" id="pw-label"></span>
              </div>
            </div>

            <div class="field">
              <label for="reg_pass2">Repetir contraseña</label>
              <div class="input-wrap">
                <input class="field-input" type="password" id="reg_pass2" name="password2" placeholder="Repetí tu contraseña" required>
                <span class="input-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
              </div>
            </div>
          </div>

          <div class="check-row">
            <input type="checkbox" id="terminos" name="terminos" required>
            <label for="terminos">Acepto los <a href="#">términos y condiciones</a> de uso del sistema</label>
          </div>

          <button type="submit" class="btn-submit">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            Crear mi cuenta
          </button>
        </form>

        <div class="auth-footer">
          ¿Ya tenés cuenta? <a href="#" onclick="switchTab('login')" style="color:var(--accent);font-weight:600">Iniciá sesión</a>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="../js/login.js"></script>
</body>
</html>