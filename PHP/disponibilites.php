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

// Récupérer toutes les salles avec leurs réservations
$sql_salles = "SELECT s.*, 
               (SELECT COUNT(*) FROM reservation_salle rs 
                WHERE rs.salle_id = s.id 
                AND rs.statut = 'validee'
                AND (
                    (rs.date_debut <= NOW() AND rs.date_fin >= NOW())
                    OR (rs.date_debut >= NOW() AND rs.date_debut <= DATE_ADD(NOW(), INTERVAL 7 DAY))
                )) as reservations_actives
               FROM salle s 
               WHERE s.disponible = 1
               ORDER BY s.nom";

$stmt_salles = $conn->prepare($sql_salles);
$stmt_salles->execute();
$salles = $stmt_salles->fetchAll(PDO::FETCH_ASSOC);

// Récupérer tout le matériel avec leurs réservations
$sql_materiel = "SELECT m.*, 
                 (SELECT COUNT(*) FROM reservation_materiel rm 
                  WHERE rm.materiel_id = m.id 
                  AND rm.statut = 'validee'
                  AND (
                      (rm.date_debut <= NOW() AND rm.date_fin >= NOW())
                      OR (rm.date_debut >= NOW() AND rm.date_debut <= DATE_ADD(NOW(), INTERVAL 7 DAY))
                  )) as reservations_actives
                 FROM materiel m 
                 WHERE m.disponible = 1
                 ORDER BY m.type, m.nom";

$stmt_materiel = $conn->prepare($sql_materiel);
$stmt_materiel->execute();
$materiels = $stmt_materiel->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilités - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/student.css">
    <style>
        .disponibilites-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .disponibilites-section h2 {
            color: #1d257e;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .resource-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #e9ecef;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .resource-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .resource-card h3 {
            color: #1d257e;
            margin: 0 0 8px 0;
            font-size: 1.2rem;
        }

        .resource-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e9ecef;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-available {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-busy {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .resource-details {
            color: #666;
            font-size: 0.9rem;
            margin: 8px 0;
        }

        .resource-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .btn-reserver {
            display: inline-block;
            background-color: #1d257e;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .btn-reserver:hover {
            background-color: #161c60;
        }

        .filters {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
        }

        .filter-select {
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
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
                <a href="mes_reservations.php">Mes Réservations</a>
                <a href="disponibilites.php" class="active">Disponibilités</a>
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
        <h1>Disponibilités</h1>

        <!-- Disponibilités des salles -->
        <div class="disponibilites-section">
            <h2>Salles</h2>
            <?php if (empty($salles)): ?>
                <div class="empty-state">
                    <p>Aucune salle n'est disponible pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="grid-container">
                    <?php foreach ($salles as $salle): ?>
                        <div class="resource-card">
                            <?php if ($salle['photo']): ?>
                                <img src="<?php echo htmlspecialchars($salle['photo']); ?>" alt="<?php echo htmlspecialchars($salle['nom']); ?>" class="resource-image">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($salle['nom']); ?></h3>
                            <div class="resource-details">
                                <p>Type: <?php echo htmlspecialchars($salle['type']); ?></p>
                                <p>Capacité: <?php echo htmlspecialchars($salle['capacite']); ?> personnes</p>
                                <?php if ($salle['equipements']): ?>
                                    <p>Équipements: <?php echo htmlspecialchars($salle['equipements']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="resource-info">
                                <span class="status-badge <?php echo $salle['reservations_actives'] > 0 ? 'status-busy' : 'status-available'; ?>">
                                    <?php echo $salle['reservations_actives'] > 0 ? 'Occupée' : 'Disponible'; ?>
                                </span>
                                <a href="reservation_salle.php" class="btn-reserver">Réserver</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Disponibilités du matériel -->
        <div class="disponibilites-section">
            <h2>Matériel</h2>
            <?php if (empty($materiels)): ?>
                <div class="empty-state">
                    <p>Aucun matériel n'est disponible pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="grid-container">
                    <?php foreach ($materiels as $materiel): ?>
                        <div class="resource-card">
                            <?php if ($materiel['photo']): ?>
                                <img src="<?php echo htmlspecialchars($materiel['photo']); ?>" alt="<?php echo htmlspecialchars($materiel['nom']); ?>" class="resource-image">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($materiel['nom']); ?></h3>
                            <div class="resource-details">
                                <p>Type: <?php echo htmlspecialchars($materiel['type']); ?></p>
                                <?php if ($materiel['description']): ?>
                                    <p><?php echo htmlspecialchars($materiel['description']); ?></p>
                                <?php endif; ?>
                                <p>État: <?php echo htmlspecialchars($materiel['etat']); ?></p>
                            </div>
                            <div class="resource-info">
                                <span class="status-badge <?php echo $materiel['reservations_actives'] > 0 ? 'status-busy' : 'status-available'; ?>">
                                    <?php echo $materiel['reservations_actives'] > 0 ? 'Emprunté' : 'Disponible'; ?>
                                </span>
                                <a href="reservation_materiel.php" class="btn-reserver">Réserver</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Université Eiffel</p>
    </footer>
</body>
</html> 