<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$dbname = 'resauge';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Messages de retour pour l'utilisateur
$message = '';
$messageType = '';

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserver'])) {
    try {
        $materiel_id = (int)$_POST['materiel_id'];
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Temporaire : utiliser 1 si non connecté

        // Vérifier si le matériel est disponible pour cette période
        $sql = "SELECT COUNT(*) FROM reservation_materiel 
                WHERE materiel_id = ? 
                AND ((date_debut BETWEEN ? AND ?) 
                OR (date_fin BETWEEN ? AND ?))
                AND statut = 'validee'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$materiel_id, $date_debut, $date_fin, $date_debut, $date_fin]);
        $reservationExistante = $stmt->fetchColumn() > 0;

        if ($reservationExistante) {
            throw new Exception("Ce matériel n'est pas disponible pour cette période.");
        }

        // Créer la réservation avec statut 'en_attente'
        $sql = "INSERT INTO reservation_materiel (materiel_id, user_id, date_debut, date_fin, statut) 
                VALUES (?, ?, ?, ?, 'en_attente')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$materiel_id, $user_id, $date_debut, $date_fin]);

        $message = "Votre demande de réservation a été enregistrée avec succès et est en attente de validation par un administrateur.";
        $messageType = "success";
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
        $messageType = "error";
    }
}

// Récupération de tous les matériels disponibles
try {
    $materiels = $pdo->query("
        SELECT * FROM materiel 
        WHERE disponible = 1 
        ORDER BY type, nom"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Erreur lors de la récupération des matériels : " . $e->getMessage();
    $messageType = "error";
    $materiels = [];
}

// Récupération des réservations de l'utilisateur
try {
    $stmt = $pdo->prepare("
        SELECT r.*, m.nom as materiel_nom, m.type as materiel_type, m.photo
        FROM reservation_materiel r 
        JOIN materiel m ON r.materiel_id = m.id 
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([1]); // Utiliser l'ID de l'utilisateur connecté
    $mes_reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Erreur lors de la récupération des réservations : " . $e->getMessage();
    $messageType = "error";
    $mes_reservations = [];
}
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
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/gestion_materiel.css">
    <link rel="stylesheet" href="../CSS/reservation_mat.css">
    <link rel="stylesheet" href="../CSS/reservation_salle.css">
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="../index.php">Tableau de bord</a>
            <a href="gestion_materiel.php">Gestions</a>
            <a href="suivi_reservations.php">Suivi</a>
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

        <!-- Filtres -->
        <div class="filter-section">
            <form class="filter-form" method="GET">
                <select name="type" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="PC" <?php echo isset($_GET['type']) && $_GET['type'] === 'PC' ? 'selected' : ''; ?>>PC</option>
                    <option value="VR" <?php echo isset($_GET['type']) && $_GET['type'] === 'VR' ? 'selected' : ''; ?>>Casque VR</option>
                    <option value="Camera" <?php echo isset($_GET['type']) && $_GET['type'] === 'Camera' ? 'selected' : ''; ?>>Caméra 360</option>
                    <option value="Autre" <?php echo isset($_GET['type']) && $_GET['type'] === 'Autre' ? 'selected' : ''; ?>>Autre</option>
                </select>
            </form>
        </div>

        <!-- Grille de matériel -->
        <div class="materiel-grid">
            <?php foreach ($materiels as $materiel): 
                if (isset($_GET['type']) && !empty($_GET['type']) && $materiel['type'] !== $_GET['type']) continue;
            ?>
                <div class="materiel-card">
                    <?php if ($materiel['photo']): ?>
                        <img src="../<?php echo htmlspecialchars($materiel['photo']); ?>" alt="<?php echo htmlspecialchars($materiel['nom']); ?>" class="materiel-photo">
                    <?php else: ?>
                        <div class="no-photo">Pas de photo</div>
                    <?php endif; ?>
                    <div class="materiel-info">
                        <div class="materiel-type"><?php echo htmlspecialchars($materiel['type']); ?></div>
                        <h3 class="materiel-nom"><?php echo htmlspecialchars($materiel['nom']); ?></h3>
                        <?php if (!empty($materiel['description'])): ?>
                            <p class="materiel-description"><?php echo htmlspecialchars($materiel['description']); ?></p>
                        <?php endif; ?>
                        <button type="button" class="btn btn-reserver" onclick="nouvelleReservation(<?php echo $materiel['id']; ?>)">
                            Réserver
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Mes réservations -->
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
                    <?php foreach ($mes_reservations as $reservation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reservation['materiel_nom']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['materiel_type']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                        <td class="status-<?php echo $reservation['statut']; ?>">
                            <?php echo htmlspecialchars($reservation['statut']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal pour nouvelle réservation -->
    <div id="modal-reservation" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Nouvelle réservation</h2>
            <form method="POST" id="form-nouvelle-reservation">
                <input type="hidden" name="materiel_id" id="modal-materiel-id">
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
    </div>

    <footer class="footer">
        &copy;2025 Université Eiffel. Tous droits réservés.
    </footer>

    <script>
    function nouvelleReservation(materielId) {
        document.getElementById('modal-materiel-id').value = materielId;
        document.getElementById('modal-reservation').style.display = 'block';
    }

    // Fermeture des modals
    document.querySelectorAll('.close').forEach(function(close) {
        close.onclick = function() {
            this.closest('.modal').style.display = 'none';
        }
    });

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
    </script>
</body>
</html>
