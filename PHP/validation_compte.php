<?php
session_start();
header('Content-Type: application/json');

// Vérification de la session et du rôle admin
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    echo json_encode(['error' => 'Accès non autorisé']);
    exit();
}

require 'connexion.php';

$stmt = $pdo->query("SELECT * FROM utilisateur ORDER BY valide ASC, id DESC");
$users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];
    $action = $_POST['action'];
    
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        if ($action === 'validate') {
            $stmt = $pdo->prepare("UPDATE utilisateur SET valide = 1 WHERE id = ?");
            $stmt->execute([$userId]);
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE utilisateur SET valide = 0 WHERE id = ?");
            $stmt->execute([$userId]);
        }
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de comptes - Admin</title>
    <link rel="stylesheet" href="../CSS/stylevalidation.css">
</head>
<body>
    <div class="container">
        <h1>Comptes à Valider</h1>
        <p>Voici la liste des comptes en attente de validation.</p>
        <div class="controls">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Rechercher par nom, prénom, email...">
            </div>
            
            <div class="filter-group">
                <select id="roleFilter">
                    <option value="all">Tous les rôles</option>
                    <option value="student">Étudiant</option>
                    <option value="teacher">Enseignant</option>
                    <option value="agent">Agent</option>
                    <option value="admin">Admin</option>
                </select>
                
                <select id="statusFilter">
                    <option value="all">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="validated">Validés</option>
                </select>
            </div>
        </div>
        
        <div class="table-container">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="nom">Nom</th>
                        <th class="sortable" data-sort="prenom">Prénom</th>
                        <th class="sortable" data-sort="email">Email</th>
                        <th class="sortable" data-sort="role">Rôle</th>
                        <th class="sortable" data-sort="valide">Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr data-id="<?= $user['id'] ?>" data-role="<?= htmlspecialchars($user['role']) ?>" data-status="<?= $user['valide'] ? 'validated' : 'pending' ?>">
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['prenom']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge badge-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'agent' ? 'primary' : 'secondary') ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['valide']): ?>
                                <span class="badge badge-success">Validé</span>
                            <?php else: ?>
                                <span class="badge badge-warning">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if (!$user['valide']): ?>
                                    <button class="btn-validate" onclick="openModal('validate', <?= $user['id'] ?>, '<?= htmlspecialchars($user['prenom']) ?>', '<?= htmlspecialchars($user['nom']) ?>')">Valider</button>
                                    <button class="btn-reject" onclick="openModal('reject', <?= $user['id'] ?>, '<?= htmlspecialchars($user['prenom']) ?>', '<?= htmlspecialchars($user['nom']) ?>')">Rejeter</button>
                                <?php else: ?>
                                    <button class="btn-reject" onclick="openModal('reject', <?= $user['id'] ?>, '<?= htmlspecialchars($user['prenom']) ?>', '<?= htmlspecialchars($user['nom']) ?>')">Invalider</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div id="noResults" class="text-center hidden">
            <p>Aucun compte ne correspond aux critères de recherche.</p>
        </div>
    </div>
    
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Confirmation</h3>
            </div>
            <div class="modal-body" id="modalBody">
                Êtes-vous sûr de vouloir effectuer cette action ?
            </div>
            <div class="modal-footer">
                <button class="secondary" onclick="closeModal()">Annuler</button>
                <form method="POST" id="actionForm">
                    <input type="hidden" name="user_id" id="modalUserId">
                    <input type="hidden" name="action" id="modalAction">
                    <button type="submit" id="confirmButton">Confirmer</button>
                </form>
            </div>
        </div>
    </div>
    <script src="../JS/scriptvalidation.js"></script>
</body>
</html>
