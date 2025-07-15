<?php
include '../conexion.php';

$mensaje = '';
$tipoMensaje = ''; 

$nombre = $apellido = $correo = $user = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $apellido = trim($_POST['apellido'] ?? '');
  $correo = trim($_POST['correo'] ?? '');
  $user = trim($_POST['user'] ?? '');
  $pass = $_POST['pass'] ?? '';

  if (empty($nombre) || empty($apellido) || empty($correo) || empty($user) || empty($pass)) {
    $mensaje = "Por favor, completa todos los campos.";
    $tipoMensaje = 'error';
  } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $mensaje = "Correo inválido.";
    $tipoMensaje = 'error';
  } else {
    // Validar si ya existe correo o usuario
    $checkStmt = $conexion->prepare("SELECT cod_usuario FROM usuario WHERE correo = ? OR user = ?");
    $checkStmt->bind_param("ss", $correo, $user);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
      $mensaje = "Ya existe un usuario registrado con ese correo o nombre de usuario.";
      $tipoMensaje = 'error';
    } else {
      // Si no existe, proceder al insert
      $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
      $stmt = $conexion->prepare("INSERT INTO usuario (nombre, apellido, correo, user, pass) VALUES (?, ?, ?, ?, ?)");

      if (!$stmt) {
        $mensaje = "Error al preparar consulta: " . $conexion->error;
        $tipoMensaje = 'error';
      } else {
        $stmt->bind_param('sssss', $nombre, $apellido, $correo, $user, $pass_hash);
        if ($stmt->execute()) {
          $mensaje = "✅ Usuario creado exitosamente.";
          $tipoMensaje = 'exito';
          $nombre = $apellido = $correo = $user = ''; // limpiar campos
        } else {
          $mensaje = "Error al crear usuario: " . $stmt->error;
          $tipoMensaje = 'error';
        }
        $stmt->close();
      }
    }

    $checkStmt->close();
  }

  $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrarse - Concesionaria Web</title>
  <link rel="stylesheet" href="../../styles/Registrarse.css" />
</head>

<body>
  <div class="container">
    <h1 class="title">Formulario de Registro</h1>

    <form class="form" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">


      <div class="row">
        <div class="form-group">
          <label for="nombre">Nombre:</label>
          <input type="text" name="nombre" id="nombre" required />
        </div>

        <div class="form-group">
          <label for="apellido">Apellido:</label>
          <input type="text" name="apellido" id="apellido" required />
        </div>
      </div>

      <div class="form-group">
        <label for="correo">Correo electrónico:</label>
        <input type="email" name="correo" id="correo" required />
      </div>

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

      <button class="btn" type="submit">Registrarse</button>
    </form>

    <p class="register-text">
      <a href="./InicioSesion.php">Volver</a>
    </p>
  </div>
</body>

</html>