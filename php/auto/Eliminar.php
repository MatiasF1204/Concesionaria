<?php
include '../conexion.php';

$mensaje = '';
$tipoMensaje = '';

$cod_auto = $_GET['cod_auto'] ?? null;

if (!$cod_auto || !is_numeric($cod_auto)) {
    $mensaje = "ID de auto inválido.";
    $tipoMensaje = "error";
} else {
    // Verificar si el auto está asociado a una revisión
    $stmt_validar = $conexion->prepare("SELECT 1 FROM revision WHERE cod_auto = ? LIMIT 1");
    $stmt_validar->bind_param("i", $cod_auto);
    $stmt_validar->execute();
    $resultado_validar = $stmt_validar->get_result();

    if ($resultado_validar->num_rows > 0) {
        $mensaje = "No se puede eliminar un auto que está asociado a una revisión.";
        $tipoMensaje = "error";
    } else {
        // Eliminar auto
        $stmt_eliminar = $conexion->prepare("DELETE FROM auto WHERE cod_auto = ?");
        $stmt_eliminar->bind_param("i", $cod_auto);
        if ($stmt_eliminar->execute()) {
            $mensaje = "✅ Auto eliminado correctamente.";
            $tipoMensaje = "exito";
        } else {
            $mensaje = "Error al eliminar auto: " . $stmt_eliminar->error;
            $tipoMensaje = "error";
        }
        $stmt_eliminar->close();
    }

    $stmt_validar->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Eliminar Auto</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f9f9;
            padding: 40px 15px;
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 460px;
            background: #fff;
            padding: 30px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgb(0 0 0 / 0.1);
            text-align: center;
        }

        .mensaje {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 22px;
            padding: 15px 18px;
            border-radius: 6px;
        }

        .mensaje.exito {
            background-color: #e6f4ea;
            color: #207a29;
            border: 1.5px solid #207a29;
        }

        .mensaje.error {
            background-color: #ffe2e2;
            color: #cc0000;
            border: 1.5px solid #cc0000;
        }

        a.button-back {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        a.button-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <p class="mensaje <?= $tipoMensaje ?>"><?= htmlspecialchars($mensaje) ?></p>
        <a href="./Listar.php" class="button-back">Volver a la lista</a>
    </div>
</body>

</html>
