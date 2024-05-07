<?php
	$servername = "localhost";
    $username = "admin";
    $password = "123456";
    $database = "bd_hsp";
    $port = "3306";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database, $port);
$conn->set_charset("utf8");
// Consulta SQL para obtener los departamentos
$sql = "SELECT DISTINCT departamento FROM hsp";
$result = $conn->query($sql);

// Verificar si hay resultados y almacenarlos en un array
$departamentos = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departamentos[] = $row;
    }
} else {
    echo "0 resultados encontrados";
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Consumo de Energía</title>
    <link rel="icon" href="img/Logo USTA.png" type="image/x-icon">
    <link rel="shortcut icon" href="img/Logo USTA.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid bg-gradient">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="text-center display-4">Dimensionamiento de sistema de energía fotovoltaica utonomo</h1>
                        <form action="conversor.php" method="post" id="formulario">
                            <div class="row mb-3">
                                <!-- Primer formulario -->
                                <div class="col primer-formulario">
                                    <label for="departamento">Departamento:</label>
                                    <select id="departamento" name="departamento" required onchange="cargarMunicipios()" class="form-select">
                                        <option value="">Selecciona un departamento</option>
                                        <?php foreach ($departamentos as $departamento): ?>
                                            <option value="<?php echo $departamento['departamento']; ?>"><?php echo $departamento['departamento']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Segundo formulario -->
                                <div class="col">
                                    <label for="municipio">Municipio:</label>
                                    <select id="municipio" name="municipio" disabled required class="form-select" onchange="cargarHSP()">
                                        <option value="">Selecciona un municipio</option>
                                    </select>
                                </div>
                                <!-- Tercer formulario -->
                                <div class="col">
                                    <label for="hsp">Horas de sol pico:</label>
                                    <input type="text" id="hsp" name="hsp" required readonly class="form-control">
                                </div>
                                <div class="col">
                                    <label for="autonomia">Autonomía:</label>
                                    <select id="autonomia" name="autonomia" required class="form-select">
                                        <option value="">Selecciona uno</option>
                                        <option value="3">Diario</option>
                                        <option value="2">Fines de semana</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Formularios para los equipos -->
                            <h2>Equipos (carga) en el Hogar</h2>
                            <div id="equipos">
                                <!-- Los primeros tres formularios -->
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="tc">TC:</label>
                                        <select id="tc" name="TC[]" required class="form-select">
                                        <option value="">Seleccione uno</option>
                                            <option value="AC">AC</option>
                                            <option value="DC">DC</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="carga">Carga:</label>
                                        <input type="text" name="carga[]" required class="form-control">
                                    </div>
                                    <div class="col">
                                        <label for="cantidad">Cantidad:</label>
                                        <input type="text" name="cantidad[]" required class="form-control">
                                    </div>
                                    <div class="col">
                                        <label for="potencia_u">Potencia unitaria:</label>
                                        <input type="text" name="potencia_u[]" required class="form-control">
                                    </div>
                                    <div class="col">
                                        <label for="tiempo_uso">Uso al Día (horas):</label>
                                        <input type="number" step="any" name="tiempo_uso[]" max="24" required class="form-control">
                                    </div>
                                    <div class="col">
                                    <label for="motor">¿Tiene motor?:</label>
                                    <select id="motor" name="motor[]" required class="form-select">
                                        <option value="">Selecciona uno</option>
                                        <option value="true">Si</option>
                                        <option value="false">No</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col">
                                    <button type="button" onclick="agregarEquipo()" class="btn btn-primary">Agregar carga</button>
                                    <input type="submit" value="Calcular Consumo" class="btn btn-success">
                                </div>
                                <div class="row align-items-end">
                                    <div class="col text-right" style="text-align: end;">
                                        <a href="https://1drv.ms/b/s!Agv1Z7ClqnRS8QTfkI0P-Odskxu4?e=DBBBsL" target="_blank">Manual de uso</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function agregarEquipo() {
        var equiposDiv = document.getElementById('equipos');
        var nuevaFila = document.createElement('div');
        nuevaFila.classList.add('row', 'mb-3', 'align-items-end');
        nuevaFila.innerHTML = `
            <div class="col">
                <label for="tc">TC:</label>
                <select id="tc" name="TC[]" required class="form-select">
                <option value="">Seleccione uno</option>
                    <option value="AC">AC</option>
                    <option value="DC">DC</option>
                </select>
            </div>
            <div class="col">
                <label for="carga">Carga:</label>
                <input type="text" name="carga[]" required class="form-control">
            </div>
            <div class="col">
                <label for="cantidad">Cantidad:</label>
                <input type="text" name="cantidad[]" required class="form-control">
            </div>
            <div class="col">
                <label for="potencia_u">Potencia unitaria:</label>
                <input type="text" name="potencia_u[]" required class="form-control">
            </div>
            <div class="col">
                <label for="tiempo_uso">Uso al Día (horas):</label>
                <input type="number" step="any" name="tiempo_uso[]"  max="24" required class="form-control">
            </div>
            <div class="col">
                <label for="motor">¿Tiene motor?:</label>
                <select id="motor" name="motor" required class="form-select">
                    <option value="">Selecciona uno</option>
                    <option value="true">Si</option>
                    <option value="false">No</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="button" onclick="eliminarEquipo(this)" class="btn btn-danger w-100">
                    <i class="fas fa-trash-alt"></i> 
                </button>
            </div>
        `;
        equiposDiv.appendChild(nuevaFila);
    }

    function eliminarEquipo(elemento) {
        var fila = elemento.parentNode.parentNode;
        fila.parentNode.removeChild(fila);
    }


        function cargarMunicipios() {
            var departamento = document.getElementById("departamento").value;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("GET", "obtener_municipios.php?departamento=" + departamento, true);
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var municipios = JSON.parse(this.responseText);
                    var municipioDropdown = document.getElementById("municipio");
                    municipioDropdown.innerHTML = "";
            
            // Agregar la opción "Seleccionar" al principio del dropdown
            var opcionSeleccionar = document.createElement('option');
            opcionSeleccionar.value = "";
            opcionSeleccionar.text = "Seleccionar";
            municipioDropdown.appendChild(opcionSeleccionar);
            
            // Iterar sobre los municipios y agregarlos al dropdown
            municipios.forEach(function(municipio) {
                var option = document.createElement("option");
                option.text = municipio.municipio;
                municipioDropdown.appendChild(option);
            });
                    municipioDropdown.disabled = false; // Habilitar el dropdown de municipios
                    
                }
            };
            
            xmlhttp.send();
        }

        function cargarHSP() {
            var municipio = document.getElementById("municipio").value;
            var departamento = document.getElementById("departamento").value;
            if(municipio !== "") {
                console.log('entro');
                console.log(municipio);
                var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("GET", "obtener_hsp.php?municipio=" + municipio + '&departamento=' + departamento, true);
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("hsp").value = this.responseText;
                }
            };
            
            xmlhttp.send();
            } else {
                document.getElementById("hsp").value = "";
            }
        }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
