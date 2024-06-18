<?php
// Iniciar la sesión
session_start();

// Desactivar todas las variables de sesión
$_SESSION = array();

// Borrar la cookie de sesión si se creó
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al usuario al index.html
header("Location: index.html");
exit();
?>
