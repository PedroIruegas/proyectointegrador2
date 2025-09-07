<?php
session_start();

// Verifica si ya hay sesión activa antes de destruirla
if (isset($_SESSION['usuario'])) {
    session_unset();  // Elimina todas las variables de sesión
    session_destroy();  // Destruye la sesión
}

header("Location: login.php");  // Redirige al login
exit();
?>