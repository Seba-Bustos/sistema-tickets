<?php
session_start(); // Reanuda la sesión

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Función para enviar correo electrónico
function enviarCorreo($destinatario, $asunto, $mensaje) {
    $mail = new PHPMailer(true);

    try {
        //Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'soporteticket.g8@gmail.com'; // Correo electrónico desde el que enviarás los correos
        $mail->Password = 'wktrxamweenmfeem'; // Contraseña del correo electrónico
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        //Destinatarios
        $mail->setFrom('soporteticket.g8@gmail.com', 'Sistema Soporte de Tickets');
        $mail->addAddress($destinatario);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Datos de conexión a la base de datos
$servername = "db-2024.mysql.database.azure.com"; //cambiar ruta
$username = "pap";
$password = "seba123+";
$dbname = "db-tickets";

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

    // Preparar la consulta SQL para obtener correos
    $stmt_correos = $conn->prepare("SELECT correo, correo_respaldo FROM clientes WHERE cliente_id = ?");
    $stmt_correos->bind_param("s", $cliente_id);
    $stmt_correos->execute();
    $stmt_correos->store_result();
    $stmt_correos->bind_result($correo_principal, $correo_respaldo);
    $stmt_correos->fetch();
    $stmt_correos->close();

    // Preparar la consulta SQL para insertar el ticket
    $stmt_insert = $conn->prepare("INSERT INTO tickets (cliente_id, asunto, descripcion, estado, fecha_creacion) VALUES (?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("sssss", $cliente_id, $asunto, $descripcion, $estado, $fecha_creacion);

    // Ejecutar la consulta
    if ($stmt_insert->execute() === TRUE) {
        // Enviar correos de confirmación al cliente y al correo de respaldo
        $asuntoCorreo = "Ticket Creado Exitosamente";
        $mensajeCorreo = "Su ticket ha sido creado exitosamente en nuestro sistema.<br><br>Detalles del ticket:<br>Asunto: $asunto<br>Descripción: $descripcion<br>Fecha de creación: $fecha_creacion";

        $enviado_principal = enviarCorreo($correo_principal, $asuntoCorreo, $mensajeCorreo);
        $enviado_respaldo = enviarCorreo($correo_respaldo, $asuntoCorreo, $mensajeCorreo);

        if ($enviado_principal && $enviado_respaldo) {
            // Redirigir a create-ticket.php con un parámetro de éxito
            header("Location: create-ticket.php?success=true");
            exit();
        } else {
            echo "Error al enviar el correo de confirmación.";
        }
    } else {
        echo "Error: " . $stmt_insert->error;
    }

    // Cerrar la declaración
    $stmt_insert->close();
} else {
    echo "Error: Datos del formulario incompletos";
}

// Cerrar conexión
$conn->close();
?>
