<?php
include '../conexion.php';

$mensaje = '';
$tipoMensaje = '';

$cod_cliente = $_GET['cod_cliente'] ?? null;

$nomyape = $dni = $correo = $direccion = $ciudad = $telefono = $falta = '';

if ($cod_cliente) {
    // Cargar datos para edición
    $stmt = $conexion->prepare("SELECT * FROM cliente WHERE cod_cliente = ?");
    $stmt->bind_param("i", $cod_cliente);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows === 1) {
        $cliente = $resultado->fetch_assoc();
        $nomyape = $cliente['nomyape'];
        $dni = $cliente['dni'];
        $correo = $cliente['correo'];
        $direccion = $cliente['direccion'];
        $ciudad = $cliente['ciudad'];
        $telefono = $cliente['telefono'];
        $falta = $cliente['falta'];
    } else {
        $mensaje = "Cliente no encontrado.";
        $tipoMensaje = "error";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir cod_cliente para edición (puede venir en POST)
    $cod_cliente = $_POST['cod_cliente'] ?? null;

    $nomyape = trim($_POST['nomyape'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $falta = trim($_POST['falta'] ?? '');

    // Validaciones básicas
    if (!$nomyape || !$dni || !$correo || !$direccion || !$ciudad || !$telefono || !$falta) {
        $mensaje = "Por favor, completa todos los campos.";
        $tipoMensaje = "error";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Correo no válido.";
        $tipoMensaje = "error";
    } elseif (!is_numeric($dni) || !is_numeric($telefono)) {
        $mensaje = "DNI y teléfono deben ser numéricos.";
        $tipoMensaje = "error";
    } else {
        // Validar que dni, correo y teléfono no estén usados por otro cliente distinto
        if ($cod_cliente) {
            $verificacion = $conexion->prepare("SELECT cod_cliente FROM cliente WHERE (dni = ? OR correo = ? OR telefono = ?) AND cod_cliente != ?");
            $verificacion->bind_param("isii", $dni, $correo, $telefono, $cod_cliente);
        } else {
            $verificacion = $conexion->prepare("SELECT cod_cliente FROM cliente WHERE dni = ? OR correo = ? OR telefono = ?");
            $verificacion->bind_param("isi", $dni, $correo, $telefono);
        }

        $verificacion->execute();
        $resultado = $verificacion->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "Ya existe un cliente con ese DNI, correo o teléfono.";
            $tipoMensaje = "error";
        } else {
            if ($cod_cliente) {
                // Actualizar cliente
                $stmt = $conexion->prepare("UPDATE cliente SET nomyape=?, dni=?, correo=?, direccion=?, ciudad=?, telefono=?, falta=? WHERE cod_cliente=?");
                $stmt->bind_param('sisssssi', $nomyape, $dni, $correo, $direccion, $ciudad, $telefono, $falta, $cod_cliente);
                if ($stmt->execute()) {
                    $mensaje = "✅ Cliente modificado exitosamente.";
                    $tipoMensaje = "exito";
                } else {
                    $mensaje = "Error al modificar cliente: " . $stmt->error;
                    $tipoMensaje = "error";
                }
                $stmt->close();
            } else {
                // Insertar cliente nuevo
                $stmt = $conexion->prepare("INSERT INTO cliente (nomyape, dni, correo, direccion, ciudad, telefono, falta) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sisssss', $nomyape, $dni, $correo, $direccion, $ciudad, $telefono, $falta);
                if ($stmt->execute()) {
                    $mensaje = "✅ Cliente registrado exitosamente.";
                    $tipoMensaje = "exito";
                    // Limpiar campos tras éxito
                    $nomyape = $dni = $correo = $direccion = $ciudad = $telefono = $falta = '';
                } else {
                    $mensaje = "Error al registrar cliente: " . $stmt->error;
                    $tipoMensaje = "error";
                }
                $stmt->close();
            }
        }
        $verificacion->close();
    }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $cod_cliente ? "Modificar Cliente" : "Registrar Cliente" ?></title>
  <link rel="stylesheet" href="../../styles/RegistrarCliente.css" />
</head>

<body>
  <div class="container">
    <h1 class="title"><?= $cod_cliente ? "Modificar Cliente" : "Registrar Cliente" ?></h1>

    <?php if (!empty($mensaje)): ?>
      <p class="mensaje <?= $tipoMensaje ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form class="form" action="" method="post">

      <input type="hidden" name="cod_cliente" value="<?= htmlspecialchars($cod_cliente) ?>">

      <div class="form-group">
        <label for="nomyape">Nombre completo:</label>
        <input type="text" name="nomyape" id="nomyape" required minlength="3" value="<?= htmlspecialchars($nomyape) ?>">
      </div>

      <div class="form-group">
        <label for="dni">DNI:</label>
        <input type="number" name="dni" id="dni" required value="<?= htmlspecialchars($dni) ?>">
      </div>

      <div class="form-group">
        <label for="correo">Correo electrónico:</label>
        <input type="email" name="correo" id="correo" required value="<?= htmlspecialchars($correo) ?>">
      </div>

      <div class="form-group">
        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion" required minlength="3" value="<?= htmlspecialchars($direccion) ?>">
      </div>

      <div class="form-group">
        <label for="ciudad">Ciudad:</label>
        <select name="ciudad" id="ciudad" required>
          <option value="">-- Ingrese una opción --</option>
          <option value="Rio Grande" <?= $ciudad === "Rio Grande" ? 'selected' : '' ?>>Rio Grande</option>
          <option value="Tolhuin" <?= $ciudad === "Tolhuin" ? 'selected' : '' ?>>Tolhuin</option>
          <option value="Ushuaia" <?= $ciudad === "Ushuaia" ? 'selected' : '' ?>>Ushuaia</option>
        </select>
      </div>

      <div class="form-group">
        <label for="telefono">Teléfono:</label>
        <input type="tel" name="telefono" id="telefono" required pattern="[0-9]{6,15}" value="<?= htmlspecialchars($telefono) ?>">
      </div>

      <div class="form-group">
        <label for="falta">Fecha de alta:</label>
        <input type="date" name="falta" id="falta" required value="<?= htmlspecialchars($falta) ?>">
      </div>

      <button class="btn" type="submit"><?= $cod_cliente ? "Modificar" : "Registrar" ?></button>
    </form>

    <p class="back-link">
      <a href="../../Menu.html">Volver al menú</a>
    </p>
  </div>
</body>

</html>
