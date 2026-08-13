<?php
session_start();

// Solo entramos acá si vienen por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paginas/login.php");
    exit;
}

require_once __DIR__ . "/db.php";

$email    = trim($_POST['email']    ?? '');
$password =       $_POST['password'] ?? '';

// Filtro rápido por si mandan campos vacíos
if ($email === '' || $password === '') {
    $_SESSION['error_login']  = 'Completá ambos campos para ingresar.';
    $_SESSION['form_activo']  = 'login';
    header('Location: ../paginas/login.php');
    exit;
}

// Intentamos traer al usuario por email
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

// Verificamos la contraseña (mantiene el fallback por si hay claves en texto plano)
$passwordOk = false;
if ($usuario) {
    if (password_verify($password, $usuario['password_hash'])) {
        $passwordOk = true;
    }
    elseif ($password === $usuario['password_hash']) {
        $passwordOk = true;
    }
}

// Si no existe o la clave no va, rebote
if (!$usuario || !$passwordOk) {
    $_SESSION['error_login'] = 'Email o contraseña incorrectos.';
    $_SESSION['form_activo'] = 'login';
    header('Location: ../paginas/login.php');
    exit;
}

// Chequeo de cuenta activa antes de armar la sesión
if (!$usuario['activo']) {
    $_SESSION['error_login'] = 'Tu cuenta está deshabilitada. Contactá al administrador.';
    $_SESSION['form_activo'] = 'login';
    header('Location: ../paginas/login.php');
    exit;
}

// Cambiamos el ID de sesión por seguridad
session_regenerate_id(true);

// Guardamos los datos clave en la sesión
$_SESSION['usuario_id']       = $usuario['id'];
$_SESSION['usuario_nombre']   = $usuario['nombre'];
$_SESSION['usuario_apellido'] = $usuario['apellido'];
$_SESSION['usuario_email']    = $usuario['email'];
$_SESSION['usuario_rol']      = (int) $usuario['id_rol'];

// Le damos formato lindo al DNI (XX.XXX.XXX)
if (!empty($usuario['dni']) && is_numeric($usuario['dni'])) {
    $_SESSION['usuario_dni'] = number_format($usuario['dni'], 0, ',', '.');
} else {
    $_SESSION['usuario_dni'] = $usuario['dni'] ?? 'Sin DNI';
}

// Registro en la tabla de auditoría
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
    // Si la auditoría falla, no pisamos el login; dejamos pasar igual
}

// Derivación según el rol del usuario
$destinos = [
    1 => '../index.php', 
    2 => '../index.php',
    3 => '../index.php',
    4 => '../index.php',
    5 => '../index.php', // Preceptor
    6 => '../index.php', // Jefe de departamento
    7 => '../index.php', // Director
];

$destino = $destinos[$usuario['id_rol']] ?? '../index.php';
header('Location: ' . $destino);
exit;