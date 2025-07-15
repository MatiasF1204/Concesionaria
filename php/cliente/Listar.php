<?php
include '../conexion.php';

$sql = "SELECT * FROM cliente";
$resultado = mysqli_query($conexion, $sql);
$cantidad = mysqli_num_rows($resultado);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Clientes</title>
    <link rel="stylesheet" href="../../styles/Listar.css">
</head>

<body>
    <div class="container">
        <h1 class="title">Listado de Clientes</h1>

        <?php if ($cantidad > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Ciudad</th>
                        <th>Teléfono</th>
                        <th>Fecha de Alta</th>
                        <th>Modificar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cliente = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?php echo $cliente['cod_cliente'] ?></td>
                            <td><?php echo $cliente['nomyape'] ?></td>
                            <td><?php echo $cliente['direccion'] ?></td>
                            <td><?php echo $cliente['ciudad'] ?></td>
                            <td><?php echo $cliente['telefono'] ?></td>
                            <td><?php echo $cliente['falta'] ?></td>
                            <td><a class="btn-modificar" href="./Modificar.php?cod_cliente=<?php echo $cliente['cod_cliente'] ?>">Modificar</a></td>
                            <td><a class="btn-eliminar" href="./Eliminar.php?cod_cliente=<?php echo $cliente['cod_cliente'] ?>">Eliminar</a></td>
                        </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mensaje-vacio">No hay clientes registrados.</p>
        <?php endif ?>

        <div class="back">
            <a href="../../Menu.html" class="btn-volver">Volver al menú</a>
        </div>
    </div>
</body>

</html>
