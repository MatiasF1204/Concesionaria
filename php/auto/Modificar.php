<?php
include '../conexion.php';

$mensaje = '';
$tipoMensaje = '';

$cod_auto = $_GET['cod_auto'] ?? null;
$marca = $modelo = $color = $pventa = $cod_cliente = '';

// Cargar clientes disponibles
$sql_clientes = "SELECT cod_cliente, nomyape FROM cliente";
$clientes_resultado = mysqli_query($conexion, $sql_clientes);
$clientes = mysqli_fetch_all($clientes_resultado, MYSQLI_ASSOC);

if ($cod_auto) {
  $stmt = $conexion->prepare("SELECT * FROM auto WHERE cod_auto = ?");
  $stmt->bind_param("i", $cod_auto);
  $stmt->execute();
  $resultado = $stmt->get_result();
  if ($resultado->num_rows === 1) {
    $auto = $resultado->fetch_assoc();
    $marca = $auto['marca'];
    $modelo = $auto['modelo'];
    $color = $auto['color'];
    $pventa = $auto['pventa'];
    $cod_cliente = $auto['cod_cliente'];
  } else {
    $mensaje = "Auto no encontrado.";
    $tipoMensaje = "error";
  }
  $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $cod_auto = $_POST['cod_auto'] ?? null;
  $marca = trim($_POST['marca'] ?? '');
  $modelo = trim($_POST['modelo'] ?? '');
  $color = trim($_POST['color'] ?? '');
  $pventa = trim($_POST['pventa'] ?? '');
  $cod_cliente = trim($_POST['cod_cliente'] ?? '');

  if (!$marca || !$modelo || !$color || !$pventa || !$cod_cliente) {
    $mensaje = "Por favor, completa todos los campos.";
    $tipoMensaje = "error";
  } elseif (!is_numeric($pventa)) {
    $mensaje = "El precio debe ser numérico.";
    $tipoMensaje = "error";
  } else {
    $stmt = $conexion->prepare("UPDATE auto SET marca = ?, modelo = ?, color = ?, pventa = ?, cod_cliente = ? WHERE cod_auto = ?");
    $stmt->bind_param("ssssii", $marca, $modelo, $color, $pventa, $cod_cliente, $cod_auto);
    if ($stmt->execute()) {
      $mensaje = "✅ Auto modificado exitosamente.";
      $tipoMensaje = "exito";
    } else {
      $mensaje = "Error al modificar auto: " . $stmt->error;
      $tipoMensaje = "error";
    }
    $stmt->close();
  }
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modificar Auto</title>
  <link rel="stylesheet" href="../../styles/RegistrarCliente.css">
</head>

<body>
  <div class="container">
    <h1 class="title">Modificar Auto</h1>

    <?php if (!empty($mensaje)): ?>
      <p class="mensaje <?= $tipoMensaje ?>"> <?= htmlspecialchars($mensaje) ?> </p>
    <?php endif; ?>

    <form class="form" method="post">
      <input type="hidden" name="cod_auto" value="<?= htmlspecialchars($cod_auto) ?>">

      <div class="form-group">
        <label for="marca">Marca:</label>
        <input type="text" name="marca" id="marca" required value="<?= htmlspecialchars($marca) ?>">
      </div>

      <div class="form-group">
        <label for="modelo">Modelo:</label>
        <input type="text" name="modelo" id="modelo" required value="<?= htmlspecialchars($modelo) ?>">
      </div>

      <div class="form-group">
        <label for="color">Color:</label>
        <input type="text" name="color" id="color" required value="<?= htmlspecialchars($color) ?>">
      </div>

      <div class="form-group">
        <label for="pventa">Precio de venta:</label>
        <input type="number" name="pventa" id="pventa" required value="<?= htmlspecialchars($pventa) ?>">
      </div>

      <div class="form-group">
        <label for="cod_cliente">Cliente:</label>
        <select name="cod_cliente" id="cod_cliente" required>
          <option value="">-- Ingrese una opción --</option>
          <?php foreach ($clientes as $cliente): ?>
            <option value="<?= $cliente['cod_cliente'] ?>" <?= $cliente['cod_cliente'] == $cod_cliente ? 'selected' : '' ?>>
              <?= htmlspecialchars($cliente['nomyape']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <button class="btn" type="submit">Modificar</button>
    </form>

    <p class="back-link">
      <a href="../../Menu.html">Volver al menú</a>
    </p>
  </div>
</body>
</html>
