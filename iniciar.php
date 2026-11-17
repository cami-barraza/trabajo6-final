<?php
session_start();
require_once __DIR__ . "/config/Conexion.php";

session_start();

// si no inició sesión, lo mando al login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: iniciar.php");
    exit;
}

// si ya está logueado lo mando al inicio
if (isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

// mensaje que viene del registro (se muestra una sola vez)
$mensaje = "";
if (isset($_SESSION["mensaje"])) {
    $mensaje = $_SESSION["mensaje"];
    unset($_SESSION["mensaje"]);
}

$error = "";
$correo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($correo === "" || $password === "") {
        $error = "Completá el correo y la contraseña.";
    } else {
        try {
            // busco al usuario por su correo
            $stmt = $conexion->prepare("SELECT * FROM usuario WHERE usu_Correo = :correo");
            $stmt->execute([":correo" => $correo]);
            $usuario = $stmt->fetch();

            // si existe y la contraseña coincide con el hash guardado
            if ($usuario && password_verify($password, $usuario["usu_Password"])) {
                session_regenerate_id(true);
                $_SESSION["usuario_id"] = $usuario["usu_ID"];
                $_SESSION["nombre"] = $usuario["usu_Nombre"];
                $_SESSION["rol"] = $usuario["usu_Rol"];

                header("Location: index.php");
                exit;
            } else {
                $error = "No se pudo iniciar sesión: correo o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            $error = "Hubo un problema con la base de datos, probá de nuevo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinePlus - Iniciar sesión</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . "/includes/nav.php"; ?>

    <main>
        <h1>Iniciar sesión</h1>

        <?php if ($mensaje !== "") { ?>
            <p class="aviso"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php } ?>

        <?php if ($error !== "") { ?>
            <div class="errores"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <form class="formulario" action="iniciar.php" method="POST">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Entrar</button>
        </form>

        <p>¿No tenés cuenta? <a href="registro.php">Registrate</a></p>
    </main>
</body>
</html>
