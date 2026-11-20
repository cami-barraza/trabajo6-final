<?php
session_start();
require_once __DIR__ . "/config/Conexion.php";
require_once __DIR__ . "/includes/auth.php";

// solo el admin puede eliminar
requiereAdmin();


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_contenido.php");
    exit;
}

$id = $_POST["id"] ?? "";
if (!is_string($id) || !ctype_digit($id)) {
    $_SESSION["error"] = "El identificador no es válido.";
    header("Location: admin_contenido.php");
    exit;
}

try {
    $conexion->beginTransaction();

   
    $stmt = $conexion->prepare("DELETE FROM contenido_genero WHERE cont_ID = :id");
    $stmt->execute([":id" => $id]);

    $stmt = $conexion->prepare("DELETE FROM participacion WHERE cont_ID = :id");
    $stmt->execute([":id" => $id]);

    $stmt = $conexion->prepare("DELETE FROM pelicula WHERE peli_ID = :id");
    $stmt->execute([":id" => $id]);

    $stmt = $conexion->prepare("DELETE FROM serie WHERE serie_ID = :id");
    $stmt->execute([":id" => $id]);


    $stmt = $conexion->prepare("DELETE FROM contenido WHERE cont_ID = :id");
    $stmt->execute([":id" => $id]);
    $filas = $stmt->rowCount();

    $conexion->commit();

    if ($filas > 0) {
        $_SESSION["mensaje"] = "Se eliminó correctamente.";
    } else {
        $_SESSION["error"] = "No se encontró ese contenido.";
    }
} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    $_SESSION["error"] = "No se pudo eliminar, probá de nuevo.";
}

header("Location: admin_contenido.php");
exit;
