<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/redirect_role.php';

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    redirect_to_role_home();
}

require_once 'connexion.php';

// Traitement de la validation/refus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $action = $_POST['action'];
        $userId = $_POST['user_id'];
        
        if ($action === 'valider') {
            $stmt = $conn->prepare("UPDATE utilisateur SET valide = 1 WHERE id = ?");
            $stmt->execute([$userId]);
            $_SESSION['message'] = "L'utilisateur a été validé avec succès.";
        } elseif ($action === 'refuser') {
            $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id = ?");
            $stmt->execute([$userId]);
            $_SESSION['message'] = "L'utilisateur a été refusé et supprimé.";
        }
        
        header('Location: validation_compte.php');
        exit();
    }
}

// Récupérer la liste des utilisateurs
$sql = "SELECT * FROM utilisateur ORDER BY date_creation DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - ResaUGE</title>
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

        .main-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: #2f2a85;
            margin-bottom: 2rem;
            font-weight: 600;
            font-size: 1.8rem;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .users-table th {
            background-color: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #2f2a85;
            font-size: 0.95rem;
        }

        .users-table td {
            padding: 1rem;
            border-top: 1px solid #dee2e6;
            font-size: 0.95rem;
        }

        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-valide {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-en-attente {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .btn-supprimer {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            font-family: 'Noto Sans', sans-serif;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-supprimer:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }

        .btn-supprimer i {
            font-size: 0.9rem;
        }

        .btn-valider {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
        }
        
        .btn-refuser {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-valider:hover {
            background-color: #218838;
        }
        
        .btn-refuser:hover {
            background-color: #c82333;
        }
        
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        tr:hover {
            background-color: #f8f9fa;
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
                <a href="reservation_materiel.php"><i class="fas fa-tools"></i> Matériel</a>
                <a href="validation_compte.php" class="active"><i class="fas fa-users"></i> Utilisateurs</a>
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

    <div class="main-content">
        <h1>Gestion des Utilisateurs</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message">
                <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

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
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['email']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['role']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($utilisateur['date_creation'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo $utilisateur['valide'] ? 'status-valide' : 'status-en-attente'; ?>">
                                <?php echo $utilisateur['valide'] ? 'Validé' : 'En attente'; ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!$utilisateur['valide']): ?>
                                <form action="validation_compte.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $utilisateur['id']; ?>">
                                    <button type="submit" name="action" value="valider" class="btn-valider">
                                        <i class="fas fa-check"></i> Valider
                                    </button>
                                    <button type="submit" name="action" value="refuser" class="btn-refuser">
                                        <i class="fas fa-times"></i> Refuser
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn-supprimer" onclick="supprimerUtilisateur(<?php echo $utilisateur['id']; ?>)">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    function supprimerUtilisateur(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'validation_compte.php';
            
            const input1 = document.createElement('input');
            input1.type = 'hidden';
            input1.name = 'user_id';
            input1.value = id;
            
            const input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = 'action';
            input2.value = 'refuser';
            
            form.appendChild(input1);
            form.appendChild(input2);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>
