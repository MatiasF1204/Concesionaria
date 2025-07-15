<?php
include '../conexion.php';

$mensaje = '';
$tipoMensaje = '';

$fingreso = $fegreso = $estado = $cambio_filtro = $cambio_aceite = $cambio_freno = $descripcion = $cod_auto = '';

// Obtener autos para el select
$sql_autos = "SELECT cod_auto, marca, modelo FROM auto ORDER BY marca, modelo";
$resultado_autos = mysqli_query($conexion, $sql_autos);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fingreso = trim($_POST['fingreso'] ?? '');
  $fegreso = trim($_POST['fegreso'] ?? '');
  $estado = trim($_POST['estado'] ?? '');
  $cambio_filtro = trim($_POST['cambio_filtro'] ?? '');
  $cambio_aceite = trim($_POST['cambio_aceite'] ?? '');
  $cambio_freno = trim($_POST['cambio_freno'] ?? '');
  $descripcion = trim($_POST['descripcion'] ?? '');
  $cod_auto = trim($_POST['cod_auto'] ?? '');

  if (!$fingreso || !$fegreso || !$estado || !$cambio_filtro || !$cambio_aceite || !$cambio_freno || !$descripcion || !$cod_auto) {
    $mensaje = "Por favor, completa todos los campos.";
    $tipoMensaje = "error";
  } else {
    $stmt = $conexion->prepare("INSERT INTO revision (fingreso, fegreso, estado, cambio_filtro, cambio_aceite, cambio_freno, descripcion, cod_auto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
      $mensaje = "Error al preparar la consulta: " . $conexion->error;
      $tipoMensaje = "error";
    } else {
      $stmt->bind_param('sssssssi', $fingreso, $fegreso, $estado, $cambio_filtro, $cambio_aceite, $cambio_freno, $descripcion, $cod_auto);
      if ($stmt->execute()) {
        $mensaje = "✅ Revisión registrada exitosamente.";
        $tipoMensaje = "exito";
        $fingreso = $fegreso = $estado = $cambio_filtro = $cambio_aceite = $cambio_freno = $descripcion = $cod_auto = '';
      } else {
        $mensaje = "Error al registrar revisión: " . $stmt->error;
        $tipoMensaje = "error";
      }
      $stmt->close();
    }
  }
  $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar Revisión</title>
  <link rel="stylesheet" href="../../styles/RegistrarCliente.css" />
</head>
<body>
  <div class="container">
    <h1 class="title">Registrar Revisión</h1>

    <form class="form" action="" method="post">
      <?php if (!empty($mensaje)): ?>
        <p class="mensaje <?= $tipoMensaje ?>"> <?= htmlspecialchars($mensaje) ?> </p>
      <?php endif; ?>

      <div class="form-group">
        <label for="fingreso">Fecha de ingreso:</label>
        <input type="date" name="fingreso" id="fingreso" required value="<?= htmlspecialchars($fingreso) ?>">
      </div>

      <div class="form-group">
        <label for="fegreso">Fecha de egreso:</label>
        <input type="date" name="fegreso" id="fegreso" required value="<?= htmlspecialchars($fegreso) ?>">
      </div>

      <div class="form-group">
        <label for="estado">Estado:</label>
        <select name="estado" id="estado" required>
          <option value="">-- Seleccione una opción --</option>
          <option value="En espera" <?= $estado === "En espera" ? 'selected' : '' ?>>En espera</option>
          <option value="En revision" <?= $estado === "En revision" ? 'selected' : '' ?>>En revisión</option>
          <option value="Finalizado" <?= $estado === "Finalizado" ? 'selected' : '' ?>>Finalizado</option>
        </select>
      </div>

      <div class="form-group">
        <label for="cambio_filtro">Cambio de filtro:</label>
        <select name="cambio_filtro" id="cambio_filtro" required>
          <option value="">-- Seleccione una opción --</option>
          <option value="Si" <?= $cambio_filtro === "Si" ? 'selected' : '' ?>>Si</option>
          <option value="No" <?= $cambio_filtro === "No" ? 'selected' : '' ?>>No</option>
        </select>
      </div>

      <div class="form-group">
        <label for="cambio_aceite">Cambio de aceite:</label>
        <select name="cambio_aceite" id="cambio_aceite" required>
          <option value="">-- Seleccione una opción --</option>
          <option value="Si" <?= $cambio_aceite === "Si" ? 'selected' : '' ?>>Si</option>
          <option value="No" <?= $cambio_aceite === "No" ? 'selected' : '' ?>>No</option>
        </select>
      </div>

      <div class="form-group">
        <label for="cambio_freno">Cambio de freno:</label>
        <select name="cambio_freno" id="cambio_freno" required>
          <option value="">-- Seleccione una opción --</option>
          <option value="Si" <?= $cambio_freno === "Si" ? 'selected' : '' ?>>Si</option>
          <option value="No" <?= $cambio_freno === "No" ? 'selected' : '' ?>>No</option>
        </select>
      </div>

      <div class="form-group">
        <label for="descripcion">Descripción:</label>
        <input type="text" name="descripcion" id="descripcion" required minlength="5" value="<?= htmlspecialchars($descripcion) ?>">
      </div>

      <div class="form-group">
        <label for="cod_auto">Auto:</label>
        <select name="cod_auto" id="cod_auto" required>
          <option value="">-- Seleccione un auto --</option>
          <?php while ($auto = mysqli_fetch_assoc($resultado_autos)): ?>
            <option value="<?= $auto['cod_auto'] ?>" <?= $cod_auto == $auto['cod_auto'] ? 'selected' : '' ?>><?= htmlspecialchars($auto['marca'] . ' - ' . $auto['modelo']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <button class="btn" type="submit">Registrar</button>
    </form>

    <p class="back-link">
      <a href="../../Menu.html">Volver al menú</a>
    </p>
  </div>
</body>
</html>