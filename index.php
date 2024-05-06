<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Consumo de Energía</title>
    <link rel="icon" href="img/Logo USTA.png" type="image/x-icon">
    <link rel="shortcut icon" href="img/Logo USTA.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid bg-gradient">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="text-center display-4">Calculadora de Consumo de Energía</h1>
                        <form action="conversor.php" method="post" id="formulario">
                            <div class="row mb-3">
                                <!-- Primer formulario -->
                                <div class="col primer-formulario">
                                    <label for="departamento">Departamento:</label>
                                    <select id="departamento" name="departamento" onchange="cargarMunicipios()" class="form-select">
                                        <option value="">Selecciona un departamento</option>
                                        <?php foreach ($departamentos as $departamento): ?>
                                            <option value="<?php echo $departamento['departamento']; ?>"><?php echo $departamento['departamento']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Segundo formulario -->
                                <div class="col">
                                    <label for="municipio">Municipio:</label>
                                    <select id="municipio" name="municipio" disabled class="form-select">
                                        <option value="">Selecciona un municipio</option>
                                    </select>
                                </div>
                                <!-- Tercer formulario -->
                                <div class="col">
                                    <label for="hsp">Horas de sol diarias:</label>
                                    <input type="text" id="hsp" name="hsp" readonly class="form-control">
                                </div>
                            </div>
                            <!-- Formularios para los equipos -->
                            <h2>Equipos en el Hogar</h2>
                            <div id="equipos">
                                <!-- Los primeros tres formularios -->
                                <div class="row mb-3">
                                    <div class="col">
                                        
                                        <label for="nombre_equipo">Nombre del Equipo:</label>
                                        <input type="text" name="nombre_equipo[]" required class="form-control">
                                    </div>
                                    <div class="col">
                                        <label for="tiempo_uso">Tiempo de Uso al Día (horas):</label>
                                        <input type="number" name="tiempo_uso[]" required class="form-control">
                                    </div>
                                    <div class="col">
                                        <label for="consumo">Consumo (watts):</label>
                                        <input type="number" name="consumo[]" required class="form-control">
                                    </div>
                                </div>
                            </div>
                            <br>
                            <button type="button" onclick="agregarEquipo()" class="btn btn-primary">Agregar Equipo</button>
                            <input type="submit" value="Calcular Consumo" class="btn btn-success">
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
            nuevaFila.classList.add('row', 'mb-3');
            nuevaFila.innerHTML = `
                <div class="col">
                    <label for="nombre_equipo">Nombre del Equipo:</label>
                    <input type="text" name="nombre_equipo[]" required class="form-control">
                </div>
                <div class="col">
                    <label for="tiempo_uso">Tiempo de Uso al Día (horas):</label>
                    <input type="number" name="tiempo_uso[]" required class="form-control">
                </div>
                <div class="col">
                    <label for="consumo">Consumo (watts):</label>
                    <input type="number" name="consumo[]" required class="form-control">
                </div>
            `;
            equiposDiv.appendChild(nuevaFila);
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
