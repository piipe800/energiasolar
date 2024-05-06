<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $hsp = $_POST['hsp'];
    $motors = $_POST['motor'];
    $tcs = $_POST['TC'];
    $cargas = $_POST['carga'];
    $cantidads = $_POST['cantidad'];
    $potencia_us = $_POST['potencia_u'];
    $tiemposs = $_POST['tiempo_uso'];
    $autonomia = $_POST['autonomia'];

    // Calcular el consumo diario de cada equipo
    $pd = 0;
    $total_watts = 0;
    $total_watts_ac = 0;
    $total_watts_dc = 0;
    $total_potencia = 0;
    $datos_equipos = array();

    // Variable para formulas
    $vsis = 0;
    $vbat = 0;
    $fs = 0;

    // Constantes
    $eficiencia = 0.9;
    $pdd = 0.5;

    if($autonomia === "3") {
        $fs = 1.3;
    } else {
        $fs = 1.2;
    } 

    for ($i = 0; $i < count($cargas); $i++) {
        $motor = $motors[$i];
        $tc = $tcs[$i];
        $carga = $cargas[$i];
        $cantidad = $cantidads[$i];
        $potencia_u = $potencia_us[$i];
        $tiempos = $tiemposs[$i];

        
        $potencia_total = $potencia_u * $cantidad;
        $watts_diarios = $tiempos * $potencia_total;

        if($tc === "AC") {
            $total_watts_ac += $watts_diarios;
        } else {
            $total_watts_dc += $watts_diarios;
        }

        if($motor === "true") {
            $potencia_un = $potencia_u * 3;
            $potencia_total_pd = $potencia_un * $tiempos;
        } else {
            $potencia_total_pd = $potencia_u * $tiempos;
        }
        $pd += $potencia_total_pd;

        $total_potencia += $potencia_total;
        $total_watts += $watts_diarios;

        // Guardar datos del equipo
        $datos_equipos[] = array('carga' => $carga, 'motor' => $motor, 'tc' => $tc, 'hsp' => $hsp, 
        'cantidad' => $cantidad, 'potencia' => $potencia_u, 'tiempo' => $tiempos, 
        'potencia_total' => $potencia_total, 'watts_diarios' => $watts_diarios);
    }

    if($total_potencia < 2000) {
        $vsis = 12;
        $vbat = 12;
    } else if($total_potencia > 2000 && $total_potencia < 4000) {
        $vsis = 24;
        $vbat = 24;
    } else if ($total_potencia > 4000) {
        $vsis = 48;
        $vbat = 48;
    }

// seleccionar panel
    $datosPanel = [
        "Panel1" => ["potencia" => 550, "voltaje" => 24, "Voc" => 49.68, "Iscc" => 14.01],
        "Panel2" => ["potencia" => 480, "voltaje" => 24, "Voc" => 45.07, "Iscc" => 13.65],
        "Panel3" => ["potencia" => 380, "voltaje" => 24, "Voc" => 45.07, "Iscc" => 13.65],
        "Panel4" => ["potencia" => 340, "voltaje" => 24, "Voc" => 45.07, "Iscc" => 13.65],
        "Panel5" => ["potencia" => 210, "voltaje" => 12, "Voc" => 45.07, "Iscc" => 13.65],
        "Panel6" => ["potencia" => 150, "voltaje" => 12, "Voc" => 45.07, "Iscc" => 13.65],
    ];
    $potencia_10 = $potencia_total * 0.1;
    // Filtrar los paneles cuya potencia sea mayor que la potencia total deseada
    foreach ($datosPanel as $nombre_panel => $datos) {
        if ($datos['potencia'] > $potencia_10) {
            $paneles_candidatos[$nombre_panel] = $datos['potencia'];
        }
    }

    if (empty($paneles_candidatos)) {
        asort($datosPanel);
        $panel_elegido = key($datosPanel);
        $vpanel = $panel_elegido['voltaje'];
        $pnom = $panel_elegido['potencia'];
    } else {
    // Ordenar los paneles candidatos por potencia de forma descendente
    asort($paneles_candidatos);

    // Obtener el primer panel (el de mayor potencia)
    $panel_elegido = key($paneles_candidatos);
    $vpanel = $datosPanel[$panel_elegido]['voltaje'];
    $pnom = $datosPanel[$panel_elegido]['potencia'];
    $voc = $datosPanel[$panel_elegido]['Voc'];
    $isc = $datosPanel[$panel_elegido]['Iscc'];
    }

    
    // Dimensionamiento
    $edt = $total_watts * $fs;
    $ns = $vsis / $vpanel;
    $calNt = $pnom * $hsp;

    // Numero de paneles
    $nt = $edt / $calNt;
    $hhh = $nt;
    
    // Cantidad de paneles en paralelo
    $np = $nt / $ns;

    // Aproximacion numero mayor par
    if (is_int($nt)) {
        // Verificar si es par o impar
        if ($nt % 2 != 0) {
            // Si es impar, sumar 1 para obtener el número par mayor más cercano
            $nt++;
        }
    } else {
        // Si el número es decimal, convertirlo a entero
        $parte_entera = intval($nt);
        // Verificar si la parte entera es par o impar
        if ($parte_entera % 2 != 0) {
            // Si la parte entera es impar, sumar 1 para obtener el número par mayor más cercano
            $parte_entera++;
        }
        // Devolver el número par resultante sumándole la parte decimal original
        $nt = $parte_entera;
    }
    // Aproximacion numero mayor paneless en paraaalelo
    if (is_int($np)) {
        // Verificar si es par o impar
        if ($np % 2 != 0) {
            // Si es impar, sumar 1 para obtener el número par mayor más cercano
            $np++;
        }
    } else {
        // Si el número es decimal, convertirlo a entero
        $parte_entera = intval($np);
        // Verificar si la parte entera es par o impar
        if ($parte_entera % 2 != 0) {
            // Si la parte entera es impar, sumar 1 para obtener el número par mayor más cercano
            $parte_entera++;
        }
        // Devolver el número par resultante sumándole la parte decimal original
        $np = $parte_entera;
    }

    $ah = $edt / $vsis;

    // seleccionar baterias
    $baterias = [
        "bateria1" => ["Capacidad" => 300, "Voltaje" => 12],
        "bateria2" => ["Capacidad" => 250, "Voltaje" => 12],
        "bateria3" => ["Capacidad" => 150, "Voltaje" => 12],
        "bateria4" => ["Capacidad" => 115, "Voltaje" => 12],
        "bateria5" => ["Capacidad" => 100, "Voltaje" => 12]
    ];

    //asort($baterias);
    $batTemp = $baterias;
    $capacidad_total = 0;
    $baterias_seleccionadas = [];
    $brp = 0;
    // Recorrer las baterías
    foreach ($baterias as $nombre_bateria => $datos) {
        $capacidad_g = $datos['Capacidad'];
        //$capacidad_g = $capacidad_g * 3;
        $btemp1 = $ah * $autonomia;
        $btemp2 = $capacidad_g / $pdd;
        $brp = $btemp1 / $btemp2;
        $capacidad_total = $brp;
        $capacidad3 = $capacidad_g * 3;
        // Agregar la batería actual a las seleccionadas
        $baterias_seleccionadas[$nombre_bateria] = $baterias[$nombre_bateria];

        if ($brp < 3 || $capacidad3 > $ah) {
            break;
        }
    }
    $brp = intval($brp);
    $ultima_bateria_seleccionada = end($baterias_seleccionadas);
    $nombre_ultima_bateria = key($baterias_seleccionadas);

    $capacidad_bateria = $baterias[$nombre_ultima_bateria]['Capacidad'];
    $vbat = $baterias[$nombre_ultima_bateria]['Voltaje'];
    ////////

    $brs = $vsis / $vbat;

    // Rgulador
    $voc6 = $voc * $ns;
    $ics6 = $isc * $np;

    // Inversor
    $inv = $pd * 1.2;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Resultado de la Calculadora</title>
    <link rel="icon" href="img/Logo USTA.png" type="image/x-icon">
    <link rel="shortcut icon" href="img/Logo USTA.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <script>
        // Función para igualar la altura de las tarjetas
        function equalizeCardHeight(containerId) {
            var cards = document.querySelectorAll('#' + containerId + ' .card');
            var maxHeight = 0;
            // Encuentra la altura máxima de todas las tarjetas
            cards.forEach(function(card) {
                maxHeight = Math.max(maxHeight, card.offsetHeight);
            });
            // Establece la altura máxima para todas las tarjetas
            cards.forEach(function(card) {
                card.style.height = maxHeight + 'px';
            });
        }

        // Llama a la función para cada contenedor de tarjetas cuando se cargue el DOM
        document.addEventListener('DOMContentLoaded', function() {
            equalizeCardHeight('card-container-1');
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <div class="card bg-gradient">
            <div class="card-body">
                <h1 class="text-center display-4 mb-4">Resultados dimensionamiento del sistema de energía fotovoltaica autónomo</h1>
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
                                <th colspan="5">Total</th>
                                <td><?php echo $total_potencia; ?></td>
                                <td><?php echo $total_watts; ?></td>
                            </tr>
                            <tr>
                                <th colspan="6">Total AC</th>
                                <td><?php echo $total_watts_ac; ?></td>
                            </tr>
                            <tr>
                                <th colspan="6">Total DC</th>
                                <td><?php echo $total_watts_dc; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="container" id="card-container-1">
                    <div class="row justify-content-center">
                        <div class="col-md-5">
                            <div class="card custom-bg d-flex" style="background-color: #f1f2f6; flex-direction: column; justify-content: center;">
                                <h2 class="text-center display-7 mb-7">Panel elegido</h2>
                                <h5>Potencia: <?php echo $pnom; ?></h5>
                                <h5>Voltaje: <?php echo $vpanel; ?></h5>
                                <h5>Voltaje de circuito abierto: <?php echo $voc; ?></h5>
                                <h5>Corriente de corto circuito: <?php echo $isc; ?></h5>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card custom-bg d-flex" style="background-color: #f1f2f6; flex-direction: column; justify-content: center;">
                                <h2 class="text-center display-7 mb-7">Batería elegido</h2>
                                <h5>Capacidad: <?php echo $capacidad_bateria; ?></h5>
                                <h5>Voltaje: <?php echo $vbat; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container" id="card-container-1">
                    <div class="row justify-content-center">
                        <div class="col-md-3">
                            <div class="card custom-bg d-flex" style="background-color: #f1f2f6; flex-direction: column; justify-content: center;">
                                <h2 class="text-center">Cálculo de paneles</h2>
                                <h6>Numero de paneles: <?php echo $nt; ?></h6>
                                <h6>Paneles en paralelo: <?php echo $np; ?></h6>
                                <h6>Paneles en serie: <?php echo $ns; ?></h6>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card custom-bg d-flex" style="background-color: #f1f2f6; flex-direction: column; justify-content: center;">
                                <h2 class="text-center">Cálculo de baterías</h2>
                                <h6>Bateria ramas paralelo: <?php echo $brp; ?></h6>
                                <h6>Bateria ramas en serie: <?php echo $brs; ?></h6>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card custom-bg d-flex" style="background-color: #f1f2f6; flex-direction: column; justify-content: center;">
                                <h2 class="text-center display-7 mb-7">Regulador</h2>
                                <h6>Voltaje del sistema <?php echo $vsis; ?></h6>
                                <h6>Voltaje de circuito abierto: <?php echo $voc6; ?></h6>
                                <h6>Corriente de corto circuito: <?php echo $ics6; ?></h6>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card custom-bg d-flex" style="background-color: #f1f2f6; flex-direction: column; justify-content: center;">
                                <h2 class="text-center display-7 mb-7">Inversor</h2>
                                <h6>Potencia maxima del inversor: <?php echo $inv; ?></h6>
                                <h6>Voltaje del sistema: <?php echo $vsis; ?></h6>
                                <h6>Voltaje de salida: 110V</h6>
                            </div>
                        </div>
                    </div>
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
