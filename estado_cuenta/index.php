<?php

$transacciones = [];

function registrarTransaccion($id, $descripcion, $monto) {
    global $transacciones;

    $nuevaTransaccion = [
        "id" => $id,
        "descripcion" => $descripcion,
        "monto" => $monto
    ];

    array_push($transacciones, $nuevaTransaccion);
}

function generarEstadoDeCuenta() {
    global $transacciones;

    $montoContado = 0;

    foreach ($transacciones as $t) {
        $montoContado += $t["monto"];
    }

    $montoConInteres = $montoContado * 1.026;
    $cashback = $montoContado * 0.001;
    $montoFinal = $montoConInteres - $cashback;

    $contenido = "ESTADO DE CUENTA\n";
    $contenido .= "================\n\n";
    $contenido .= "Transacciones:\n";

    foreach ($transacciones as $t) {
        $contenido .= "ID: " . $t["id"] . " - " . $t["descripcion"] . " - $" . number_format($t["monto"], 2) . "\n";
    }

    $contenido .= "\nMonto de contado: $" . number_format($montoContado, 2) . "\n";
    $contenido .= "Monto con interes (2.6%): $" . number_format($montoConInteres, 2) . "\n";
    $contenido .= "Cash back (0.1%): $" . number_format($cashback, 2) . "\n";
    $contenido .= "Monto final a pagar: $" . number_format($montoFinal, 2) . "\n";

    // desde aca se genera el archivo de texto con el estado de cuenta (parte investigativa)
    $archivo = fopen("estado_cuenta.txt", "w"); // abre o si no existe crea  el archivo en modo escritura
    fwrite($archivo, $contenido); // escribe el contenido del estado de cuenta dentro del archivo 
    fclose($archivo); // cierra el archivo para liberar el recurso y guardar los cambios

    return [
        "transacciones" => $transacciones,
        "montoContado" => $montoContado,
        "montoConInteres" => $montoConInteres,
        "cashback" => $cashback,
        "montoFinal" => $montoFinal
    ];
}

registrarTransaccion(1, "Compra en supermercado", 45000);
registrarTransaccion(2, "Pago de suscripcion streaming", 8500);
registrarTransaccion(3, "Compra en tienda de ropa", 32000);
registrarTransaccion(4, "Pago de restaurante", 21500);

$estado = generarEstadoDeCuenta();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

    <div class="estado-container">
        <main class="content d-flex flex-column">
            <header class="toolbar bg-white p-3 d-flex justify-content-between align-items-center mb-4">
                <h1 class="m-0 fs-3 fw-bold">Estado de cuenta</h1>
            </header>

            <div class="px-4 flex-grow-1">
                <div class="card-custom mb-4">
                    <h2 class="fs-5 mb-3 fw-bold">Transacciones</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descripcion</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estado["transacciones"] as $t): ?>
                            <tr>
                                <td><?= $t["id"] ?></td>
                                <td><?= htmlspecialchars($t["descripcion"]) ?></td>
                                <td>$<?= number_format($t["monto"], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-custom">
                    <h2 class="fs-5 mb-3 fw-bold">Resumen</h2>
                    <p>Monto de contado: <strong>$<?= number_format($estado["montoContado"], 2) ?></strong></p>
                    <p>Monto con interes (2.6%): <strong>$<?= number_format($estado["montoConInteres"], 2) ?></strong></p>
                    <p>Cash back (0.1%): <strong>$<?= number_format($estado["cashback"], 2) ?></strong></p>
                    <p>Monto final a pagar: <strong>$<?= number_format($estado["montoFinal"], 2) ?></strong></p>
                </div>
            </div>
        </main>
    </div>

    <footer class="footer text-center p-3">
        <p class="m-0">Tarea #3 Ambiente Cliente/Servidor. Emily Rojas Garro.</p>
    </footer>

</body>
</html>