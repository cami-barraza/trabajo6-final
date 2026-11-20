<?php
session_start();
require_once __DIR__ . "/config/Conexion.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/contenido_funciones.php";

// solo el admin puede modificar
requiereAdmin();

// el id viene por la URL (modificar.php?id=3)
$id = $_GET["id"] ?? "";
if (!is_string($id) || !ctype_digit($id)) {
    $_SESSION["error"] = "El identificador no es válido.";
    header("Location: admin_contenido.php");
    exit;
}

// busco el contenido con sus datos de película o serie
$sql = "SELECT c.*, p.peli_Duracion, s.serie_Temporadas, s.serie_EpisodiosTot, s.serie_DuracionProm
        FROM contenido c
        LEFT JOIN pelicula p ON p.peli_ID = c.cont_ID
        LEFT JOIN serie s ON s.serie_ID = c.cont_ID
        WHERE c.cont_ID = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([":id" => $id]);
$c = $stmt->fetch();

if (!$c) {
    $_SESSION["error"] = "No existe ese contenido.";
    header("Location: admin_contenido.php");
    exit;
}

$generos = $conexion->query("SELECT gen_ID, gen_Nombre FROM genero ORDER BY gen_Nombre")->fetchAll();

// los datos que ya tenía, para mostrarlos en el formulario
$modo = "modificar";
$errores = [];
$tipo = $c["cont_Tipo"];   // el tipo no se puede cambiar
$titulo = $c["cont_Titulo"];
$anio = $c["cont_Anio"];
$sinopsis = $c["cont_Sinopsis"] ?? "";
$duracion = $c["peli_Duracion"] ?? "";
$temporadas = $c["serie_Temporadas"] ?? "";
$episodios = $c["serie_EpisodiosTot"] ?? "";
$duracionProm = $c["serie_DuracionProm"] ?? "";

$stmt = $conexion->prepare("SELECT gen_ID FROM contenido_genero WHERE cont_ID = :id");
$stmt->execute([":id" => $id]);
$generosElegidos = array_map("intval", array_column($stmt->fetchAll(), "gen_ID"));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $anio = trim($_POST["anio"] ?? "");
    $sinopsis = trim($_POST["sinopsis"] ?? "");
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
            $conexion->beginTransaction();

            $sql = "UPDATE contenido
                    SET cont_Titulo = :titulo, cont_Anio = :anio, cont_Sinopsis = :sinopsis
                    WHERE cont_ID = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ":titulo" => $titulo,
                ":anio" => $anio,
                ":sinopsis" => ($sinopsis === "") ? null : $sinopsis,
                ":id" => $id
            ]);

            if ($tipo === "Pelicula") {
                $stmt = $conexion->prepare("UPDATE pelicula SET peli_Duracion = :duracion WHERE peli_ID = :id");
                $stmt->execute([":duracion" => $duracion, ":id" => $id]);
            } else {
                $sql = "UPDATE serie
                        SET serie_Temporadas = :temporadas, serie_EpisodiosTot = :episodios, serie_DuracionProm = :prom
                        WHERE serie_ID = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    ":temporadas" => $temporadas,
                    ":episodios" => $episodios,
                    ":prom" => $duracionProm,
                    ":id" => $id
                ]);
            }

            // los géneros: borro los que tenía y guardo los que quedaron tildados
            $stmt = $conexion->prepare("DELETE FROM contenido_genero WHERE cont_ID = :id");
            $stmt->execute([":id" => $id]);
            guardarGeneros($conexion, $id, $generosElegidos);

            $conexion->commit();

            $_SESSION["mensaje"] = "Se guardaron los cambios de \"" . $titulo . "\".";
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
    <title>CinePlus - Modificar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include __DIR__ . "/includes/nav.php"; ?>

    <main>
        <h1>Modificar película o serie</h1>

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
