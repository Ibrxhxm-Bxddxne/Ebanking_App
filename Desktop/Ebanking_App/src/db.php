<?php
// Configuration via variables d'environnement (recommandé pour OpenShift)
// Si la variable n'existe pas, on peut définir une valeur par défaut
$host = getenv('DB_HOST') ?: 'mysql'; // 'mysql' est souvent le nom du service par défaut
$db   = getenv('DB_NAME') ?: 'ebanking';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASS') ?: 'pass';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Protection renforcée contre l'injection SQL
];

try {
    // Tentative de connexion
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Message d'erreur plus propre pour le débogage
    error_log("Erreur de connexion MySQL : " . $e->getMessage());
    
    // En production, évitez d'afficher les détails de l'erreur à l'utilisateur
    die("Désolé, l'application ne peut pas se connecter à la base de données pour le moment. Vérifiez vos variables d'environnement.");
}
?>
