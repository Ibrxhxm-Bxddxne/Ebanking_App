<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }

if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

// SÉCURITÉ : Récupérer uniquement les autres utilisateurs pour la liste de sélection
$stmtUsers = $pdo->prepare("SELECT username, first_name, last_name FROM users WHERE id != ?");
$stmtUsers->execute([$_SESSION['user_id']]);
$availableUsers = $stmtUsers->fetchAll();

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erreur de sécurité CSRF.");
    }

    $amount = (float)$_POST['amount'];
    $receiver_name = $_POST['receiver'];
    $sender_id = $_SESSION['user_id'];

    if ($amount <= 0) {
        $error = "Le montant doit être supérieur à 0.";
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$sender_id]);
            if ($stmt->fetchColumn() < $amount) throw new Exception("Solde insuffisant.");

            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$receiver_name]);
            $receiver = $stmt->fetch();
            if (!$receiver) throw new Exception("Utilisateur destinataire introuvable.");
            if ($receiver['id'] == $sender_id) throw new Exception("Vous ne pouvez pas vous envoyer d'argent à vous-même.");

            $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")->execute([$amount, $sender_id]);
            $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")->execute([$amount, $receiver['id']]);
            $pdo->prepare("INSERT INTO transactions (sender_id, receiver_id, amount, description) VALUES (?, ?, ?, 'Virement immédiat')")->execute([$sender_id, $receiver['id'], $amount]);

            $pdo->commit();
            $success = "Virement de $amount € vers $receiver_name effectué !";
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            // Rafraîchir le solde pour l'affichage (optionnel)
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virement - E-Banking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .transfer-card { max-width: 500px; margin: 50px auto; border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        datalist { width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card transfer-card p-4">
            <h4 class="fw-bold mb-4">Nouveau Virement</h4>
            
            <?php if ($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>
            <?php if ($success): ?> <div class="alert alert-success"><?= $success ?></div> <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Destinataire</label>
                    <!-- Utilisation de list="user-list" pour lier l'input au datalist -->
                    <input type="text" name="receiver" id="receiver" class="form-control form-control-lg" 
                           placeholder="Chercher un utilisateur..." list="user-list" autocomplete="off" required>
                    
                    <datalist id="user-list">
                        <?php foreach ($availableUsers as $u): ?>
                            <option value="<?= htmlspecialchars($u['username']) ?>">
                                <?= htmlspecialchars($u['first_name'] . " " . $u['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                    <div class="form-text small">Sélectionnez un utilisateur dans la liste ou tapez son nom.</div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Montant (€)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="amount" class="form-control form-control-lg" placeholder="0.00" required>
                        <span class="input-group-text">€</span>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">Confirmer le transfert</button>
                    <a href="dashboard.php" class="btn btn-light">Annuler et retour</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>