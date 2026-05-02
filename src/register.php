<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $pass = $_POST['password'];
    $pass_confirm = $_POST['password_confirm'];

    // SÉCURITÉ : Vérification de la confirmation
    if ($pass !== $pass_confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $hashedPassword = password_hash($pass, PASSWORD_ARGON2ID);
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, username, password, balance) VALUES (?, ?, ?, ?, ?, ?)");
        
        try {
            $stmt->execute([$first_name, $last_name, $email, $username, $hashedPassword, 1000.00]);
            header('Location: login.php?success=1');
            exit();
        } catch (Exception $e) {
            $error = "Erreur : Nom d'utilisateur déjà pris.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - E-Banking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #007bff; border: none; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card p-4">
                    <h2 class="text-center mb-4 text-primary">Créer un compte</h2>
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" name="first_name" class="form-control" placeholder="Prénom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="text" name="last_name" class="form-control" placeholder="Nom" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Nom d'utilisateur" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password_confirm" class="form-control" placeholder="Confirmer le mot de passe" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">S'inscrire</button>
                        <div class="text-center mt-3">
                            <a href="login.php" class="text-decoration-none">Déjà inscrit ? Connexion</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>