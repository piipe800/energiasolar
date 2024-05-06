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

// Obtener el departamento seleccionado
$departamento = $_GET['departamento'];

// Realizar la consulta a la base de datos para obtener los municipios asociados con el departamento
$stmt = $conn->prepare("SELECT municipio FROM hsp WHERE departamento = ?");
$stmt->bind_param("s", $departamento);
$stmt->execute();
$result = $stmt->get_result();

// Obtener todos los resultados como un array asociativo
$municipios = $result->fetch_all(MYSQLI_ASSOC);

// Devolver los municipios en formato JSON
echo json_encode($municipios);

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
