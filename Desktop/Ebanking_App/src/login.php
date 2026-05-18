<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        session_regenerate_id(true);
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - E-Banking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-card { max-width: 400px; margin: auto; border: none; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container text-center">
        <div class="card login-card p-5">
            <h1 class="h3 mb-4 fw-bold">E-Banking Secure</h1>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if (isset($_GET['success'])) echo "<div class='alert alert-success'>Compte créé ! Connectez-vous.</div>"; ?>

            <form method="POST">
                <div class="form-floating mb-3">
                    <input type="text" name="username" class="form-control" id="u" placeholder="User" required>
                    <label for="u">Utilisateur</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" name="password" class="form-control" id="p" placeholder="Pass" required>
                    <label for="p">Mot de passe</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary shadow-sm" type="submit">Se connecter</button>
            </form>
            <p class="mt-4"><a href="register.php" class="text-secondary">Pas encore de compte ?</a></p>
        </div>
    </div>
</body>
</html>