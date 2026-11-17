<?php
session_start();
require_once __DIR__ . "/config/Conexion.php";

// si no inició sesión, lo mando al login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: iniciar.php");
    exit;
}

// mensaje (se muestra una sola vez)
$mensaje = "";
if (isset($_SESSION["mensaje"])) {
    $mensaje = $_SESSION["mensaje"];
    unset($_SESSION["mensaje"]);
}

// datos de pelis y series.
$sql = "SELECT c.cont_ID, c.cont_Titulo, c.cont_Anio, c.cont_Sinopsis, c.cont_Tipo,
               p.peli_Duracion, s.serie_Temporadas, s.serie_EpisodiosTot,
               (SELECT GROUP_CONCAT(g.gen_Nombre SEPARATOR ', ')
                FROM contenido_genero cg
                INNER JOIN genero g ON g.gen_ID = cg.gen_ID
                WHERE cg.cont_ID = c.cont_ID) AS generos
        FROM contenido c
        LEFT JOIN pelicula p ON p.peli_ID = c.cont_ID
        LEFT JOIN serie s ON s.serie_ID = c.cont_ID
        ORDER BY c.cont_Titulo";

try {
    $contenidos = $conexion->query($sql)->fetchAll();
} catch (PDOException $e) {
    $contenidos = [];
    $mensaje = "No se pudo cargar el catálogo.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinePlus - Inicio</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . "/includes/nav.php"; ?>

    <main>
        <h1>Películas y series</h1>
        <p class="intro">
            Este es el catálogo de CinePlus.
            <?php if (!isset($_SESSION["usuario_id"])) { ?>
                Para tener tu cuenta podés <a href="registro.php">registrarte</a>.
            <?php } ?>
        </p>

        <?php if ($mensaje !== "") { ?>
            <p class="aviso"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php } ?>

        <?php if (count($contenidos) === 0) { ?>
            <p>Todavía no hay contenido cargado.</p>
        <?php } else { ?>
            <div class="grilla">
                <?php foreach ($contenidos as $c) { ?>
                    <article class="tarjeta">
                        <h2><?php echo htmlspecialchars($c["cont_Titulo"]); ?></h2>
                        <p class="tipo">
                            <?php echo htmlspecialchars($c["cont_Tipo"]); ?>
                            (<?php echo $c["cont_Anio"]; ?>)
                        </p>
                        <p><strong>Géneros:</strong> <?php echo htmlspecialchars($c["generos"] ?? "sin género"); ?></p>

                        <?php if ($c["cont_Tipo"] === "Pelicula") { ?>
                            <p><strong>Duración:</strong> <?php echo $c["peli_Duracion"]; ?> min</p>
                        <?php } else { ?>
                            <p><strong>Temporadas:</strong> <?php echo $c["serie_Temporadas"]; ?>
                               - <?php echo $c["serie_EpisodiosTot"]; ?> episodios</p>
                        <?php } ?>

                        <p class="sinopsis"><?php echo htmlspecialchars($c["cont_Sinopsis"] ?? ""); ?></p>
                    </article>
                <?php } ?>
            </div>
        <?php } ?>
    </main>
</body>
</html>
