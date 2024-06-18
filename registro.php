<?php
// Función para validar RUT (chileno)
function validarRut($rut) {
    $rut = preg_replace('/[^k0-9]/i', '', $rut);
    $dv = substr($rut, -1);
    $numero = substr($rut, 0, strlen($rut) - 1);
    $i = 2;
    $suma = 0;
    foreach (array_reverse(str_split($numero)) as $v) {
        if ($i == 8)
            $i = 2;
        $suma += $v * $i;
        ++$i;
    }
    $dvr = 11 - ($suma % 11);
    if ($dvr == 11)
        $dvr = 0;
    if ($dvr == 10)
        $dvr = 'K';
    if ($dvr == strtoupper($dv))
        return true;
    else
        return false;
}

session_start(); // Inicia la sesión si aún no está iniciada

// Variables para manejar mensajes de error y éxito
$mensaje = "";
$errorRut = "";

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos de conexión a la base de datos
    $servername = "db-tickets.mysql.database.azure.com"; //cambiar ruta
    $db_username = "pap";
    $db_password = "seba123+";
    $dbname = "db-tickets";

    // Crear conexión
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Recibir y limpiar los datos del formulario
    $cliente_id = $_POST['rut'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $fecha_registro = date('Y-m-d H:i:s'); // Fecha y hora actual
    $correo_respaldo = $_POST['correo_respaldo'];
    $pass = $_POST['pass'];

    // Validar RUT (ejemplo de función, deberías tener una función de validación adecuada)
    if (!validarRut($cliente_id)) {
        $errorRut = "El RUT ingresado no es válido.";
    } else {
        // Preparar la consulta SQL
        $stmt = $conn->prepare("INSERT INTO clientes (cliente_id, nombre, correo, telefono, direccion, fecha_registro, correo_respaldo, pass) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $cliente_id, $nombre, $correo, $telefono, $direccion, $fecha_registro, $correo_respaldo, $pass);

        // Ejecutar la consulta
        if ($stmt->execute() === TRUE) {
            $mensaje = "Nuevo cliente registrado con éxito";
        } else {
            $mensaje = "Error: " . $stmt->error;
        }

        // Cerrar la declaración y la conexión
        $stmt->close();
    }

    // Cerrar conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Registro de Nuevo Cliente</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <h1>Registro de Nuevo Cliente</h1>
    <!-- Aquí podrías agregar un menú de navegación si lo necesitas -->
  </header>
  <main>
    <div class="container">
      <section id="registro-form">
        <h2>Registro de Nuevo Cliente</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <label for="rut">RUT:</label>
          <input type="text" id="rut" name="rut" required>
          <?php if (!empty($errorRut)) echo "<p style='color: red;'>$errorRut</p>"; ?>
          <label for="nombre">Nombre:</label>
          <input type="text" id="nombre" name="nombre" required>
          <label for="correo">Correo:</label>
          <input type="email" id="correo" name="correo" required>
          <label for="telefono">Teléfono:</label>
          <input type="text" id="telefono" name="telefono" required>
          <label for="direccion">Dirección:</label>
          <input type="text" id="direccion" name="direccion" required>
          <label for="correo_respaldo">Correo de Respaldo:</label>
          <input type="email" id="correo_respaldo" name="correo_respaldo" required>
          <label for="pass">Contraseña:</label>
          <input type="password" id="pass" name="pass" required>
          <button type="submit">Registrar Cliente</button>
        </form>
        <?php if (!empty($mensaje)) echo "<p>$mensaje</p>"; ?>
      </section>
    </div>
  </main>
</body>
</html>
