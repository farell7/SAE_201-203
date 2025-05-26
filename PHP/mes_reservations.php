<?php
session_start();
require_once 'includes/redirect_role.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit();
}

$user = $_SESSION['utilisateur'];
if ($user['role'] !== 'student') {
    redirect_to_role_home();
}

require_once 'connexion.php';

// Récupérer toutes les réservations de salles
$sql_salles = "SELECT rs.*, s.nom as salle_nom, s.capacite as salle_capacite 
               FROM reservation_salle rs 
               JOIN salle s ON rs.salle_id = s.id 
               WHERE rs.user_id = :user_id 
               ORDER BY rs.date_debut DESC";

$stmt_salles = $conn->prepare($sql_salles);
$stmt_salles->execute(['user_id' => $user['id']]);
$reservations_salles = $stmt_salles->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les réservations et demandes de matériel
$sql_materiel = "SELECT 
    'reservation' as type_demande,
    r.date_debut,
    r.date_fin,
    r.statut,
    r.commentaire,
    m.nom as materiel_nom,
    m.type as materiel_type
FROM reservation_materiel r 
JOIN materiel m ON r.materiel_id = m.id 
WHERE r.user_id = :user_id 

UNION ALL

SELECT 
    'demande' as type_demande,
    dm.date_debut,
    dm.date_fin,
    dm.statut,
    NULL as commentaire,
    dmi.nom_materiel as materiel_nom,
    dmi.nom_materiel as materiel_type
FROM demande_materiel dm
JOIN demande_materiel_items dmi ON dm.id = dmi.demande_id
WHERE dm.user_id = :user_id 

ORDER BY date_debut DESC";

$stmt_materiel = $conn->prepare($sql_materiel);
$stmt_materiel->execute(['user_id' => $user['id']]);
$reservations_materiel = $stmt_materiel->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/student.css">
    <style>
        .reservations-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .reservations-section h2 {
            color: #1d257e;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .reservations-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .reservations-table th,
        .reservations-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .reservations-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #1d257e;
        }

        .reservations-table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state p {
            margin: 10px 0;
        }

        .btn-reserver {
            display: inline-block;
            background-color: #1d257e;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .btn-reserver:hover {
            background-color: #161c60;
        }
    </style>
</head>
<body>
    <nav class="nav-container">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
            <div class="nav-menu">
                <a href="student.php">Accueil</a>
                <a href="reservation_salle.php">Réservations</a>
                <a href="mes_reservations.php" class="active">Mes Réservations</a>
                <a href="profil.php">Mon Compte</a>
            </div>
        </div>
        <div class="profile-menu">
            <a href="profil.php" class="user-info">
                <?php if (!empty($_SESSION['utilisateur']['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['utilisateur']['photo']); ?>" alt="Photo de profil" class="profile-icon">
                <?php else: ?>
                    <img src="../img/profil.png" alt="Photo de profil par défaut" class="profile-icon">
                <?php endif; ?>
                <span><?php echo htmlspecialchars($_SESSION['utilisateur']['prenom'] . ' ' . $_SESSION['utilisateur']['nom']); ?></span>
            </a>
            <a href="../logout.php" class="logout-btn">Déconnexion</a>
        </div>
    </nav>

    <div class="main-content">
        <h1>Mes Réservations</h1>

        <!-- Réservations de salles -->
        <div class="reservations-section">
            <h2>Réservations de Salles</h2>
            <?php if (empty($reservations_salles)): ?>
                <div class="empty-state">
                    <p>Vous n'avez pas encore de réservations de salles.</p>
                    <a href="reservation_salle.php" class="btn-reserver">Réserver une salle</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="reservations-table">
                        <thead>
                            <tr>
                                <th>Salle</th>
                                <th>Capacité</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations_salles as $reservation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reservation['salle_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['salle_capacite']); ?> personnes</td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $reservation['statut'] === 'validee' ? 'badge-success' : 
                                                ($reservation['statut'] === 'en_attente' ? 'badge-warning' : 'badge-danger'); 
                                        ?>">
                                            <?php echo htmlspecialchars($reservation['statut']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Réservations de matériel -->
        <div class="reservations-section">
            <h2>Réservations de Matériel</h2>
            <?php if (empty($reservations_materiel)): ?>
                <div class="empty-state">
                    <p>Vous n'avez pas encore de réservations de matériel.</p>
                    <a href="reservation_materiel.php" class="btn-reserver">Réserver du matériel</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="reservations-table">
                        <thead>
                            <tr>
                                <th>Matériel</th>
                                <th>Type</th>
                                <th>Date de début</th>
                                <th>Date de fin</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations_materiel as $reservation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reservation['materiel_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['materiel_type']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $reservation['statut'] === 'validee' || $reservation['statut'] === 'approuvee' ? 'badge-success' : 
                                                ($reservation['statut'] === 'en_attente' ? 'badge-warning' : 'badge-danger'); 
                                        ?>">
                                            <?php echo htmlspecialchars($reservation['statut']); ?>
                                        </span>
                                        <?php if ($reservation['commentaire']): ?>
                                            <div style="font-size: 0.85rem; color: #666; margin-top: 4px;">
                                                <?php echo htmlspecialchars($reservation['commentaire']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Université Eiffel</p>
    </footer>
</body>
</html> 