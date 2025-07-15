<?php
include '../conexion.php';

$mensaje = '';
$tipoMensaje = '';

$marca = $modelo = $color = $pventa = $cod_cliente = '';

$sql_cliente = "SELECT * FROM cliente";
$resultado_clientes = mysqli_query($conexion, $sql_cliente);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $pventa = trim($_POST['pventa'] ?? '');
    $cod_cliente = trim($_POST['cod_cliente'] ?? '');

    // Validaciones
    if (!$marca || !$modelo || !$color || !$pventa || !$cod_cliente) {
        $mensaje = "Por favor, completa todos los campos.";
        $tipoMensaje = "error";
    } elseif (!is_numeric($pventa) || $pventa <= 0) {
        $mensaje = "El precio debe ser un número positivo.";
        $tipoMensaje = "error";
    } else {
        // Verificar si ya existe un auto con la misma marca, modelo y cliente (opcional)
        $verificacion = $conexion->prepare("SELECT * FROM auto WHERE marca = ? AND modelo = ? AND cod_cliente = ?");
        $verificacion->bind_param("ssi", $marca, $modelo, $cod_cliente);
        $verificacion->execute();
        $resultado = $verificacion->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "Ya existe un auto con esa marca y modelo para este cliente.";
            $tipoMensaje = "error";
        } else {
            $stmt = $conexion->prepare("INSERT INTO auto (marca, modelo, color, pventa, cod_cliente) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                $mensaje = "Error al preparar la consulta: " . $conexion->error;
                $tipoMensaje = "error";
            } else {
                $stmt->bind_param('sssdi', $marca, $modelo, $color, $pventa, $cod_cliente);
                if ($stmt->execute()) {
                    $mensaje = "✅ Auto registrado exitosamente.";
                    $tipoMensaje = "exito";
                    $marca = $modelo = $color = $pventa = $cod_cliente = ''; // limpiar campos
                } else {
                    $mensaje = "Error al registrar auto: " . $stmt->error;
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
    <title>Registrar Auto</title>
    <link rel="stylesheet" href="../../styles/RegistrarCliente.css" />
</head>

<body>
    <div class="container">
        <h1 class="title">Registrar Auto</h1>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje <?= $tipoMensaje ?>"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <form class="form" action="" method="post">

            <div class="form-group">
                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" required minlength="2" value="<?= htmlspecialchars($marca) ?>" />
            </div>

            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" required minlength="1" value="<?= htmlspecialchars($modelo) ?>" />
            </div>

            <div class="form-group">
                <label for="color">Color:</label>
                <input type="text" id="color" name="color" required minlength="2" value="<?= htmlspecialchars($color) ?>" />
            </div>

            <div class="form-group">
                <label for="pventa">Precio:</label>
                <input type="number" id="pventa" name="pventa" required min="1" step="0.01" value="<?= htmlspecialchars($pventa) ?>" />
            </div>

            <div class="form-group">
                <label for="cod_cliente">Cliente:</label>
                <select id="cod_cliente" name="cod_cliente" required>
                    <option value="">-- Ingrese una opción --</option>
                    <?php foreach ($resultado_clientes as $cliente): ?>
                        <option value="<?= $cliente['cod_cliente'] ?>" <?= ($cod_cliente == $cliente['cod_cliente']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cliente['nomyape']) ?>
                        </option>
                    <?php endforeach; ?>
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
