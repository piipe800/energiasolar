<?php
	$servername = "localhost";
    $username = "admin";
    $password = "123456";
    $database = "bd_hsp";
    $port = "3306";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database, $port);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosa";
}

// Consulta SQL para obtener los departamentos
$sql = "SELECT departamento FROM hsp";
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
