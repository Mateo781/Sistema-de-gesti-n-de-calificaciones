<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paginas/login.php");
    exit;
}

require_once __DIR__ . "/db.php";

$email    = trim($_POST['email']    ?? '');
$password =        $_POST['password'] ?? '';

// Validar campos vacíos
if ($email === '' || $password === '') {
    $_SESSION['error_login']  = 'Completá ambos campos para ingresar.';
    $_SESSION['form_activo']  = 'login';
    header('Location: ../paginas/login.php');
    exit;
}

// Buscar usuario
try {
    $stmt = $pdo->prepare(
        "SELECT id, id_rol, nombre, apellido, dni, email, password_hash, activo
         FROM usuarios
         WHERE email = :email
         LIMIT 1"
    );
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error_login'] = 'Error al conectar con la base de datos. Intentá más tarde.';
    $_SESSION['form_activo'] = 'login';
    header('Location: ../paginas/login.php');
    exit;
}

// Validar password
$passwordOk = false;
if ($usuario) {
    if (password_verify($password, $usuario['password_hash'])) {
        $passwordOk = true;
    }
    elseif ($password === $usuario['password_hash']) {
        $passwordOk = true;
    }
}

if (!$usuario || !$passwordOk) {
    $_SESSION['error_login'] = 'Email o contraseña incorrectos.';
    $_SESSION['form_activo'] = 'login';
    header('Location: ../paginas/login.php');
    exit;
}

// Validar estado de cuenta
if (!$usuario['activo']) {
    $_SESSION['error_login'] = 'Tu cuenta está deshabilitada. Contactá al administrador.';
    $_SESSION['form_activo'] = 'login';
    header('Location: ../paginas/login.php');
    exit;
}

session_regenerate_id(true);

// Cargar datos en la sesión
$_SESSION['usuario_id']       = $usuario['id'];
$_SESSION['usuario_nombre']   = $usuario['nombre'];
$_SESSION['usuario_apellido'] = $usuario['apellido'];
$_SESSION['usuario_email']    = $usuario['email'];
$_SESSION['usuario_rol']      = (int) $usuario['id_rol'];

// Formatear DNI con puntos
if (!empty($usuario['dni']) && is_numeric($usuario['dni'])) {
    $_SESSION['usuario_dni'] = number_format($usuario['dni'], 0, ',', '.');
} else {
    $_SESSION['usuario_dni'] = $usuario['dni'] ?? 'Sin DNI';
}

// Guardar en la tabla de auditoría
try {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $pdo->prepare(
        "INSERT INTO auditoria (id_usuario, accion, tabla_afectada, id_registro, ip_origen)
         VALUES (:uid, 'LOGIN', 'usuarios', :rid, :ip)"
    )->execute([
        ':uid' => $usuario['id'],
        ':rid' => $usuario['id'],
        ':ip'  => $ip,
    ]);
} catch (PDOException $e) {
    // Si falla la auditoría no frena el login
}

// Redirección por rol
// Se agregaron los roles 5, 6, 7 y 8 al mapa de destinos
$destinos = [
    1 => '../index.php', // Admin
    2 => '../index.php', // Docente
    3 => '../index.php', // Alumno
    4 => '../index.php', // Preceptor
    5 => '../index.php', // Jefe de Área
    6 => '../index.php', // Director
    7 => '../index.php', // Tutor
    8 => '../index.php', // Administrador
];

$destino = $destinos[$usuario['id_rol']] ?? '../index.php';
header('Location: ' . $destino);
exit;