<?php
session_start(); // Reanuda la sesión

// Datos de conexión a la base de datos
$servername = "db-2024.mysql.database.azure.com";
$username = "jim";
$password = "2839064Void";
$dbname = "db-ticket";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si los datos del formulario están presentes
if (isset($_POST['cliente_id']) && isset($_POST['subject']) && isset($_POST['description'])) {
    // Obtener datos del formulario
    $cliente_id = $_POST['cliente_id']; // Obtener el cliente_id del formulario
    $asunto = $_POST['subject'];
    $descripcion = $_POST['description'];
    $estado = 'abierto';
    $fecha_creacion = date('Y-m-d H:i:s');

    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO tickets (cliente_id, asunto, descripcion, estado, fecha_creacion) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $cliente_id, $asunto, $descripcion, $estado, $fecha_creacion);

    // Ejecutar la consulta
    if ($stmt->execute() === TRUE) {
        // Redirigir a create-ticket.php con un parámetro de éxito
        header("Location: create-ticket.php?success=true");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo "Error: Datos del formulario incompletos";
}

// Cerrar conexión
$conn->close();
?>
