<?php
session_start();
require_once "db.php";

// Filtro de seguridad: solo administradores (rol 1)
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] != 1 && $_SESSION['usuario_role'] != 1)) {
    die("Acceso denegado de forma directa.");
}

$id_admin = $_SESSION['usuario_id'];
$accion = $_GET['accion'] ?? '';

//  Crear un nuevo usuario usando el DNI como clave inicial
if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $dni = trim($_POST['dni']);
    $email = trim($_POST['email']);
    $id_rol = intval($_POST['id_rol']);
    
    $password_hash = password_hash($dni, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, dni, email, id_rol, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $apellido, $dni, $email, $id_rol, $password_hash]);

        // Guardamos el log para la auditoría del director
        $detalle_log = "Creó al usuario: $apellido, $nombre (DNI: $dni) asignándole el Rol ID: $id_rol.";
        $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Alta de Usuario', ?)");
        $stmtLog->execute([$id_admin, $detalle_log]);

        header("Location: ../index.php?p=admin_usuarios&msg=Usuario registrado correctamente");
    } catch (PDOException $e) {
        header("Location: ../index.php?p=admin_usuarios&msg=Error: El DNI o Correo ya se encuentran registrados.");
    }
    exit;
}

//  Blanquear clave y volverla a setear con el DNI
if ($accion === 'blanquear') {
    $id_usuario = intval($_GET['id']);

    // Buscamos los datos para dejar asentado de quién era la clave en el log
    $stmtUser = $pdo->prepare("SELECT nombre, apellido, dni FROM usuarios WHERE id = ?");
    $stmtUser->execute([$id_usuario]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $nuevo_pass = password_hash($user['dni'], PASSWORD_DEFAULT);
        
        $update = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        $update->execute([$nuevo_pass, $id_usuario]);

        $detalle_log = "Blanqueó la contraseña del usuario: " . $user['apellido'] . ", " . $user['nombre'] . " (Restaurada al DNI).";
        $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Reset de Password', ?)");
        $stmtLog->execute([$id_admin, $detalle_log]);

        header("Location: ../index.php?p=admin_usuarios&msg=Contraseña restablecida al DNI con éxito");
    } else {
        header("Location: ../index.php?p=admin_usuarios&msg=Error: Usuario no encontrado");
    }
    exit;
}

//  Eliminar definitivamente un usuario del sistema
if ($accion === 'eliminar') {
    $id_usuario = intval($_GET['id']);

    $stmtUser = $pdo->prepare("SELECT nombre, apellido, dni FROM usuarios WHERE id = ?");
    $stmtUser->execute([$id_usuario]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $delete = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $delete->execute([$id_usuario]);

        $detalle_log = "Eliminó definitivamente del sistema al usuario: " . $user['apellido'] . ", " . $user['nombre'] . " (DNI: " . $user['dni'] . ").";
        $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Baja de Usuario', ?)");
        $stmtLog->execute([$id_admin, $detalle_log]);

        header("Location: ../index.php?p=admin_usuarios&msg=Usuario dado de baja del sistema");
    } else {
        header("Location: ../index.php?p=admin_usuarios&msg=Error: Usuario no encontrado");
    }
    exit;
}