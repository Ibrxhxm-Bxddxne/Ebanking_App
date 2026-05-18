<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require 'db.php';

// Récupérer infos utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Récupérer les 10 dernières transactions
$stmt = $pdo->prepare("
    SELECT t.*, u_send.username as sender_name, u_recv.username as receiver_name 
    FROM transactions t
    JOIN users u_send ON t.sender_id = u_send.id
    JOIN users u_recv ON t.receiver_id = u_recv.id
    WHERE t.sender_id = ? OR t.receiver_id = ?
    ORDER BY t.created_at DESC LIMIT 10
");
$stmt->execute([$user['id'], $user['id']]);
$transactions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - E-Banking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-color: #2c3e50; --accent-color: #3498db; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background-color: var(--primary-color) !important; border-bottom: 3px solid var(--accent-color); }
        .stat-card { border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .btn-transfer { background-color: var(--accent-color); border: none; font-weight: 600; }
        .btn-transfer:hover { background-color: #2980b9; }
        .table-container { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 1.5rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">E-BANKING SECURE</a>
        <div class="ms-auto">
            <span class="text-light me-3">Bonjour, <strong><?= htmlspecialchars($user['username']) ?></strong></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <!-- Carte Profil -->
        <div class="col-md-6 mb-3">
            <div class="card stat-card h-100 p-4">
                <h6 class="text-muted text-uppercase small fw-bold">Titulaire du compte</h6>
                <h3 class="mb-0"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
                <p class="text-muted mb-0"><?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>
        <!-- Carte Solde -->
        <div class="col-md-6 mb-3">
            <div class="card stat-card h-100 p-4 bg-white border-start border-4 border-primary">
                <h6 class="text-muted text-uppercase small fw-bold">Solde Disponible</h6>
                <h2 class="display-6 fw-bold text-primary"><?= number_format($user['balance'], 2, ',', ' ') ?> €</h2>
                <a href="transfer.php" class="btn btn-transfer text-white mt-2">Nouveau Virement</a>
            </div>
        </div>
    </div>

    <!-- Historique -->
    <div class="table-container">
        <h5 class="fw-bold mb-4">Historique des transactions</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Bénéficiaire / Expéditeur</th>
                        <th>Description</th>
                        <th class="text-end">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="4" class="text-center text-muted">Aucune transaction effectuée.</td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                            <td>
                                <?php if ($t['sender_id'] == $user['id']): ?>
                                    <span class="text-danger">⬇ Vers:</span> <strong><?= htmlspecialchars($t['receiver_name']) ?></strong>
                                <?php else: ?>
                                    <span class="text-success">⬆ De:</span> <strong><?= htmlspecialchars($t['sender_name']) ?></strong>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($t['description']) ?></td>
                            <td class="text-end fw-bold <?= $t['sender_id'] == $user['id'] ? 'text-danger' : 'text-success' ?>">
                                <?= $t['sender_id'] == $user['id'] ? '-' : '+' ?> <?= number_format($t['amount'], 2, ',', ' ') ?> €
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>