<?php
$servername = "localhost";
$username = "admin";
$password = "123456";
$database = "bd_hsp";
$port = "3306";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database, $port);
$conn->set_charset("utf8");
// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el departamento y el municipio seleccionados
$departamento = $_GET['departamento'];
$municipio = $_GET['municipio'];

// Realizar la consulta a la base de datos para obtener el valor de HSP asociado con el departamento y el municipio
$stmt = $conn->prepare("SELECT hsp FROM hsp WHERE departamento = ? AND municipio = ?");
$stmt->bind_param("ss", $departamento, $municipio);
$stmt->execute();
$result = $stmt->get_result();

// Obtener el valor de HSP
if ($row = $result->fetch_assoc()) {
    $hsp = $row['hsp'];
} else {
    $hsp = "No se encontró ningún dato para el departamento y municipio seleccionados.";
}

// Devolver el valor de HSP en formato JSON
echo json_encode($hsp);

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
