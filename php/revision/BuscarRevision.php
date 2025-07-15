<?php
include '../conexion.php';

$clientes = mysqli_query($conexion, "SELECT cod_cliente, nomyape FROM cliente");
$autos = mysqli_query($conexion, "SELECT cod_auto, marca, modelo FROM auto");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Revisiones</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f5f7fa, #c3cfe2);
            margin: 0;
            padding: 40px 15px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            max-width: 550px;
            background: #ffffff;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #444;
        }

        select {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        select:focus {
            border-color: #5b8def;
            outline: none;
        }

        button {
            width: 100%;
            background-color: #5b8def;
            color: white;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #4175e0;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 25px;
            text-decoration: none;
            color: #555;
            font-weight: 500;
        }

        a:hover {
            color: #000;
            text-decoration: underline;
        }

        #grupo_cliente, #grupo_auto {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Buscar Revisiones</h1>

    <form action="ProcesarBuscarRevision.php" method="post">
        <div class="form-group">
            <label for="criterio">Buscar por:</label>
            <select name="criterio" id="criterio" required onchange="mostrarOpciones()">
                <option value="">-- Seleccione un criterio --</option>
                <option value="cliente">Cliente</option>
                <option value="auto">Auto</option>
            </select>
        </div>

        <div class="form-group" id="grupo_cliente" style="display: none;">
            <label for="cod_cliente">Seleccione cliente:</label>
            <select name="cod_cliente" id="cod_cliente">
                <option value="">-- Elija un cliente --</option>
                <?php while ($c = mysqli_fetch_assoc($clientes)): ?>
                    <option value="<?= $c['cod_cliente'] ?>"><?= $c['nomyape'] ?></option>
                <?php endwhile ?>
            </select>
        </div>

        <div class="form-group" id="grupo_auto" style="display: none;">
            <label for="cod_auto">Seleccione auto:</label>
            <select name="cod_auto" id="cod_auto">
                <option value="">-- Elija un auto --</option>
                <?php while ($a = mysqli_fetch_assoc($autos)): ?>
                    <option value="<?= $a['cod_auto'] ?>"><?= $a['marca'] . ' - ' . $a['modelo'] ?></option>
                <?php endwhile ?>
            </select>
        </div>

        <button type="submit">Buscar</button>
    </form>

    <a href="../../Menu.html">Volver al menú</a>
</div>

<script>
    function mostrarOpciones() {
        let criterio = document.getElementById('criterio').value;
        document.getElementById('grupo_cliente').style.display = (criterio === 'cliente') ? 'block' : 'none';
        document.getElementById('grupo_auto').style.display = (criterio === 'auto') ? 'block' : 'none';
    }
</script>
</body>
</html>
