<?php
session_start();

// en teoria borra los datos de sesión
session_unset();
session_destroy();

header("Location: iniciar.php");
exit;
