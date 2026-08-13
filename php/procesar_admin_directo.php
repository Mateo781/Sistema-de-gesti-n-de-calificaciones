<?php
session_start();
require_once "db.php";

// Filtro de seguridad: solo permitimos administradores (rol 1)
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    die("Acceso no autorizado.");
}

$id_admin = $_SESSION['usuario_id'];
$nombre = trim($_POST['nombre']);
$apellido = trim($_POST['apellido']);
$dni = trim($_POST['dni']);
$email = trim($_POST['email']);
$id_rol = intval($_POST['id_rol']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

try {
    // Metemos el nuevo usuario a la base de datos
    $stmtUser = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, dni, email, id_rol, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtUser->execute([$nombre, $apellido, $dni, $email, $id_rol, $password]);

    // Dejamos registro en el historial para la auditoría del director
    $detalle_log = "Se registró al usuario: $nombre $apellido (DNI: $dni) con el Rol ID: $id_rol.";
    $stmtLog = $pdo->prepare("INSERT INTO historial_cambios (id_admin, accion, detalle) VALUES (?, 'Alta de Usuario', ?)");
    $stmtLog->execute([$id_admin, $detalle_log]);

    // Volvemos al panel con bandera de éxito
    header("Location: ../index.php?p=admin_inicio&msg=Usuario creado e historial registrado con éxito");
} catch (PDOException $e) {
    // Si falla algo (ej: DNI duplicado), volvemos arrastrando el error
    header("Location: ../index.php?p=admin_inicio&err=Error al guardar: " . $e->getMessage());
}
exit;