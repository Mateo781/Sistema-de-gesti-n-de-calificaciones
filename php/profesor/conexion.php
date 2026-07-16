<?php
// Iniciar sesión si no está iniciada (necesario para ajax/*.php que se llaman por separado)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexión a la base de datos real del proyecto
// Ajustá la ruta si tu php/db.php está en otro lugar relativo a este archivo.
require_once __DIR__ . './php/db.php';