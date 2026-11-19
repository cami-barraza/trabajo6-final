<?php
?>
<header class="cabecera">
    <a class="logo" href="index.php">
    <img src="img/logo2.png" alt="Logo de CinePlus"></a>
    <nav>
        <?php if (isset($_SESSION["usuario_id"])) { ?>
        <a href="index.php">Inicio</a>
        <a href="datos.php">Datos de la base</a>
        <span class="saludo">Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></span>
        <a href="cerrarSesion.php">Cerrar sesión</a>
        <?php } else { ?>
        <a href="iniciar.php">Iniciar sesión</a>
        <a href="registro.php">Registrarse</a>
    <?php } ?>
    </nav>
</header>
