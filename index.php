<!DOCTYPE html>
<html>
<head>
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
                    <option value="<?php echo $departamento['departamento']; ?>"></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="municipio">Municipio:</label>
            <select id="municipio" name="municipio" disabled>
                <option value="">Selecciona un municipio</option>
                <!-- Aquí se cargarán los municipios según el departamento seleccionado -->
            </select>
            <br>
            <label for="hsp">Horas de sol diarias:</label>
            <input type="text" id="hsp" name="hsp" readonly>
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
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("municipio").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "obtener_municipios.php?departamento=" + departamento, true);
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

        document.addEventListener("DOMContentLoaded", function() {
            var departamentoSelect = document.getElementById('departamento');
            var municipioSelect = document.getElementById('municipio');
            var hspInput = document.getElementById('hsp');

            // Manejar el cambio en el dropdown de departamento
            departamentoSelect.addEventListener('change', function() {
                var departamentoId = this.value;
                if (departamentoId) {
                    // Habilitar el dropdown de municipio
                    municipioSelect.disabled = false;
                    // Obtener los municipios para el departamento seleccionado
                    fetch('get_municipios.php?departamento_id=' + departamentoId)
                        .then(response => response.json())
                        .then(data => {
                            // Limpiar el dropdown de municipio
                            municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';
                            // Llenar el dropdown de municipio con los datos obtenidos
                            data.forEach(municipio => {
                                var option = document.createElement('option');
                                option.value = municipio.id;
                                option.textContent = municipio.nombre;
                                municipioSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error al obtener los municipios:', error);
                        });
                } else {
                    // Deshabilitar y limpiar el dropdown de municipio si no se selecciona ningún departamento
                    municipioSelect.disabled = true;
                    municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';
                    // Limpiar el input de hsp
                    hspInput.value = '';
                }
            });

            // Manejar el cambio en el dropdown de municipio
            municipioSelect.addEventListener('change', function() {
                var municipioId = this.value;
                if (municipioId) {
                    // Obtener las horas de sol diarias para el municipio seleccionado
                    // Esto también podría hacerse a través de una solicitud AJAX si los datos no se cargaron previamente
                    // Supongamos que obtienes las horas de sol diarias de la base de datos y las asignas a una variable hsp
                    var hsp = 8; // Ejemplo, reemplaza con el valor real
                    // Asignar las horas de sol diarias al input correspondiente
                    hspInput.value = hsp;
                } else {
                    // Limpiar el input de hsp si no se selecciona ningún municipio
                    hspInput.value = '';
                }
            });
        });

    </script>
</body>
</html>
