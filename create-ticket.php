<?php
session_start(); // Inicia la sesión
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
    <h1>Crear Nuevo Ticket</h1>
    <nav>
      <ul>
        <li><a href="home.php">Inicio</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <div class="container">
      <section id="ticket-form">
        <h2>Crear Nuevo Ticket</h2>
        <!-- Mostrar mensaje de éxito si existe -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
          <p class="success-message">Nuevo ticket creado con éxito.</p>
        <?php endif; ?>
        <form action="send-ticket.php" method="post">
          <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
          <label for="subject">Asunto:</label>
          <input type="text" id="subject" name="subject" required>
          <label for="description">Descripción:</label>
          <textarea id="description" name="description" required></textarea>
          <button type="submit">Enviar Ticket</button>
        </form>
      </section>
    </div>
  </main>
</body>
</html>
