<?php
session_start();
include '../conexion.php';

$mensaje = '';
$tipoMensaje = ''; // 'error' o 'exito'

$user = $_POST['user'] ?? '';
$pass = $_POST['pass'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($user) || empty($pass)) {
        $mensaje = "Por favor, completa todos los campos.";
        $tipoMensaje = 'error';
    } else {
        // Consulta preparada segura
        $stmt = $conexion->prepare("SELECT cod_usuario, user, pass FROM usuario WHERE user = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($pass, $usuario['pass'])) {
                $_SESSION['usuario'] = $usuario['user'];
                $_SESSION['cod_usuario'] = $usuario['cod_usuario'];
                header("Location: ../../Menu.html");
                exit();
            } else {
                $mensaje = "Contraseña incorrecta.";
                $tipoMensaje = 'error';
            }
        } else {
            $mensaje = "El usuario no existe.";
            $tipoMensaje = 'error';
        }
        $stmt->close();
    }
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar sesión - Concesionaria Web</title>
  <link rel="stylesheet" href="../../styles/IniciarSesion.css" />
</head>
<body>
  <div class="container">
    <h1 class="title">Concesionaria Web</h1>

    <form class="form" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
      <div class="form-group">
        <label for="user">Usuario:</label>
        <input type="text" name="user" id="user" required />
      </div>

      <div class="form-group">
        <label for="pass">Contraseña:</label>
        <input type="password" name="pass" id="pass" required />
      </div>

      <?php if (!empty($mensaje)): ?>
        <p class="mensaje <?= $tipoMensaje ?>"><?= htmlspecialchars($mensaje) ?></p>
      <?php endif; ?>

      <button class="btn" type="submit">Iniciar sesión</button>
    </form>

    <p class="register-text">
      ¿No tienes una cuenta?
      <a href="./Registrarse.php">Regístrate</a>.
    </p>
  </div>
</body>
</html>
