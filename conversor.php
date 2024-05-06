<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombres = $_POST['nombre_equipo'];
    $tiempos = $_POST['tiempo_uso'];
    $consumos = $_POST['consumo'];

    // Calcular el consumo diario de cada equipo
    $total_watts = 0;
    $datos_equipos = array();

    for ($i = 0; $i < count($nombres); $i++) {
        $nombre = $nombres[$i];
        $tiempo = $tiempos[$i];
        $consumo = $consumos[$i];

        $watts_diarios = $tiempo * $consumo;
        $total_watts += $watts_diarios;

        // Guardar datos del equipo
        $datos_equipos[] = array('nombre' => $nombre, 'tiempo' => $tiempo, 'consumo' => $consumo, 'watts_diarios' => $watts_diarios);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resultado de la Calculadora</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Resultado de la Calculadora de Consumo de Energía</h1>
    <table>
        <tr>
            <th>Nombre del Equipo</th>
            <th>Tiempo de Uso al Día (horas)</th>
            <th>Consumo (watts)</th>
            <th>Watts Gastados al Día</th>
            <th>Potencia</th>
        </tr>
        <?php foreach ($datos_equipos as $equipo): ?>
            <tr>
                <td><?php echo $equipo['nombre']; ?></td>
                <td><?php echo $equipo['tiempo']; ?></td>
                <td><?php echo $equipo['consumo']; ?></td>
                <td><?php echo $equipo['watts_diarios']; ?></td>
                <td><?php echo $equipo['consumo'] * 0.001; ?></td> <!-- Potencia en kW -->
            </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="3">Total</th>
            <td><?php echo $total_watts; ?></td>
            <td><?php echo $total_watts * 0.001; ?></td> <!-- Potencia total en kW -->
        </tr>
    </table>
</body>
</html>

<?php } ?>
