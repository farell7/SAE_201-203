<?php
session_start();
require_once 'connexion.php';

// Vérification de la session
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit();
}

// Messages de retour pour l'utilisateur
$message = '';
$messageType = '';

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reserver'])) {
        try {
            $materiel_id = (int)$_POST['materiel_id'];
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            $user_id = $_SESSION['utilisateur']['id'];

            // Créer la réservation
            $stmt = $connexion->prepare("
                INSERT INTO reservation_materiel (materiel_id, user_id, date_debut, date_fin, statut) 
                VALUES (?, ?, ?, ?, 'en_attente')
            ");
            $stmt->execute([$materiel_id, $user_id, $date_debut, $date_fin]);

            $message = "Votre demande de réservation a été enregistrée et est en attente de validation";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Récupérer la liste du matériel
$materiels = $connexion->query("SELECT * FROM materiel ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les réservations de l'utilisateur
$stmt = $connexion->prepare("
    SELECT r.*, m.nom as materiel_nom, m.type as materiel_type
    FROM reservation_materiel r 
    JOIN materiel m ON r.materiel_id = m.id 
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['utilisateur']['id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de Matériel - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/gestion_salle.css">
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="#">Accueil</a>
            <a href="#" class="active">Réservations</a>
            <a href="#">Mon Compte</a>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <div class="menu-icon">☰</div>
        </div>
    </nav>

    <main class="main-content">
        <h1>Réservation de Matériel</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire de réservation -->
        <div class="form-container">
            <h2>Nouvelle réservation</h2>
            <form method="POST" class="form-gestion">
                <select name="materiel_id" required>
                    <option value="">Sélectionnez un matériel</option>
                    <?php foreach ($materiels as $materiel): ?>
                        <option value="<?php echo $materiel['id']; ?>">
                            <?php echo htmlspecialchars($materiel['nom']); ?> 
                            (<?php echo htmlspecialchars($materiel['type']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="form-group">
                    <label>Date de début</label>
                    <input type="datetime-local" name="date_debut" required>
                </div>

                <div class="form-group">
                    <label>Date de fin</label>
                    <input type="datetime-local" name="date_fin" required>
                </div>

                <button type="submit" name="reserver" class="btn btn-reserver">Réserver</button>
            </form>
        </div>

        <!-- Liste des réservations -->
        <div class="table-container">
            <h2>Mes réservations</h2>
            <table class="gestion-table">
                <thead>
                    <tr>
                        <th>Matériel</th>
                        <th>Type</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reservation['materiel_nom']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['materiel_type']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                        <td>
                            <span class="badge <?php echo $reservation['statut'] === 'validee' ? 'bg-success' : 
                                ($reservation['statut'] === 'en_attente' ? 'bg-warning' : 'bg-danger'); ?>">
                                <?php echo htmlspecialchars($reservation['statut']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="footer">
        &copy;2025 Université Eiffel. Tous droits réservés.
    </footer>

    <script>
    // Définir la date minimale pour les champs datetime-local
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        
        document.querySelector('input[name="date_debut"]').min = minDateTime;
        document.querySelector('input[name="date_fin"]').min = minDateTime;
    });

    // Validation des dates
    document.querySelector('form').addEventListener('submit', function(e) {
        const dateDebut = new Date(document.querySelector('input[name="date_debut"]').value);
        const dateFin = new Date(document.querySelector('input[name="date_fin"]').value);
        
        if (dateFin <= dateDebut) {
            e.preventDefault();
            alert('La date de fin doit être postérieure à la date de début');
        }
    });
    </script>
</body>
</html> 