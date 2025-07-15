<?php
include '../conexion.php';

$mensaje = '';
$tipoMensaje = '';

$cod_revision = $_GET['cod_revision'] ?? null;

// Inicializar variables vacías
$fingreso = $fegreso = $estado = $cambio_filtro = $cambio_aceite = $cambio_freno = $descripcion = $cod_auto = '';

// Obtener datos actuales
if ($cod_revision && is_numeric($cod_revision)) {
    $stmt = $conexion->prepare("SELECT * FROM revision WHERE cod_revision = ?");
    $stmt->bind_param("i", $cod_revision);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $revision = $resultado->fetch_assoc();
        $fingreso = $revision['fingreso'];
        $fegreso = $revision['fegreso'];
        $estado = $revision['estado'];
        $cambio_filtro = $revision['cambio_filtro'];
        $cambio_aceite = $revision['cambio_aceite'];
        $cambio_freno = $revision['cambio_freno'];
        $descripcion = $revision['descripcion'];
        $cod_auto = $revision['cod_auto'];
    } else {
        $mensaje = "Revisión no encontrada.";
        $tipoMensaje = "error";
    }
    $stmt->close();
} else {
    $mensaje = "ID de revisión inválido.";
    $tipoMensaje = "error";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod_revision = $_POST['cod_revision'] ?? null;
    $fingreso = trim($_POST['fingreso'] ?? '');
    $fegreso = trim($_POST['fegreso'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $cambio_filtro = trim($_POST['cambio_filtro'] ?? '');
    $cambio_aceite = trim($_POST['cambio_aceite'] ?? '');
    $cambio_freno = trim($_POST['cambio_freno'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $cod_auto = $_POST['cod_auto'] ?? '';

    if (!$fingreso || !$estado || !$cambio_filtro || !$cambio_aceite || !$cambio_freno || !$descripcion || !$cod_auto) {
        $mensaje = "Por favor, completa todos los campos.";
        $tipoMensaje = "error";
    } elseif (!in_array($estado, ['En revision', 'Finalizado'])) {
        $mensaje = "Estado no válido.";
        $tipoMensaje = "error";
    } else {
        $stmt = $conexion->prepare("UPDATE revision SET fingreso = ?, fegreso = ?, estado = ?, cambio_filtro = ?, cambio_aceite = ?, cambio_freno = ?, descripcion = ?, cod_auto = ? WHERE cod_revision = ?");
        $stmt->bind_param("sssssssii", $fingreso, $fegreso, $estado, $cambio_filtro, $cambio_aceite, $cambio_freno, $descripcion, $cod_auto, $cod_revision);

        if ($stmt->execute()) {
            $mensaje = "✅ Revisión actualizada correctamente.";
            $tipoMensaje = "exito";
        } else {
            $mensaje = "Error al actualizar revisión: " . $stmt->error;
            $tipoMensaje = "error";
        }

        $stmt->close();
    }
}

$sql_auto = "SELECT * FROM auto";
$resultado_auto = mysqli_query($conexion, $sql_auto);
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modificar Revisión</title>
    <link rel="stylesheet" href="../../styles/RegistrarCliente.css">
</head>

<body>
    <div class="container">
        <h1 class="title">Modificar Revisión</h1>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje <?= $tipoMensaje ?>"> <?= htmlspecialchars($mensaje) ?> </p>
        <?php endif; ?>

        <form class="form" method="post">
            <input type="hidden" name="cod_revision" value="<?= htmlspecialchars($cod_revision) ?>">

            <div class="form-group">
                <label for="fingreso">Fecha de ingreso:</label>
                <input type="date" name="fingreso" id="fingreso" required value="<?= htmlspecialchars($fingreso) ?>">
            </div>

            <div class="form-group">
                <label for="fegreso">Fecha de egreso:</label>
                <input type="date" name="fegreso" id="fegreso" value="<?= htmlspecialchars($fegreso) ?>">
            </div>

            <div class="form-group">
                <label for="estado">Estado:</label>
                <select name="estado" id="estado" required>
                    <option value="">-- Seleccione --</option>
                    <option value="En revision" <?= $estado === 'En revision' ? 'selected' : '' ?>>En revisión</option>
                    <option value="Finalizado" <?= $estado === 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cambio_filtro">Cambio de filtro:</label>
                <select name="cambio_filtro" id="cambio_filtro" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Si" <?= $cambio_filtro === 'Si' ? 'selected' : '' ?>>Si</option>
                    <option value="No" <?= $cambio_filtro === 'No' ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cambio_aceite">Cambio de aceite:</label>
                <select name="cambio_aceite" id="cambio_aceite" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Si" <?= $cambio_aceite === 'Si' ? 'selected' : '' ?>>Si</option>
                    <option value="No" <?= $cambio_aceite === 'No' ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cambio_freno">Cambio de freno:</label>
                <select name="cambio_freno" id="cambio_freno" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Si" <?= $cambio_freno === 'Si' ? 'selected' : '' ?>>Si</option>
                    <option value="No" <?= $cambio_freno === 'No' ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <input type="text" name="descripcion" id="descripcion" required minlength="3" value="<?= htmlspecialchars($descripcion) ?>">
            </div>

            <div class="form-group">
                <label for="cod_auto">Auto:</label>
                <select name="cod_auto" id="cod_auto" required>
                    <option value="">-- Seleccione --</option>
                    <?php while ($auto = mysqli_fetch_assoc($resultado_auto)): ?>
                        <option value="<?= $auto['cod_auto'] ?>" <?= $cod_auto == $auto['cod_auto'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($auto['marca'] . ' - ' . $auto['modelo']) ?>
                        </option>
                    <?php endwhile; ?>
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
