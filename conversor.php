<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $hsps = $_POST['hsp'];
    $motors = $_POST['motor'];
    $tcs = $_POST['TC'];
    $cargas = $_POST['carga'];
    $cantidads = $_POST['cantidad'];
    $potencia_us = $_POST['potencia_u'];
    $tiemposs = $_POST['tiempo_uso'];

    // Calcular el consumo diario de cada equipo
    $total_watts = 0;
    $datos_equipos = array();

    for ($i = 0; $i < count($cargas); $i++) {
        $hsp = $hsps[$i];
        $motor = $motors[$i];
        $tc = $tcs[$i];
        $carga = $cargas[$i];
        $cantidad = $cantidads[$i];
        $potencia_u = $potencia_us[$i];
        $tiempos = $tiemposs[$i];

        //$potencia_t += $potencia_u;
        $potencia_total = $potencia_u * $tiempos;
        $watts_diarios = $tiempos * $potencia_total;

        // Guardar datos del equipo
        $datos_equipos[] = array('carga' => $carga, 'motor' => $motor, 'tc' => $tc, 'hsp' => $hsp, 
        'cantidad' => $cantidad, 'potencia' => $potencia_u, 'tiempo' => $tiempos, 
        'potencia_total' => $potencia_total, 'watts_diarios' => $watts_diarios);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resultado de la Calculadora</title>
    <link rel="icon" href="img/Logo USTA.png" type="image/x-icon">
    <link rel="shortcut icon" href="img/Logo USTA.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card bg-gradient">
            <div class="card-body">
                <h1 class="text-center display-4 mb-4">Resultado de la Calculadora de Consumo de Energía</h1>
                <div class="table-responsive">
                    <table class="table table-bordered table-light">
                        <thead>
                            <tr>
                                <th>TC</th>
                                <th>Carga</th>
                                <th>Cantidad</th>
                                <th>Potencia unitaria</th>
                                <th>Uso al Día (horas)</th>
                                <th>Potencia total</th>
                                <th>Energia requerida diaria</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($datos_equipos as $equipo): ?>
                                <tr>
                                    <td><?php echo $equipo['tc']; ?></td>
                                    <td><?php echo $equipo['carga']; ?></td>
                                    <td><?php echo $equipo['cantidad']; ?></td>
                                    <td><?php echo $equipo['potencia']; ?></td>
                                    <td><?php echo $equipo['tiempo']; ?></td>
                                    <td><?php echo $equipo['potencia_total']; ?></td>
                                    <td><?php echo $equipo['watts_diarios']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th colspan="3">Total</th>
                                <td><?php echo $total_watts; ?></td>
                                <td><?php echo $total_watts * 0.001; ?></td> <!-- Potencia total en kW -->
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Botón para regresar a index.php -->
                <div class="text-left mt-4">
                    <a href="index.php" class="btn btn-primary">Regresar</a>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>

<?php } ?>
