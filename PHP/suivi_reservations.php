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
    $sql = "SELECT rs.*, s.nom as salle_nom, s.capacite, u.nom as user_nom, u.prenom as user_prenom
    FROM reservation_salle rs
    JOIN salle s ON rs.salle_id = s.id
    JOIN utilisateur u ON rs.user_id = u.id
    ORDER BY rs.date_debut DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $reservations_salle = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des réservations de matériel
    $sql = "SELECT r.*, m.nom as materiel_nom, m.type as materiel_type, u.nom as user_nom, u.prenom as user_prenom
    FROM reservation_materiel r
    JOIN materiel m ON r.materiel_id = m.id
    JOIN utilisateur u ON r.user_id = u.id
    ORDER BY r.date_debut DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/suivi.css">
</head>
<body>
    <nav class="nav-container">
        <div class="nav-left">
            <a href="admin.php" class="nav-logo">
                <img src="../img/logo_sansfond.png" alt="Logo">
                <span>ResaUGE</span>
            </a>
            <div class="nav-menu">
                <a href="admin.php"><i class="fas fa-home"></i> Accueil</a>
                <a href="reservation_salle.php"><i class="fas fa-door-open"></i> Salles</a>
                <a href="reservation_materiel.php"><i class="fas fa-tools"></i> Matériel</a>
                <a href="validation_compte.php"><i class="fas fa-users"></i> Utilisateurs</a>
                <a href="statistiques.php"><i class="fas fa-chart-bar"></i> Statistiques</a>
            </div>
        </div>
        <div class="nav-right">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></span>
            </div>
            <a href="../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </nav>

    <style>
        body {
            font-family: 'Noto Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .nav-container {
            background-color: #2f2a85;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Noto Sans', sans-serif;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: white;
            font-weight: 600;
        }

        .nav-logo img {
            height: 40px;
            width: auto;
        }

        .nav-menu {
            display: flex;
            gap: 1.5rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-menu a:hover, .nav-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .nav-menu a i {
            font-size: 1.1rem;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
        }

        .nav-right .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .nav-right a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-right a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
    </style>

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