<?php
session_start();
require_once __DIR__ . "/config/Conexion.php";

// Muestra el resultado de una consulta como tabla.
// Las consultas de esta página son fijas (no dependen de lo que escriba el usuario).
function mostrarTabla($conexion, $titulo, $sql) {
    echo "<h2>" . $titulo . "</h2>";

    try {
        $filas = $conexion->query($sql)->fetchAll();
    } catch (PDOException $e) {
        echo "<p>No se pudo leer la tabla.</p>";
        return;
    }

    if (count($filas) === 0) {
        echo "<p>La tabla está vacía.</p>";
        return;
    }

    echo "<div class='tabla-scroll'><table><tr>";
    // los nombres de las columnas salen de la primera fila
    foreach (array_keys($filas[0]) as $columna) {
        echo "<th>" . htmlspecialchars($columna) . "</th>";
    }
    echo "</tr>";

    foreach ($filas as $fila) {
        echo "<tr>";
        foreach ($fila as $valor) {
            echo "<td>" . htmlspecialchars((string)$valor) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table></div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinePlus - Datos de la base</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . "/includes/nav.php"; ?>

    <main>
        <h1>Datos de la base de datos</h1>
        <p class="intro">
            Acá se ven las tablas de la base <strong>cineplus</strong> con los datos cargados.
            La tabla de usuarios no se muestra porque tiene las contraseñas (aunque estén cifradas).
        </p>

        <?php
        mostrarTabla($conexion, "genero", "SELECT * FROM genero");
        mostrarTabla($conexion, "contenido", "SELECT * FROM contenido");
        mostrarTabla($conexion, "pelicula", "SELECT * FROM pelicula");
        mostrarTabla($conexion, "serie", "SELECT * FROM serie");
        mostrarTabla($conexion, "contenido_genero", "SELECT * FROM contenido_genero");
        mostrarTabla($conexion, "persona", "SELECT * FROM persona");
        mostrarTabla($conexion, "participacion", "SELECT * FROM participacion");
        ?>
    </main>
</body>
</html>
