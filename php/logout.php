<?php
session_start();

// Vaciamos todas las variables de la sesión actual
$_SESSION = array();

// Si la sesión usa cookies (lo normal), la matamos también en el navegador
if (ini_get("session_use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruimos la sesión en el servidor
session_destroy();

// Limpiado todo, mandamos al usuario de vuelta al login
header("Location: ../paginas/login.php");
exit;