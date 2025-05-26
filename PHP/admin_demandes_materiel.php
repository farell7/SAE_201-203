<?php
session_start();
require_once 'connexion.php';
require_once 'includes/redirect_role.php';

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    redirect_to_role_home();
}

// Traitement des actions (approuver/refuser)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['demande_id'])) {
    $action = $_POST['action'];
    $demande_id = $_POST['demande_id'];
    
    $nouveau_statut = ($action === 'approuver') ? 'approuvee' : 'refusee';
    $signature_admin = $_SESSION['utilisateur']['prenom'] . ' ' . $_SESSION['utilisateur']['nom'];
    
    $sql_update = "UPDATE demande_materiel SET statut = :statut, signature_admin = :signature WHERE id = :id";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->execute([
        ':statut' => $nouveau_statut,
        ':signature' => $signature_admin,
        ':id' => $demande_id
    ]);
    
    // Rediriger pour éviter la soumission multiple du formulaire
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Récupérer toutes les demandes de matériel avec les items associés
$sql = "SELECT dm.*, GROUP_CONCAT(CONCAT(dmi.nom_materiel, ' (', dmi.quantite, ')') SEPARATOR ', ') as materiels_list
        FROM demande_materiel dm
        LEFT JOIN demande_materiel_items dmi ON dm.id = dmi.demande_id
        GROUP BY dm.id
        ORDER BY dm.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Demandes - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/style.css">
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

        /* Styles pour le tableau et les autres éléments */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        h1 {
            color: #2f2a85;
            margin-bottom: 2rem;
            font-weight: 600;
            font-size: 1.8rem;
        }

        .demandes-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .demandes-table th,
        .demandes-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .demandes-table th {
            background-color: #f8f9fa;
            color: #2f2a85;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .status {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-en_attente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approuvee {
            background-color: #d4edda;
            color: #155724;
        }

        .status-refusee {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-approve {
            background-color: #28a745;
            color: white;
        }

        .btn-reject {
            background-color: #dc3545;
            color: white;
            margin-left: 0.5rem;
        }

        .btn-approve:hover {
            background-color: #218838;
        }

        .btn-reject:hover {
            background-color: #c82333;
        }
    </style>
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
                <a href="gestion_materiel.php" class="active"><i class="fas fa-tools"></i> Matériel</a>
                <a href="validation_compte.php"><i class="fas fa-users"></i> Utilisateurs</a>
                <a href="statistiques.php"><i class="fas fa-chart-bar"></i> Statistiques</a>
            </div>
        </div>
        <div class="nav-right">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['utilisateur']['prenom'] . ' ' . $_SESSION['utilisateur']['nom']); ?></span>
            </div>
            <a href="../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </nav>

    <div class="container">
        <h1>Demandes de matériel</h1>
        
        <table class="demandes-table">
            <thead>
                <tr>
                    <th>Date de demande</th>
                    <th>Étudiant</th>
                    <th>Numéro étudiant</th>
                    <th>Email</th>
                    <th>Date d'emprunt</th>
                    <th>Horaires</th>
                    <th>Matériel demandé</th>
                    <th>Statut</th>
                    <th>Signature Admin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $demande): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($demande['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']); ?></td>
                    <td><?php echo htmlspecialchars($demande['numero_etudiant']); ?></td>
                    <td><?php echo htmlspecialchars($demande['email']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($demande['date_debut'])); ?></td>
                    <td><?php echo date('H:i', strtotime($demande['date_debut'])) . ' - ' . date('H:i', strtotime($demande['date_fin'])); ?></td>
                    <td><?php echo htmlspecialchars($demande['materiels_list'] ?? 'Non spécifié'); ?></td>
                    <td>
                        <span class="status status-<?php echo strtolower($demande['statut']); ?>">
                            <?php echo ucfirst($demande['statut']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($demande['signature_admin'] ?? '-'); ?></td>
                    <td>
                        <?php if ($demande['statut'] === 'en_attente'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="demande_id" value="<?php echo $demande['id']; ?>">
                            <button type="submit" name="action" value="approuver" class="action-btn btn-approve">Approuver</button>
                            <button type="submit" name="action" value="refuser" class="action-btn btn-reject">Refuser</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 