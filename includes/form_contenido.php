<?php

if ($modo === "alta") {
    $destino = "alta.php";
} else {
    $destino = "modificar.php?id=" . (int)$id;
}
?>
<form class="formulario formulario-ancho" action="<?php echo $destino; ?>" method="POST">

    <label for="titulo">Título</label>
    <input class="sin-icono" type="text" id="titulo" name="titulo" maxlength="150"
           value="<?php echo htmlspecialchars($titulo); ?>" required>

    <label for="anio">Año</label>
    <input type="number" id="anio" name="anio" min="1888"
           value="<?php echo htmlspecialchars((string)$anio); ?>" required>

    <?php if ($modo === "alta") { ?>
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" onchange="mostrarCampos()">
            <option value="Pelicula" <?php if ($tipo === "Pelicula") { echo "selected"; } ?>>Película</option>
            <option value="Serie" <?php if ($tipo === "Serie") { echo "selected"; } ?>>Serie</option>
        </select>
    <?php } else { ?>
        <!-- al modificar no se puede cambiar de película a serie -->
        <p>Tipo: <strong><?php echo htmlspecialchars($tipo); ?></strong> (no se puede cambiar)</p>
        <input type="hidden" id="tipo" value="<?php echo htmlspecialchars($tipo); ?>">
    <?php } ?>

    <div id="campos-pelicula">
        <label for="duracion">Duración (minutos)</label>
        <input type="number" id="duracion" name="duracion" min="1"
               value="<?php echo htmlspecialchars((string)$duracion); ?>">
    </div>

    <div id="campos-serie">
        <label for="temporadas">Temporadas</label>
        <input type="number" id="temporadas" name="temporadas" min="1"
               value="<?php echo htmlspecialchars((string)$temporadas); ?>">

        <label for="episodios">Episodios en total</label>
        <input type="number" id="episodios" name="episodios" min="1"
               value="<?php echo htmlspecialchars((string)$episodios); ?>">

        <label for="duracion_prom">Duración promedio por episodio (minutos)</label>
        <input type="number" id="duracion_prom" name="duracion_prom" min="1"
               value="<?php echo htmlspecialchars((string)$duracionProm); ?>">
    </div>

    <label for="sinopsis">Sinopsis</label>
    <textarea id="sinopsis" name="sinopsis" rows="4" maxlength="500"><?php echo htmlspecialchars($sinopsis); ?></textarea>

    <label>Géneros</label>
    <div class="checks">
        <?php foreach ($generos as $g) { ?>
            <label>
                <input type="checkbox" name="generos[]" value="<?php echo (int)$g["gen_ID"]; ?>"
                    <?php if (in_array((int)$g["gen_ID"], $generosElegidos, true)) { echo "checked"; } ?>>
                <?php echo htmlspecialchars($g["gen_Nombre"]); ?>
            </label>
        <?php } ?>
    </div>

    <button type="submit"><?php echo ($modo === "alta") ? "Agregar" : "Guardar cambios"; ?></button>
</form>

<script>
// muestra los campos de película o de serie según el tipo
function mostrarCampos() {
    var tipo = document.getElementById("tipo").value;
    document.getElementById("campos-pelicula").style.display = (tipo === "Pelicula") ? "block" : "none";
    document.getElementById("campos-serie").style.display = (tipo === "Serie") ? "block" : "none";
}
mostrarCampos();
</script>
