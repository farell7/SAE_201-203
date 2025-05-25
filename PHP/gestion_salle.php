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

    // Création des tables si elles n'existent pas
    $pdo->exec("CREATE TABLE IF NOT EXISTS salle (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        capacite INT NOT NULL,
        description TEXT,
        photo VARCHAR(255),
        disponible TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS reservation_salle (
        id INT AUTO_INCREMENT PRIMARY KEY,
        salle_id INT NOT NULL,
        user_id INT NOT NULL,
        date_debut DATETIME NOT NULL,
        date_fin DATETIME NOT NULL,
        statut ENUM('en_attente', 'validee', 'refusee', 'annulee') DEFAULT 'en_attente',
        commentaire TEXT,
        signature_admin VARCHAR(255),
        date_signature DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (salle_id) REFERENCES salle(id) ON DELETE CASCADE
    )");

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Création du dossier uploads s'il n'existe pas
$uploadDir = "../uploads/salles";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Messages de retour pour l'utilisateur
$message = '';
$messageType = '';

// Fonction pour redimensionner l'image
function resizeImage($sourcePath, $targetPath, $maxWidth = 800, $maxHeight = 600) {
    // Vérifier si GD est installé
    if (!extension_loaded('gd')) {
        // Si GD n'est pas installé, simplement copier l'image
        return move_uploaded_file($sourcePath, $targetPath);
    }

    list($width, $height, $type) = getimagesize($sourcePath);
    
    // Calculer les nouvelles dimensions en gardant le ratio
    if ($width > $maxWidth || $height > $maxHeight) {
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }

    // Créer une nouvelle image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Gérer la transparence pour les PNG
    if ($type === IMAGETYPE_PNG) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Charger l'image source selon son type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourcePath);
            break;
        default:
            return move_uploaded_file($sourcePath, $targetPath);
    }

    // Redimensionner
    imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Sauvegarder selon le type
    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($newImage, $targetPath, 85);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($newImage, $targetPath, 8);
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($newImage, $targetPath);
            break;
    }

    // Libérer la mémoire
    imagedestroy($source);
    imagedestroy($newImage);

    return $success;
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout d'une salle
    if (isset($_POST['ajouter'])) {
        try {
            $nom = $_POST['nom'];
            $capacite = (int)$_POST['capacite'];
            $description = $_POST['description'] ?? '';
            $disponible = isset($_POST['disponible']) ? 1 : 0;
            $photo = '';

            // Traitement de la photo
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['photo']['tmp_name'];
                $fileName = uniqid() . '_' . $_FILES['photo']['name'];
                $uploadFile = $uploadDir . '/' . $fileName;

                // Vérifier le type de fichier
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = mime_content_type($tmpName);
                
                if (in_array($fileType, $allowedTypes)) {
                    // Redimensionner et sauvegarder l'image
                    if (resizeImage($tmpName, $uploadFile)) {
                        $photo = 'uploads/salles/' . $fileName;
                    }
                } else {
                    throw new Exception("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
                }
            }

            $sql = "INSERT INTO salle (nom, capacite, description, disponible, photo) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom, $capacite, $description, $disponible, $photo]);

            $message = "La salle a été ajoutée avec succès.";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Modification d'une salle
    if (isset($_POST['modifier'])) {
        try {
            $id = (int)$_POST['salle_id'];
            $nom = $_POST['nom'];
            $capacite = (int)$_POST['capacite'];
            $description = $_POST['description'] ?? '';
            $disponible = isset($_POST['disponible']) ? 1 : 0;

            // Récupérer l'ancienne photo
            $stmt = $pdo->prepare("SELECT photo FROM salle WHERE id = ?");
            $stmt->execute([$id]);
            $oldPhoto = $stmt->fetchColumn();

            // Traitement de la nouvelle photo
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['photo']['tmp_name'];
                $fileName = uniqid() . '_' . $_FILES['photo']['name'];
                $uploadFile = $uploadDir . '/' . $fileName;

                // Vérifier le type de fichier
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = mime_content_type($tmpName);
                
                if (in_array($fileType, $allowedTypes)) {
                    // Redimensionner et sauvegarder l'image
                    if (resizeImage($tmpName, $uploadFile)) {
                        // Supprimer l'ancienne photo si elle existe
                        if ($oldPhoto && file_exists("../" . $oldPhoto)) {
                            unlink("../" . $oldPhoto);
                        }
                        $photo = 'uploads/salles/' . $fileName;
                        
                        $sql = "UPDATE salle SET 
                                nom = ?, 
                                capacite = ?, 
                                description = ?, 
                                disponible = ?,
                                photo = ?
                                WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$nom, $capacite, $description, $disponible, $photo, $id]);
                    }
                } else {
                    throw new Exception("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
                }
            } else {
                $sql = "UPDATE salle SET 
                        nom = ?, 
                        capacite = ?, 
                        description = ?, 
                        disponible = ?
                        WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nom, $capacite, $description, $disponible, $id]);
            }

            $message = "La salle a été modifiée avec succès.";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Suppression d'une salle
    if (isset($_POST['supprimer'])) {
        try {
            $id = (int)$_POST['salle_id'];
            $sql = "DELETE FROM salle WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            $message = "La salle a été supprimée avec succès.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Erreur lors de la suppression : " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Validation d'une réservation
    if (isset($_POST['valider'])) {
        try {
            $reservation_id = (int)$_POST['reservation_id'];
            $commentaire = $_POST['commentaire'] ?? '';
            $signature = $_SESSION['user_id'] ?? 'Admin'; // Utiliser l'ID de l'utilisateur connecté
            
            $sql = "UPDATE reservation_salle SET 
                    statut = 'validee',
                    commentaire = ?,
                    signature_admin = ?,
                    date_signature = NOW()
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$commentaire, $signature, $reservation_id]);

            $message = "La réservation a été validée avec succès.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Erreur lors de la validation : " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Refus d'une réservation
    if (isset($_POST['refuser'])) {
        try {
            $reservation_id = (int)$_POST['reservation_id'];
            $commentaire = $_POST['commentaire'] ?? '';
            
            if (empty($commentaire)) {
                throw new Exception("Un commentaire est requis pour refuser une réservation.");
            }

            $sql = "UPDATE reservation_salle SET 
                    statut = 'refusee',
                    commentaire = ?,
                    date_signature = NOW()
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$commentaire, $reservation_id]);

            $message = "La réservation a été refusée.";
            $messageType = "success";
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = "error";
        }
    }
}

// Récupération des salles et des réservations
try {
    $salles = $pdo->query("SELECT * FROM salle ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
    
    // Simplifions la requête pour éviter les erreurs de jointure
    $sql = "SELECT rs.*, s.nom as salle_nom, s.capacite
            FROM reservation_salle rs 
            JOIN salle s ON rs.salle_id = s.id 
            WHERE rs.statut = 'en_attente'
            ORDER BY rs.date_debut";
    $reservations = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Erreur lors de la récupération des données : " . $e->getMessage();
    $messageType = "error";
    $salles = [];
    $reservations = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Salles - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/gestion_salle.css">
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="../index.php">Tableau de bord</a>
            <a href="gestion_materiel.php">Materiel</a>
            <a href="gestion_salle.php">Salle</a>
            <a href="suivi_reservations.php">Suivi</a>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <div class="menu-icon">☰</div>
        </div>
    </nav>

    <main class="main-content">
        <h1>Gestion des Salles</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout de salle -->
        <div class="form-container">
            <h2>Ajouter une salle</h2>
            <form method="POST" class="form-gestion" enctype="multipart/form-data">
                <input type="text" name="nom" placeholder="Nom de la salle" required>
                <input type="number" name="capacite" placeholder="Capacité" min="1" required>
                <textarea name="description" placeholder="Description de la salle"></textarea>
                <div class="form-group">
                    <label>Photo de la salle</label>
                    <input type="file" name="photo" accept="image/*" class="file-input">
                </div>
                <div class="checkbox-line">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="disponible" checked>
                        <span class="checkmark"></span>
                    </label>
                    <span>Disponible</span>
                </div>
                <button type="submit" name="ajouter" class="btn btn-ajouter">Ajouter</button>
            </form>
        </div>

        <!-- Liste des réservations en attente -->
        <div class="table-container">
            <h2>Réservations en attente</h2>
            <table class="gestion-table">
                <thead>
                    <tr>
                        <th>Salle</th>
                        <th>Demandeur</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucune réservation en attente</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['salle_nom']); ?></td>
                                <td>Utilisateur <?php echo htmlspecialchars($reservation['user_id']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                <td class="actions">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <textarea name="commentaire" placeholder="Commentaire" class="commentaire-input"></textarea>
                                        <button type="submit" name="valider" class="btn btn-valider">Valider</button>
                                        <button type="submit" name="refuser" class="btn btn-refuser">Refuser</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Liste des salles -->
        <div class="table-container">
            <h2>Salles disponibles</h2>
            <table class="gestion-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Capacité</th>
                        <th>Description</th>
                        <th>Disponible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($salles)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Aucune salle enregistrée</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($salles as $salle): ?>
                            <tr>
                                <td>
                                    <?php if ($salle['photo']): ?>
                                        <img src="../<?php echo htmlspecialchars($salle['photo']); ?>" alt="Photo de la salle" class="salle-photo">
                                    <?php else: ?>
                                        <div class="no-photo">Pas de photo</div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($salle['nom']); ?></td>
                                <td><?php echo $salle['capacite']; ?> personnes</td>
                                <td><?php echo htmlspecialchars($salle['description'] ?: '-'); ?></td>
                                <td><?php echo $salle['disponible'] ? 'Oui' : 'Non'; ?></td>
                                <td class="actions">
                                    <button type="button" class="btn btn-modifier" onclick="modifierSalle(<?php echo htmlspecialchars(json_encode($salle)); ?>)">
                                        Modifier
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette salle ?');">
                                        <input type="hidden" name="salle_id" value="<?php echo $salle['id']; ?>">
                                        <button type="submit" name="supprimer" class="btn btn-supprimer">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal pour modification de salle -->
    <div id="modal-salle" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier la salle</h2>
            <form method="POST" id="form-modifier-salle" enctype="multipart/form-data">
                <input type="hidden" name="salle_id" id="modal-salle-id">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" id="modal-salle-nom" required>
                </div>
                <div class="form-group">
                    <label>Capacité</label>
                    <input type="number" name="capacite" id="modal-salle-capacite" min="1" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="modal-salle-description"></textarea>
                </div>
                <div class="form-group">
                    <label>Photo de la salle</label>
                    <input type="file" name="photo" accept="image/*" class="file-input">
                    <div id="modal-salle-photo-preview" class="photo-preview"></div>
                </div>
                <div class="checkbox-line">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="disponible" id="modal-salle-disponible">
                        <span class="checkmark"></span>
                    </label>
                    <span>Disponible</span>
                </div>
                <button type="submit" name="modifier" class="btn btn-modifier">Modifier</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        &copy;2025 Université Eiffel. Tous droits réservés.
    </footer>

    <script>
    function modifierSalle(salle) {
        document.getElementById('modal-salle-id').value = salle.id;
        document.getElementById('modal-salle-nom').value = salle.nom;
        document.getElementById('modal-salle-capacite').value = salle.capacite;
        document.getElementById('modal-salle-description').value = salle.description || '';
        document.getElementById('modal-salle-disponible').checked = salle.disponible == 1;
        
        // Afficher la photo existante
        const photoPreview = document.getElementById('modal-salle-photo-preview');
        if (salle.photo) {
            photoPreview.innerHTML = `<img src="../${salle.photo}" alt="Photo actuelle" class="preview-image">`;
        } else {
            photoPreview.innerHTML = '<div class="no-photo">Pas de photo</div>';
        }
        
        document.getElementById('modal-salle').style.display = 'block';
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