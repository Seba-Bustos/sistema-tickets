<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Datos de conexión a la base de datos
    $servername = "db-2024.mysql.database.azure.com";
    $db_username = "jim";
    $db_password = "2839064Void";
    $dbname = "db-ticket";

    // Crear conexión
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Recibir los datos del formulario
    $username = $_GET['username'];
    $password = $_GET['password'];

    // Consulta SQL para verificar el usuario y la contraseña
    $sql = "SELECT * FROM clientes WHERE cliente_id = '$username' AND pass = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Si hay al menos un resultado, el usuario y la contraseña son correctos
        // Redirigir a home.html
        header("Location: home.html");
        exit(); // Asegura que el script se detenga aquí y no siga ejecutándose
    } else {
        // Si no hay resultados, el usuario y/o la contraseña son incorrectos
        header("Location: usuariofail.html"); // Redirigir al index con un parámetro de error
        exit(); // Asegura que el script se detenga aquí y no siga ejecutándose
    }

    $conn->close();
} else {
    echo "Acceso denegado";
}
?>