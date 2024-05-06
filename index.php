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
<html>
<head>
<meta charset="utf-8">
    <title>Calculadora de Consumo de Energía</title>
</head>
<body>
    <h1>Calculadora de Consumo de Energía</h1>
    <form action="conversor.php" method="post" id="formulario">
        <div id="lugar">
            <label for="departamento">Departamento:</label>
            <select id="departamento" name="departamento" onchange="cargarMunicipios()">
                <option value="">Selecciona un departamento</option>
                <?php foreach ($departamentos as $departamento): ?>
                    <option value="<?php echo $departamento['departamento']; ?>"><?php echo $departamento['departamento']; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="municipio">Municipio:</label>
            <select id="municipio" disabled onchange="cargarHSP()">
                <option value="">Seleccionar</option>
            </select>

            <label for="hsp">HSP:</label>
            <input type="text" id="hsp" readonly>
        </div>
        <div id="equipos">
            <div class="equipo">
                <label for="nombre_equipo">Nombre del Equipo:</label>
                <input type="text" name="nombre_equipo[]" required>
                <label for="tiempo_uso">Tiempo de Uso al Día (horas):</label>
                <input type="number" name="tiempo_uso[]" required>
                <label for="consumo">Consumo (watts):</label>
                <input type="number" name="consumo[]" required>
            </div>
        </div>
        <button type="button" onclick="agregarEquipo()">Agregar Equipo</button>
        <input type="submit" value="Calcular Consumo">
    </form>

    <script>
        function agregarEquipo() {
            var equiposDiv = document.getElementById('equipos');
            var nuevoEquipo = document.createElement('div');
            nuevoEquipo.classList.add('equipo');
            nuevoEquipo.innerHTML = `
                <label for="nombre_equipo">Nombre del Equipo:</label>
                <input type="text" name="nombre_equipo[]" required>
                <label for="tiempo_uso">Tiempo de Uso al Día (horas):</label>
                <input type="number" name="tiempo_uso[]" required>
                <label for="consumo">Consumo (watts):</label>
                <input type="number" name="consumo[]" required>
            `;
            equiposDiv.appendChild(nuevoEquipo);
        }

        function cargarMunicipios() {
            var departamento = document.getElementById("departamento").value;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("GET", "obtener_municipios.php?departamento=" + departamento, true);
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var municipios = JSON.parse(this.responseText);
                    var municipioDropdown = document.getElementById("municipio");
                    municipioDropdown.innerHTML = ""; // Limpiar opciones existentes
                    municipios.forEach(function(municipio) {
                        var option = document.createElement("option");
                        option.text = municipio.municipio;
                        municipioDropdown.add(option);
                    });
                    municipioDropdown.disabled = false; // Habilitar el dropdown de municipios
                }
            };
            
            xmlhttp.send();
        }

        function cargarHSP() {
            var municipio = document.getElementById("municipio").value;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("hsp").value = this.responseText;
                }
            };
            xmlhttp.open("GET", "obtener_hsp.php?municipio=" + municipio, true);
            xmlhttp.send();
        }

    </script>
</body>
</html>
