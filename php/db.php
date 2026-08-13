<?php
// php/db.php

$host = "localhost";
$dbname = "sgc_db"; 
$user = "root";
$pass = "";

// Intentamos colgar el sistema a la base usando PDO
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // Si rompe acá, matamos el script antes de que tire errores raros más adelante
    die("Error de conexión: " . $e->getMessage());
}