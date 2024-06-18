<?php
session_start(); // Inicia la sesión
// Verifica si no hay usuario en sesión
if (!isset($_SESSION['username'])) {
    // Redirige al index si no hay sesión activa
    header("Location: index.html");
    exit();
}

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

// Obtener el cliente_id del usuario actual
$cliente_id = $_SESSION['username'];

// Consulta SQL para obtener los tickets del usuario actual
$sql = "SELECT * FROM tickets WHERE cliente_id = '$cliente_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sistema Soporte de Tickets</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <h1>Bienvenido al Sistema de Soporte de Tickets</h1>
    <h2>Usuario: <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
    <nav>
      <ul>
        <li><a href="create-ticket.php">Crear Nuevo Ticket</a></li>
        <!-- Eliminamos el enlace a view-tickets.php -->
        <li><a href="faq.html">Preguntas Frecuentes</a></li>
        <li><a href="formulario.html">Contactanos</a></li>
        <li><a href="logout.php">Cerrar sesión</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <div class="container">
      
      <!-- Mostrar los tickets del usuario -->
      <section id="ticket-list">
        <h2>Mis Tickets</h2>
        <ul id="tickets">
          <?php
          // Comprobar si hay al menos un ticket
          if ($result->num_rows > 0) {
              // Mostrar los tickets en una lista
              while ($row = $result->fetch_assoc()) {
                  echo "<li>";
                  echo "<strong>Asunto:</strong> " . $row['asunto'] . "<br>";
                  echo "<strong>Descripción:</strong> " . $row['descripcion'] . "<br>";
                  echo "<strong>Estado:</strong> " . $row['estado'] . "<br>";
                  echo "<strong>Fecha de creación:</strong> " . $row['fecha_creacion'] . "<br>";
                  // Puedes añadir más campos aquí si los necesitas
                  echo "</li>";
              }
          } else {
              echo "<li>No se encontraron tickets.</li>";
          }
          ?>
        </ul>
      </section>
    </div>
  </main>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
