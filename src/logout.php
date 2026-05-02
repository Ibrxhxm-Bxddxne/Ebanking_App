<?php
session_start();
$_SESSION = array(); // Efface toutes les variables de session
session_destroy(); // Détruit la session sur le serveur

// Supprime le cookie de session côté client
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header("Location: login.php");
exit();