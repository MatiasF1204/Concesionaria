<?php
include '../conexion.php';

$revisiones = [];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fingreso = $_POST['fingreso'] ?? '';
    $fegreso = $_POST['fegreso'] ?? '';

    if (!$fingreso || !$fegreso) {
        $mensaje = "Por favor, completa ambas fechas.";
    } else {
        $stmt = $conexion->prepare("SELECT revision.cod_revision, revision.fingreso, revision.fegreso,
                                            revision.estado, revision.cambio_filtro, revision.cambio_aceite,
                                            revision.cambio_freno, revision.descripcion,
                                            auto.marca, auto.modelo
                                    FROM revision
                                    INNER JOIN auto ON revision.cod_auto = auto.cod_auto
                                    WHERE revision.fingreso BETWEEN ? AND ?");

        $stmt->bind_param("ss", $fingreso, $fegreso);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $revisiones = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Buscar Revisiones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            gap: 25px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            flex-direction: column;
            margin-bottom: 20px;
        }

        form .date-container {
            display: flex;
            justify-content: center;
            gap: 25px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-volver {
            display: flex;
            justify-content: center;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        a.back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .mensaje {
            color: red;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Buscar Revisiones por Período</h1>
        <form method="POST">
            <div class="date-container">
                <div>
                    <label for="fingreso">Desde:</label>
                    <input type="date" name="fingreso" id="fingreso" required value="<?= htmlspecialchars($_POST['fingreso'] ?? '') ?>">
                </div>
    
                <div>
                    <label for="fegreso">Hasta:</label>
                    <input type="date" name="fegreso" id="fegreso" required value="<?= htmlspecialchars($_POST['fegreso'] ?? '') ?>">
                </div>
            </div>

            <div class="btn-container">
                <button type="submit">Buscar</button>
            </div>
        </form>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <h2>Resultados encontrados</h2>
            <?php if (count($revisiones) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ingreso</th>
                            <th>Egreso</th>
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
                        <?php foreach ($revisiones as $rev): ?>
                            <tr>
                                <td><?= htmlspecialchars($rev['cod_revision']) ?></td>
                                <td><?= htmlspecialchars($rev['fingreso']) ?></td>
                                <td><?= htmlspecialchars($rev['fegreso']) ?></td>
                                <td><?= htmlspecialchars($rev['estado']) ?></td>
                                <td><?= htmlspecialchars($rev['cambio_filtro']) ?></td>
                                <td><?= htmlspecialchars($rev['cambio_aceite']) ?></td>
                                <td><?= htmlspecialchars($rev['cambio_freno']) ?></td>
                                <td><?= htmlspecialchars($rev['descripcion']) ?></td>
                                <td><?= htmlspecialchars($rev['marca']) ?></td>
                                <td><?= htmlspecialchars($rev['modelo']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="mensaje">No se encontraron revisiones en ese rango de fechas.</p>
            <?php endif; ?>
        <?php endif; ?>

        <div class="btn-volver">
            <a href="../../Menu.html" class="back-link">Volver al menú</a>
        </div>
    </div>
</body>
</html>
