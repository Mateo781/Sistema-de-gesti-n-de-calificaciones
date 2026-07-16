<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/plain; charset=utf-8');

echo "Session ID: " . session_id() . "\n\n";
echo "Contenido de \$_SESSION:\n";
var_dump($_SESSION);

echo "\nCookies recibidas:\n";
var_dump($_COOKIE);