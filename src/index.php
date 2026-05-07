<?php
require 'db.php';
session_start();

// Récupération de quelques utilisateurs pour faciliter les tests de virement
try {
    $stmt = $pdo->query("SELECT username, first_name, last_name FROM users LIMIT 6");
    $test_users = $stmt->fetchAll();
} catch (Exception $e) {
    $test_users = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EBANKING Secure | Bienvenue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-color: #2c3e50; --accent-color: #3498db; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .hero-section { background: linear-gradient(135deg, var(--primary-color) 0%, #1a252f 100%); color: white; padding: 100px 0; border-bottom: 5px solid var(--accent-color); }
        .feature-card { border: none; border-radius: 12px; transition: transform 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .feature-card:hover { transform: translateY(-10px); }
        .user-badge { background: #ebf5ff; color: #007bff; border-radius: 20px; padding: 5px 15px; display: inline-block; margin: 5px; font-size: 0.9rem; border: 1px solid #cce5ff; }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">E-BANKING <span class="text-info">SECURE</span></a>
        <div class="d-flex">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-outline-info me-2">Mon Tableau de Bord</a>
                <a href="logout.php" class="btn btn-danger">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light me-2">Connexion</a>
                <a href="register.php" class="btn btn-info text-white">Ouvrir un compte</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section text-center">
    <div class="container">
        <h1 class="display-3 fw-bold mb-4">La banque, en toute sécurité.</h1>
        <p class="lead mb-5 text-light">Une plateforme robuste développée en PHP 8, déployée sur Azure OpenShift, et protégée contre les vulnérabilités OWASP.</p>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-info btn-lg px-5 py-3 shadow-lg fw-bold text-white">Commencer maintenant</a>
        <?php endif; ?>
    </div>
</header>

<!-- Section Utilisateurs de test (Utile pour votre projet) -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold mb-4">Comptes disponibles pour tests</h2>
                <p class="text-muted mb-4">Utilisez ces noms d'utilisateur dans le module de virement pour tester les transactions sécurisées :</p>
                <div class="mb-5">
                    <?php if (!empty($test_users)): ?>
                        <?php foreach ($test_users as $u): ?>
                            <div class="user-badge">
                                <strong>@<?= htmlspecialchars($u['username']) ?></strong> 
                                <span class="text-muted small">(<?= htmlspecialchars($u['first_name']) ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">Aucun utilisateur trouvé. Inscrivez-vous pour apparaître ici !</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4">
                    <div class="h1 text-primary mb-3">🛡️</div>
                    <h5 class="fw-bold">Sécurité Maximale</h5>
                    <p class="text-muted small">Hachage Argon2id, protection CSRF et requêtes préparées PDO contre les injections SQL.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4">
                    <div class="h1 text-info mb-3">☁️</div>
                    <h5 class="fw-bold">Cloud Ready</h5>
                    <p class="text-muted small">Architecture conteneurisée prête pour Azure Red Hat OpenShift (ARO) et scalabilité PaaS.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4">
                    <div class="h1 text-success mb-3">💸</div>
                    <h5 class="fw-bold">Transactions ACID</h5>
                    <p class="text-muted small">Gestion des virements via transactions SQL pour garantir l'intégrité absolue des données.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-dark text-light py-4 mt-5 border-top border-info border-3">
    <div class="container text-center">
        <p class="mb-0 small text-muted">&copy; 2026 E-BANKING Secure - Projet Académique - Développement PHP & Cloud Azure</p>
    </div>
</footer>

</body>
</html>
