document.addEventListener("DOMContentLoaded", function() {
    // Realizar una solicitud AJAX para obtener los departamentos
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Parsear la respuesta JSON
            var departamentos = JSON.parse(this.responseText);
            
            // Actualizar el contenido del dropdown de departamentos en el HTML
            var departamentoDropdown = document.getElementById("departamento");
            departamentos.forEach(function(departamento) {
                var option = document.createElement("option");
                option.text = departamento.nombre;
                option.value = departamento.id;
                departamentoDropdown.add(option);
            });
        }
    };
    xmlhttp.open("GET", "index.php", true);
    xmlhttp.send();
});