<?php 
require_once('check_session.php');
include 'vc.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - ResaUGE</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/vc.css">
</head>
<body>
    <nav>
        <div class="nav-left">
            <a href="#" class="logo">Logo ResaUGE</a>
            <a href="admin_dashboard.php">Accueil</a>
            <a href="gestion_salles.php">Salles</a>
            <a href="validation_compte.php" class="active">Utilisateurs</a>
            <a href="statistiques.php">Statistiques</a>
        </div>
        <div class="nav-right">
            <span>admin admin</span>
            <a href="../logout.php">Déconnexion</a>
        </div>
    </nav>

    <div class="main-content">
        <h1>Gestion des Utilisateurs</h1>
        
        <table class="users-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date d'inscription</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($utilisateurs) && is_array($utilisateurs)): ?>
                    <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['prenom']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($user['date_creation'])) ?></td>
                        <td>
                            <span class="status <?= $user['valide'] ? 'status-validated' : 'status-pending' ?>">
                                <?= $user['valide'] ? 'Validé' : 'En attente' ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!$user['valide']): ?>
                            <button class="action-btn validate-btn" onclick="validerCompte(<?= $user['id'] ?>)">
                                Valider
                            </button>
                            <?php endif; ?>
                            <button class="action-btn delete-btn" onclick="supprimerCompte(<?= $user['id'] ?>)">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>ResaUGE</h3>
                <p>Système de réservation de salles<br>Université Gustave Eiffel</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>Email: support@resauge.fr<br>Tél: 01 23 45 67 89</p>
            </div>
            <div class="footer-section">
                <h3>Liens utiles</h3>
                <a href="https://www.univ-gustave-eiffel.fr" target="_blank">Site de l'université</a><br>
                <a href="mentions_legales.php">Mentions légales</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> ResaUGE - Tous droits réservés</p>
        </div>
    </footer>

    <script>
    function validerCompte(userId) {
        if (confirm('Voulez-vous vraiment valider ce compte ?')) {
            fetch('validation_compte.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    userId: userId,
                    action: 'valider'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la validation du compte');
                }
            });
        }
    }

    function supprimerCompte(userId) {
        if (confirm('Voulez-vous vraiment supprimer ce compte ?')) {
            fetch('validation_compte.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    userId: userId,
                    action: 'supprimer'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression du compte');
                }
            });
        }
    }
    </script>
</body>
</html>
