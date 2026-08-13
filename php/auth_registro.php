<?php
session_start();

// Solo procesamos si entran por POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../paginas/login.php");
    exit;
}

require_once __DIR__ . "/db.php";

$nombre    = trim($_POST["nombre"] ?? "");
$apellido  = trim($_POST["apellido"] ?? "");
$dni       = trim($_POST["dni"] ?? "");
$email     = strtolower(trim($_POST["email"] ?? ""));
$id_rol    = intval($_POST["id_rol"] ?? 0);
$password  = $_POST["password"] ?? "";
$password2 = $_POST["password2"] ?? "";
$terminos  = isset($_POST["terminos"]);

$errores = [];

// Validaciones básicas de los inputs
if (empty($nombre)) $errores[] = "Ingresá tu nombre.";
if (empty($apellido)) $errores[] = "Ingresá tu apellido.";
if (!preg_match('/^[0-9]{7,12}$/', $dni)) $errores[] = "El DNI no es válido.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "El email no es válido.";

// Mapeo dinámico para aceptar los 7 roles nuevos del sistema
if (!in_array($id_rol, [1, 2, 3, 4, 5, 6, 7])) $errores[] = "Seleccioná un rol válido.";

if (strlen($password) < 8) $errores[] = "La contraseña debe tener al menos 8 caracteres.";
if ($password != $password2) $errores[] = "Las contraseñas no coinciden.";
if (!$terminos) $errores[] = "Debés aceptar los términos.";

// Si salta alguna validación, volvemos con los errores
if (!empty($errores)) {
    $_SESSION["error_registro"] = implode("<br>", $errores);
    $_SESSION["form_activo"] = "registro";
    header("Location: ../paginas/login.php");
    exit;
}

// Controlamos que el email o el DNI no estén duplicados en la base
try {
    $stmt = $pdo->prepare("
        SELECT id, email, dni
        FROM usuarios
        WHERE email = :email OR dni = :dni
        LIMIT 1
    ");
    $stmt->execute([
        ":email" => $email,
        ":dni" => $dni
    ]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        if ($usuario["email"] == $email) {
            $_SESSION["error_registro"] = "Ese correo ya está registrado.";
            $_SESSION["form_activo"] = "registro";
            header("Location: ../paginas/login.php");
            exit;
        }
        if ($usuario["dni"] == $dni) {
            $_SESSION["error_registro"] = "Ese DNI ya está registrado.";
            $_SESSION["form_activo"] = "registro";
            header("Location: ../paginas/login.php");
            exit;
        }
    }
} catch (PDOException $e) {
    $_SESSION["error_registro"] = "Error al verificar los datos.";
    $_SESSION["form_activo"] = "registro";
    header("Location: ../paginas/login.php");
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Bloque transaccional para meter usuario y auditoría juntos
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO usuarios (id_rol, nombre, apellido, dni, email, password_hash, activo)
        VALUES (:rol, :nombre, :apellido, :dni, :email, :pass, 1)
    ");
    $stmt->execute([
        ":rol" => $id_rol,
        ":nombre" => $nombre,
        ":apellido" => $apellido,
        ":dni" => $dni,
        ":email" => $email,
        ":pass" => $password_hash
    ]);

    $id_usuario = $pdo->lastInsertId();

    // Guardamos los datos nuevos en formato JSON para la auditoría
    $stmt = $pdo->prepare("
        INSERT INTO auditoria (id_usuario, accion, tabla_afectada, id_registro, valor_nuevo, ip_origen)
        VALUES (:usuario, 'REGISTRO', 'usuarios', :registro, :valor, :ip)
    ");
    $stmt->execute([
        ":usuario" => $id_usuario,
        ":registro" => $id_usuario,
        ":valor" => json_encode([
            "nombre" => $nombre,
            "apellido" => $apellido,
            "email" => $email,
            "rol" => $id_rol
        ]),
        ":ip" => $_SERVER["REMOTE_ADDR"] ?? null
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    die($e->getMessage());
}

$_SESSION["exito_registro"] = "Cuenta creada correctamente. Ahora iniciá sesión.";
$_SESSION["form_activo"] = "login";
header("Location: ../paginas/login.php");
exit;