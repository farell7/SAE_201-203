<?php
session_start();
require_once '../includes/db.php';

// Vérification de la session
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit();
}

$user = $_SESSION['utilisateur'];

// Initialisation des variables
$reservations_salle = [];
$reservations_materiel = [];

try {
    // Récupération des réservations de salles
    $stmt = $conn->prepare("
        SELECT rs.*, s.nom as salle_nom, s.capacite,
               u.nom as user_nom, u.prenom as user_prenom,
               rs.signature_admin, rs.date_signature, rs.commentaire
        FROM reservation_salle rs 
        JOIN salle s ON rs.salle_id = s.id
        JOIN utilisateur u ON rs.user_id = u.id
        WHERE rs.user_id = ?
        ORDER BY rs.date_debut DESC
    ");
    $stmt->execute([$user['id']]);
    $reservations_salle = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des réservations de matériel
    $stmt = $conn->prepare("
        SELECT r.*, m.nom as materiel_nom, m.type as materiel_type,
               u.nom as user_nom, u.prenom as user_prenom,
               r.signature_admin, r.date_signature, r.commentaire
        FROM reservation_materiel r 
        JOIN materiel m ON r.materiel_id = m.id
        JOIN utilisateur u ON r.user_id = u.id
        WHERE r.user_id = ?
        ORDER BY r.date_debut DESC
    ");
    $stmt->execute([$user['id']]);
    $reservations_materiel = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des réservations : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des Réservations - ResaUGE</title>
    <link rel="stylesheet" href="../CSS/suivi.css">
</head>
<body>
    <nav>
        <div class="nav-left">
            <a href="#" class="logo">Logo ResaUGE</a>
            <a href="admin.php" class="active">Accueil</a>
            <a href="reservation_salle.php" class="active">Salles</a>
            <a href="reservation_materiel.php" class="active">Matériel</a>
            <a href="validation_compte.php" class="active">Utilisateurs</a>
            <a href="statistiques.php" class="active">Statistiques</a>
        </div>
        <div class="nav-right">
            <span><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></span>
            <a href="../logout.php">Déconnexion</a>
        </div>
    </nav>

    <div class="main-content">
        <h1>Suivi des Réservations</h1>

        <!-- Réservations de salles -->
        <h2 class="section-title">Réservations de salles</h2>
        <div class="table-container">
            <table class="reservations-table">
                <thead>
                    <tr>
                        <th>Salle</th>
                        <th>Capacité</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Statut</th>
                        <th>Signature</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations_salle)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Aucune réservation de salle trouvée</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations_salle as $reservation): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservation['salle_nom']) ?></td>
                                <td><?= htmlspecialchars($reservation['capacite']) ?> personnes</td>
                                <td><?= date('d/m/Y H:i', strtotime($reservation['date_debut'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($reservation['date_fin'])) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($reservation['statut']) ?>">
                                        <?= htmlspecialchars($reservation['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($reservation['signature_admin']): ?>
                                        <div class="signature-info">
                                            <?= htmlspecialchars($reservation['signature_admin']) ?>
                                            <br>
                                            <small>Le <?= date('d/m/Y à H:i', strtotime($reservation['date_signature'])) ?></small>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($reservation['commentaire']): ?>
                                        <div class="commentaire">
                                            "<?= htmlspecialchars($reservation['commentaire']) ?>"
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Réservations de matériel -->
        <h2 class="section-title">Réservations de matériel</h2>
        <div class="table-container">
            <table class="reservations-table">
                <thead>
                    <tr>
                        <th>Matériel</th>
                        <th>Type</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Statut</th>
                        <th>Signature</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations_materiel)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Aucune réservation de matériel trouvée</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations_materiel as $reservation): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservation['materiel_nom']) ?></td>
                                <td><?= htmlspecialchars($reservation['materiel_type']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($reservation['date_debut'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($reservation['date_fin'])) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($reservation['statut']) ?>">
                                        <?= htmlspecialchars($reservation['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($reservation['signature_admin']): ?>
                                        <div class="signature-info">
                                            <?= htmlspecialchars($reservation['signature_admin']) ?>
                                            <br>
                                            <small>Le <?= date('d/m/Y à H:i', strtotime($reservation['date_signature'])) ?></small>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($reservation['commentaire']): ?>
                                        <div class="commentaire">
                                            "<?= htmlspecialchars($reservation['commentaire']) ?>"
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="footer">
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
</body>
</html> 