<?php
session_start();
require_once __DIR__ . "/config/Conexion.php";
require_once __DIR__ . "/includes/auth.php";

// solo el admin puede entrar acá
requiereAdmin();

// mensajes que dejan alta.php, modificar.php y eliminar.php (se muestran una sola vez)
$mensaje = "";
$error = "";
if (isset($_SESSION["mensaje"])) {
    $mensaje = $_SESSION["mensaje"];
    unset($_SESSION["mensaje"]);
}
if (isset($_SESSION["error"])) {
    $error = $_SESSION["error"];
    unset($_SESSION["error"]);
}

// listado de películas y series con sus géneros
$sql = "SELECT c.cont_ID, c.cont_Titulo, c.cont_Anio, c.cont_Tipo,
               (SELECT GROUP_CONCAT(g.gen_Nombre SEPARATOR ', ')
                FROM contenido_genero cg
                INNER JOIN genero g ON g.gen_ID = cg.gen_ID
                WHERE cg.cont_ID = c.cont_ID) AS generos
        FROM contenido c
        ORDER BY c.cont_Titulo";

try {
    $contenidos = $conexion->query($sql)->fetchAll();
} catch (PDOException $e) {
    $contenidos = [];
    $error = "No se pudo cargar el listado.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinePlus - Administrar contenido</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include __DIR__ . "/includes/nav.php"; ?>

    <main>
        <h1>Administrar películas y series</h1>

        <?php if ($mensaje !== "") { ?>
            <p class="aviso"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php } ?>

        <?php if ($error !== "") { ?>
            <div class="errores"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <div class="barra-acciones">
            <span><?php echo count($contenidos); ?> en el catálogo</span>
            <a class="boton" href="alta.php">+ Agregar película o serie</a>
        </div>

        <?php if (count($contenidos) === 0) { ?>
            <p>Todavía no hay contenido cargado.</p>
        <?php } else { ?>
            <div class="tabla-scroll">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Año</th>
                        <th>Géneros</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($contenidos as $c) { ?>
                        <tr>
                            <td><?php echo $c["cont_ID"]; ?></td>
                            <td><?php echo htmlspecialchars($c["cont_Titulo"]); ?></td>
                            <td><?php echo htmlspecialchars($c["cont_Tipo"]); ?></td>
                            <td><?php echo $c["cont_Anio"]; ?></td>
                            <td><?php echo htmlspecialchars($c["generos"] ?? "-"); ?></td>
                            <td class="acciones">
                                <a class="boton boton-azul" href="modificar.php?id=<?php echo $c["cont_ID"]; ?>">Modificar</a>

                                <!-- la baja se manda por POST y con confirmación -->
                                <form action="eliminar.php" method="POST"
                                      onsubmit="return confirm('¿Seguro que querés eliminarlo? No se puede deshacer.');">
                                    <input type="hidden" name="id" value="<?php echo $c["cont_ID"]; ?>">
                                    <button class="boton boton-rojo" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    </main>
</body>
</html>
