<?php 
$host = 'localhost';
$usuario = 'root';
$contrasena = ''; 
$basedatos = 'sistema_calificaciones';

$conexion = new mysqli($host, $usuario, $contrasena, $basedatos);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$basedatos;charset=utf8mb4", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
?>