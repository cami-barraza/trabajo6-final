<?php
session_start();
require_once __DIR__ . "/config/Conexion.php";

// no deberia de mostrar esto si esta la sesión iniciada
if (isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

$errores = [];
$nombre = "";
$apellido = "";
$correo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";
    $password2 = $_POST["password2"] ?? "";

    // validaciones 
    if ($nombre === "") {
        $errores[] = "El nombre es obligatorio.";
    }
    if ($apellido === "") {
        $errores[] = "El apellido es obligatorio.";
    }
    if ($correo === "") {
        $errores[] = "El correo es obligatorio.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo no es válido.";
    }
    if ($password === "") {
        $errores[] = "La contraseña es obligatoria.";
    } elseif (strlen($password) < 6) {
        $errores[] = "La contraseña tiene que tener al menos 6 caracteres.";
    }
    if ($password !== $password2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    try {
        // comprobar que el correo no esté usado (si no hay error)
        if (empty($errores)) {
            $stmt = $conexion->prepare("SELECT usu_ID FROM usuario WHERE usu_Correo = :correo");
            $stmt->execute([":correo" => $correo]);
            if ($stmt->fetch()) {
                $errores[] = "Ya existe una cuenta con ese correo.";
            }
        }

        // si sigue sin errores, guardo el usuario
        if (empty($errores)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            
            $sql = "INSERT INTO usuario (usu_Nombre, usu_Apellido, usu_Correo, usu_Password, usu_Rol)
                    VALUES (:nombre, :apellido, :correo, :password, 'usuario')";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ":nombre" => $nombre,
                ":apellido" => $apellido,
                ":correo" => $correo,
                ":password" => $hash
            ]);

            $_SESSION["mensaje"] = "Cuenta creada. Ya podés iniciar sesión.";
            header("Location: iniciar.php");
            exit;
        }
    } catch (PDOException $e) {
        $errores[] = "No se pudo crear la cuenta, probá de nuevo más tarde.";
    }
}
?>
<<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinePlus - Registro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include __DIR__ . "/includes/nav.php"; ?>

    <div class="auth-contenedor">
        <main class="auth-formulario">
            <h1>Crear cuenta</h1>

            <?php if (!empty($errores)) { ?>
                <div class="errores">
                    <ul>
                        <?php foreach ($errores as $error) { ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <form class="formulario" action="registro.php" method="POST">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>

                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>

                <label for="password2">Repetir contraseña</label>
                <input type="password" id="password2" name="password2" required>

                <button type="submit">Registrarme</button>
            </form>

            <p>¿Ya tenés cuenta? <a href="iniciar.php">Iniciá sesión</a></p>
        </main>
        <div class="auth-imagen auth-imagen-registro"></div>
    </div>
</body>
</html>