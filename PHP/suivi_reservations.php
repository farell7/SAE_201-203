<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$host = 'localhost';
$dbname = 'resauge';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des réservations de matériel validées
    $sql_materiel = "SELECT r.*, m.nom as materiel_nom, m.type as materiel_type,
                   u.nom as user_nom, u.prenom as user_prenom
            FROM reservation_materiel r 
            JOIN materiel m ON r.materiel_id = m.id
            LEFT JOIN utilisateur u ON r.user_id = u.id
            WHERE r.statut = 'validee'
            ORDER BY r.date_signature DESC";
    
    $reservations_materiel = $pdo->query($sql_materiel)->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des réservations de salles validées
    $sql_salles = "SELECT rs.*, s.nom as salle_nom, s.capacite,
                   u.nom as user_nom, u.prenom as user_prenom
            FROM reservation_salle rs 
            JOIN salle s ON rs.salle_id = s.id
            LEFT JOIN utilisateur u ON rs.user_id = u.id
            WHERE rs.statut = 'validee'
            ORDER BY rs.date_signature DESC";
    
    $reservations_salles = $pdo->query($sql_salles)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "Erreur de connexion : " . $e->getMessage();
    $messageType = "error";
    $reservations_materiel = [];
    $reservations_salles = [];
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
    <link rel="stylesheet" href="../CSS/gestion_materiel.css">
    <style>
        .section-title {
            margin-top: 2rem;
            padding: 1rem;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="../index.php">Tableau de bord</a>
            <a href="gestion_materiel.php">Gestions</a>
            <a href="suivi_reservations.php" class="active">Suivi</a>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <div class="menu-icon">☰</div>
        </div>
    </nav>

    <main class="main-content">
        <h1>Suivi des Réservations</h1>

        <!-- Tableau des réservations de matériel validées -->
        <div class="table-container">
            <h2 class="section-title">Historique des réservations de matériel</h2>
            <div class="table-wrapper">
                <table class="gestion-table">
                    <thead>
                        <tr>
                            <th>Matériel</th>
                            <th>Type</th>
                            <th>Emprunteur</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Date validation</th>
                            <th>Signature Admin</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservations_materiel)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">Aucune réservation de matériel validée trouvée</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservations_materiel as $reservation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reservation['materiel_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['materiel_type']); ?></td>
                                    <td>
                                        <?php 
                                        if ($reservation['user_nom'] && $reservation['user_prenom']) {
                                            echo htmlspecialchars($reservation['user_prenom'] . ' ' . $reservation['user_nom']);
                                        } else {
                                            echo "Utilisateur inconnu";
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                    <td><?php echo $reservation['date_signature'] ? date('d/m/Y H:i', strtotime($reservation['date_signature'])) : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($reservation['signature_admin'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['commentaire'] ?: '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tableau des réservations de salles validées -->
        <div class="table-container">
            <h2 class="section-title">Historique des réservations de salles</h2>
            <div class="table-wrapper">
                <table class="gestion-table">
                    <thead>
                        <tr>
                            <th>Salle</th>
                            <th>Capacité</th>
                            <th>Réservé par</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Date validation</th>
                            <th>Signature Admin</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservations_salles)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">Aucune réservation de salle validée trouvée</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservations_salles as $reservation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reservation['salle_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['capacite']); ?> personnes</td>
                                    <td>
                                        <?php 
                                        if ($reservation['user_nom'] && $reservation['user_prenom']) {
                                            echo htmlspecialchars($reservation['user_prenom'] . ' ' . $reservation['user_nom']);
                                        } else {
                                            echo "Utilisateur inconnu";
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                    <td><?php echo $reservation['date_signature'] ? date('d/m/Y H:i', strtotime($reservation['date_signature'])) : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($reservation['signature_admin'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['commentaire'] ?: '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="footer">
        &copy;2025 Université Eiffel. Tous droits réservés.
    </footer>
</body>
</html> 