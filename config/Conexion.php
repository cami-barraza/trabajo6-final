<?php
$servidor = "localhost";
$base = "cineplus";
$usuario_bd = "root";
$clave_bd = "";

try {
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$base;charset=utf8mb4",
        $usuario_bd,
        $clave_bd,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    // no mostramos $e->getMessage() para no exponer datos de la conexión
    die("No se pudo conectar a la base de datos.");
}
