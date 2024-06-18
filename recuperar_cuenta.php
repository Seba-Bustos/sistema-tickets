<?php
session_start(); // Iniciar la sesión si aún no está iniciada

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Función para generar una contraseña aleatoria
function generarPassword($longitud = 10) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $longitud_caracteres = strlen($caracteres);
    $password = '';
    for ($i = 0; $i < $longitud; $i++) {
        $password .= $caracteres[rand(0, $longitud_caracteres - 1)];
    }
    return $password;
}

// Función para enviar correo electrónico
function enviarCorreo($destinatario, $asunto, $mensaje) {
    $mail = new PHPMailer(true);

    try {
        //Configuración del servidor
        $mail->SMPTDebug = 2;
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

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Recibir y limpiar el RUT enviado desde el formulario
    $rut = $_POST['rut'];

    // Consulta SQL para verificar si el RUT existe en la tabla clientes
    $sql = "SELECT * FROM clientes WHERE cliente_id = '$rut'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Si el RUT existe, generar nueva contraseña aleatoria
        $nuevaPass = generarPassword(8); // Generar contraseña de 8 caracteres aleatorios

        // Actualizar la contraseña en la base de datos
        $stmt = $conn->prepare("UPDATE clientes SET pass = ? WHERE cliente_id = ?");
        $stmt->bind_param("ss", $nuevaPass, $rut);
        $stmt->execute();
        $stmt->close();

        // Obtener los datos del usuario
        $row = $result->fetch_assoc();
        $correo_principal = $row['correo'];
        $correo_respaldo = $row['correo_respaldo'];

        // Enviar correos electrónicos al correo principal y al correo de respaldo
        $asunto = "Recuperación de Contraseña";
        $mensaje = "Su nueva contraseña para iniciar sesión en el Sistema de Soporte de Tickets es: $nuevaPass";

        $enviado1 = enviarCorreo($correo_principal, $asunto, $mensaje);
        $enviado2 = enviarCorreo($correo_respaldo, $asunto, $mensaje);

        if ($enviado1 && $enviado2) {
            $mensaje = "Se ha enviado un correo a su correo principal y al de respaldo con la nueva contraseña.";
        } else {
            $mensaje = "Hubo un problema al enviar el correo. Por favor, inténtelo nuevamente más tarde.";
        }
    } else {
        // Si el RUT no existe, mostrar un mensaje de error
        $mensaje = "El RUT ingresado no está registrado en nuestro sistema.";
    }

    // Cerrar conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Recuperar Cuenta</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <h1>Recuperar Cuenta</h1>
  </header>
  <main>
    <div class="container">
      <section id="recuperar-form">
        <h2>Recuperar Cuenta</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <label for="rut">RUT:</label>
          <input type="text" id="rut" name="rut" required>
          <button type="submit">Recuperar Cuenta</button>
        </form>
        <?php if (isset($mensaje)) echo "<p style='color: green;'>$mensaje</p>"; ?>
      </section>
    </div>
  </main>
</body>
</html>
