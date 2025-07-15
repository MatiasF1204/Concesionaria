<?php
include '../conexion.php';

$criterio = $_POST['criterio'] ?? '';
$revisiones = [];
$mensaje = '';

if ($criterio === 'cliente') {
    $cod_cliente = $_POST['cod_cliente'] ?? '';
    if ($cod_cliente && is_numeric($cod_cliente)) {
        $sql = "SELECT r.*, a.marca, a.modelo 
                FROM revision r 
                INNER JOIN auto a ON r.cod_auto = a.cod_auto 
                WHERE a.cod_cliente = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $cod_cliente);
        $stmt->execute();
        $revisiones = $stmt->get_result();
    } else {
        $mensaje = "Cliente no válido.";
    }
} elseif ($criterio === 'auto') {
    $cod_auto = $_POST['cod_auto'] ?? '';
    if ($cod_auto && is_numeric($cod_auto)) {
        $sql = "SELECT r.*, a.marca, a.modelo 
                FROM revision r 
                INNER JOIN auto a ON r.cod_auto = a.cod_auto 
                WHERE a.cod_auto = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $cod_auto);
        $stmt->execute();
        $revisiones = $stmt->get_result();
    } else {
        $mensaje = "Auto no válido.";
    }
} else {
    $mensaje = "Criterio inválido.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Revisiones encontradas</title>
    <link rel="stylesheet" href="../../styles/Listar.css">
</head>

<body>
    <div class="container">
        <h1 class="title">Revisiones encontradas</h1>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje-vacio"><?= htmlspecialchars($mensaje) ?></p>
        <?php elseif ($revisiones && $revisiones->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Ingreso</th>
                        <th>Fecha Egreso</th>
                        <th>Estado</th>
                        <th>Filtro</th>
                        <th>Aceite</th>
                        <th>Freno</th>
                        <th>Descripción</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rev = $revisiones->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($rev['cod_revision']) ?></td>
                            <td><?= htmlspecialchars($rev['fingreso']) ?></td>
                            <td><?= htmlspecialchars($rev['fegreso']) ?: '-' ?></td>
                            <td><?= htmlspecialchars($rev['estado']) ?></td>
                            <td><?= htmlspecialchars($rev['cambio_filtro']) ?></td>
                            <td><?= htmlspecialchars($rev['cambio_aceite']) ?></td>
                            <td><?= htmlspecialchars($rev['cambio_freno']) ?></td>
                            <td><?= htmlspecialchars($rev['descripcion']) ?></td>
                            <td><?= htmlspecialchars($rev['marca']) ?></td>
                            <td><?= htmlspecialchars($rev['modelo']) ?></td>
                        </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mensaje-vacio">No se encontraron revisiones con ese criterio.</p>
        <?php endif ?>

        <div class="back">
            <a href="BuscarRevision.php" class="btn-volver">Volver a buscar</a>
        </div>
    </div>
</body>

</html>
