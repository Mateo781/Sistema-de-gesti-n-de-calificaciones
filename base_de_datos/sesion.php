<?php
session_start();
include ("./conexion.php");

$username = $_POST['name'] ?? '';
$password = $_POST['pass'] ?? '';

if(empty($username) || empty($password)){
    $_SESSION['error_login'] = "Rellenar todos los campos";
    header("Location:../paginas/inicio_sesion.php");
    exit();
}

try{
    $query = "SELECT `nombre`,`password_hash` FROM `usuarios` WHERE 1";
    $stmt = $pdo->prepare($query);
    $user= $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){
        if(password_verify($password, $user['password_hash'])){
            $_SESSION[user_name] = $user['name'];
            unset($_SESSION[error_login]);
            header("Location:../index.php");
            exit();
        }else {
            // Contraseña incorrecta
            $_SESSION['error_login'] = 'Contraseña incorrecta.';
            header("Location:../paginas/inicio_sesion.php");
            exit();
        }
    }else {
        // Usuario no encontrado
        $_SESSION['error_login'] = 'Usuario no encontrado.';
        header("Location:../paginas/inicio_sesion.php");
        exit();
    }
}catch (PDOException $e) {
    // Manejo de errores de conexión o consulta
    $_SESSION['error_login'] = "Error en la base de datos: " . $e->getMessage();
    header("Location:../paginas/inicio_sesion.php");
    exit();
}