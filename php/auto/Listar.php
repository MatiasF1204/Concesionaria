<?php
include '../conexion.php';

$sql = "SELECT auto.cod_auto, auto.marca, auto.modelo, auto.color, auto.pventa, cliente.nomyape
        FROM auto
        INNER JOIN cliente ON auto.cod_cliente = cliente.cod_cliente";

$resultado = mysqli_query($conexion, $sql);
$cantidad = mysqli_num_rows($resultado);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Listado de Autos</title>
    <link rel="stylesheet" href="../../styles/Listar.css" />
</head>

<body>
    <div class="container">
        <h1 class="title">Listado de Autos</h1>

        <?php if ($cantidad > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Auto</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Color</th>
                        <th>Precio</th>
                        <th>Cliente</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($auto = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?= htmlspecialchars($auto['cod_auto']) ?></td>
                            <td><?= htmlspecialchars($auto['marca']) ?></td>
                            <td><?= htmlspecialchars($auto['modelo']) ?></td>
                            <td><?= htmlspecialchars($auto['color']) ?></td>
                            <td>$<?= number_format($auto['pventa'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($auto['nomyape']) ?></td>
                            <td><a class="btn-modificar" href="./Modificar.php?cod_auto=<?= $auto['cod_auto'] ?>">Modificar</a></td>
                            <td><a class="btn-eliminar" href="./Eliminar.php?cod_auto=<?= $auto['cod_auto'] ?>">Eliminar</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mensaje-vacio">No hay autos registrados.</p>
        <?php endif; ?>

        <div class="back">
            <a href="../../Menu.html" class="btn-volver">Volver al menú</a>
        </div>
    </div>
</body>

</html>
