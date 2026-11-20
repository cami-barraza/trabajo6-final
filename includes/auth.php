<?php
// Funciones para controlar el acceso a las páginas.
// Hay que hacer session_start() antes de usarlas.

// Si no inició sesión, lo mando al login
function requiereLogin() {
    if (!isset($_SESSION["usuario_id"])) {
        header("Location: iniciar.php");
        exit;
    }
}

// Solo deja pasar al admin. Al resto lo manda al inicio.
function requiereAdmin() {
    requiereLogin();

    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
        header("Location: index.php");
        exit;
    }
}
