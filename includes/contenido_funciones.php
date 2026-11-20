<?php
// Funciones que comparten alta.php y modificar.php

// Revisa los datos del formulario.
// Devuelve un array con los errores (si está vacío, está todo bien).
function validarContenido($titulo, $anio, $sinopsis, $tipo, $duracion, $temporadas, $episodios, $duracionProm, $generosElegidos, $generos) {
    $errores = [];

    if ($titulo === "") {
        $errores[] = "El título es obligatorio.";
    } elseif (mb_strlen($titulo) > 150) {
        $errores[] = "El título no puede tener más de 150 caracteres.";
    }

    if (!ctype_digit($anio) || $anio < 1888 || $anio > date("Y") + 5) {
        $errores[] = "El año no es válido.";
    }

    if (mb_strlen($sinopsis) > 500) {
        $errores[] = "La sinopsis no puede tener más de 500 caracteres.";
    }

    if ($tipo === "Pelicula") {
        if (!ctype_digit($duracion) || $duracion < 1) {
            $errores[] = "La duración tiene que ser un número de minutos mayor a 0.";
        }
    } elseif ($tipo === "Serie") {
        if (!ctype_digit($temporadas) || $temporadas < 1) {
            $errores[] = "Las temporadas tienen que ser un número mayor a 0.";
        }
        if (!ctype_digit($episodios) || $episodios < 1) {
            $errores[] = "Los episodios tienen que ser un número mayor a 0.";
        }
        if (!ctype_digit($duracionProm) || $duracionProm < 1) {
            $errores[] = "La duración promedio tiene que ser un número de minutos mayor a 0.";
        }
    } else {
        $errores[] = "El tipo tiene que ser Película o Serie.";
    }

    // los géneros elegidos tienen que existir en la tabla genero
    $idsValidos = array_map("intval", array_column($generos, "gen_ID"));
    foreach ($generosElegidos as $g) {
        if (!in_array((int)$g, $idsValidos, true)) {
            $errores[] = "Hay un género que no es válido.";
            break;
        }
    }

    return $errores;
}

// Guarda los géneros de un contenido en la tabla contenido_genero
function guardarGeneros($conexion, $idContenido, $generosElegidos) {
    $stmt = $conexion->prepare("INSERT INTO contenido_genero (cont_ID, gen_ID) VALUES (:cont, :gen)");

    foreach (array_unique($generosElegidos) as $g) {
        $stmt->execute([
            ":cont" => $idContenido,
            ":gen" => (int)$g
        ]);
    }
}
