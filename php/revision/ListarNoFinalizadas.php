<?php
include '../conexion.php';

$sql = "SELECT revision.cod_revision, revision.fingreso, revision.fegreso, 
               revision.estado, revision.cambio_filtro, revision.cambio_aceite, 
               revision.cambio_freno, revision.descripcion, 
               auto.marca, auto.modelo
        FROM revision
        INNER JOIN auto ON revision.cod_auto = auto.cod_auto
        WHERE revision.estado != 'Finalizado'";

$resultado = mysqli_query($conexion, $sql);
$cantidad = mysqli_num_rows($resultado);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisiones en curso</title>
    <link rel="stylesheet" href="../../styles/Listar.css">
</head>

<body>
    <div class="container">
        <h1 class="title">Revisiones en Curso</h1>

        <?php if ($cantidad > 0): ?>
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
                    <?php while ($revision = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?= htmlspecialchars($revision['cod_revision']) ?></td>
                            <td><?= htmlspecialchars($revision['fingreso']) ?></td>
                            <td><?= $revision['fegreso'] ? htmlspecialchars($revision['fegreso']) : '-' ?></td>
                            <td><?= htmlspecialchars($revision['estado']) ?></td>
                            <td><?= htmlspecialchars($revision['cambio_filtro']) ?></td>
                            <td><?= htmlspecialchars($revision['cambio_aceite']) ?></td>
                            <td><?= htmlspecialchars($revision['cambio_freno']) ?></td>
                            <td><?= htmlspecialchars($revision['descripcion']) ?></td>
                            <td><?= htmlspecialchars($revision['marca']) ?></td>
                            <td><?= htmlspecialchars($revision['modelo']) ?></td>
                        </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mensaje-vacio">No hay revisiones en curso o pendientes.</p>
        <?php endif ?>

        <div class="back">
            <a href="../../Menu.html" class="btn-volver">Volver al menú</a>
        </div>
    </div>
</body>

</html>
