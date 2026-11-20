<?php
session_start();
require_once __DIR__ . "/config/Conexion.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/contenido_funciones.php";

// solo el admin puede agregar
requiereAdmin();

// géneros para armar los checkboxes
$generos = $conexion->query("SELECT gen_ID, gen_Nombre FROM genero ORDER BY gen_Nombre")->fetchAll();

$modo = "alta";
$errores = [];
$titulo = "";
$anio = "";
$sinopsis = "";
$tipo = "Pelicula";
$duracion = "";
$temporadas = "";
$episodios = "";
$duracionProm = "";
$generosElegidos = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $anio = trim($_POST["anio"] ?? "");
    $sinopsis = trim($_POST["sinopsis"] ?? "");
    $tipo = $_POST["tipo"] ?? "";
    $duracion = trim($_POST["duracion"] ?? "");
    $temporadas = trim($_POST["temporadas"] ?? "");
    $episodios = trim($_POST["episodios"] ?? "");
    $duracionProm = trim($_POST["duracion_prom"] ?? "");

    $generosElegidos = $_POST["generos"] ?? [];
    if (!is_array($generosElegidos)) {
        $generosElegidos = [];
    }
    $generosElegidos = array_map("intval", $generosElegidos);

    $errores = validarContenido($titulo, $anio, $sinopsis, $tipo, $duracion, $temporadas, $episodios, $duracionProm, $generosElegidos, $generos);

    if (empty($errores)) {
        try {
            // son varias tablas, así que se guarda todo junto o no se guarda nada
            $conexion->beginTransaction();

            $sql = "INSERT INTO contenido (cont_Titulo, cont_Anio, cont_Sinopsis, cont_Tipo)
                    VALUES (:titulo, :anio, :sinopsis, :tipo)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ":titulo" => $titulo,
                ":anio" => $anio,
                ":sinopsis" => ($sinopsis === "") ? null : $sinopsis,
                ":tipo" => $tipo
            ]);

            // id que le asignó la base al contenido nuevo
            $idNuevo = $conexion->lastInsertId();

            if ($tipo === "Pelicula") {
                $stmt = $conexion->prepare("INSERT INTO pelicula (peli_ID, peli_Duracion) VALUES (:id, :duracion)");
                $stmt->execute([":id" => $idNuevo, ":duracion" => $duracion]);
            } else {
                $sql = "INSERT INTO serie (serie_ID, serie_Temporadas, serie_EpisodiosTot, serie_DuracionProm)
                        VALUES (:id, :temporadas, :episodios, :prom)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    ":id" => $idNuevo,
                    ":temporadas" => $temporadas,
                    ":episodios" => $episodios,
                    ":prom" => $duracionProm
                ]);
            }

            guardarGeneros($conexion, $idNuevo, $generosElegidos);

            $conexion->commit();

            $_SESSION["mensaje"] = "Se agregó \"" . $titulo . "\" al catálogo.";
            header("Location: admin_contenido.php");
            exit;
        } catch (PDOException $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            $errores[] = "No se pudo guardar, probá de nuevo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinePlus - Agregar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include __DIR__ . "/includes/nav.php"; ?>

    <main>
        <h1>Agregar película o serie</h1>

        <?php if (!empty($errores)) { ?>
            <div class="errores">
                <ul>
                    <?php foreach ($errores as $error) { ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <?php include __DIR__ . "/includes/form_contenido.php"; ?>

        <p><a href="admin_contenido.php">Volver al listado</a></p>
    </main>
</body>
</html>
