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
    $pdo->exec("CREATE TABLE IF NOT EXISTS materiel (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        type VARCHAR(50) NOT NULL,
        description TEXT,
        numero_serie VARCHAR(100),
        etat VARCHAR(50) DEFAULT 'bon',
        disponible TINYINT(1) DEFAULT 1,
        photo VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS reservation_materiel (
        id INT AUTO_INCREMENT PRIMARY KEY,
        materiel_id INT NOT NULL,
        user_id INT NOT NULL,
        date_debut DATETIME NOT NULL,
        date_fin DATETIME NOT NULL,
        statut ENUM('en_attente', 'validee', 'refusee', 'annulee') DEFAULT 'en_attente',
        commentaire TEXT,
        signature_admin VARCHAR(255),
        date_signature DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (materiel_id) REFERENCES materiel(id) ON DELETE CASCADE
    )");

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Création du dossier uploads s'il n'existe pas
$uploadDir = "../uploads/materiel";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Messages de retour pour l'utilisateur
$message = '';
$messageType = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout de matériel
    if (isset($_POST['ajouter'])) {
        try {
            $nom = $_POST['nom'];
            $type = $_POST['type'];
            $description = $_POST['description'] ?? '';
            $numero_serie = $_POST['numero_serie'] ?? '';
            $etat = $_POST['etat'] ?? 'bon';
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
                    if (move_uploaded_file($tmpName, $uploadFile)) {
                        $photo = 'uploads/materiel/' . $fileName;
                    }
                } else {
                    throw new Exception("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
                }
            }

            $sql = "INSERT INTO materiel (nom, type, description, numero_serie, etat, disponible, photo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom, $type, $description, $numero_serie, $etat, $disponible, $photo]);

            $message = "Le matériel a été ajouté avec succès.";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Modification de matériel
    if (isset($_POST['modifier'])) {
        try {
            $id = (int)$_POST['materiel_id'];
            $nom = $_POST['nom'];
            $type = $_POST['type'];
            $description = $_POST['description'] ?? '';
            $numero_serie = $_POST['numero_serie'] ?? '';
            $etat = $_POST['etat'];
            $disponible = isset($_POST['disponible']) ? 1 : 0;

            // Récupérer l'ancienne photo
            $stmt = $pdo->prepare("SELECT photo FROM materiel WHERE id = ?");
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
                    if (move_uploaded_file($tmpName, $uploadFile)) {
                        // Supprimer l'ancienne photo si elle existe
                        if ($oldPhoto && file_exists("../" . $oldPhoto)) {
                            unlink("../" . $oldPhoto);
                        }
                        $photo = 'uploads/materiel/' . $fileName;
                        
                        $sql = "UPDATE materiel SET 
                                nom = ?, 
                                type = ?, 
                                description = ?, 
                                numero_serie = ?,
                                etat = ?,
                                disponible = ?,
                                photo = ?
                                WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$nom, $type, $description, $numero_serie, $etat, $disponible, $photo, $id]);
                    }
                } else {
                    throw new Exception("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
                }
            } else {
                $sql = "UPDATE materiel SET 
                        nom = ?, 
                        type = ?, 
                        description = ?, 
                        numero_serie = ?,
                        etat = ?,
                        disponible = ?
                        WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nom, $type, $description, $numero_serie, $etat, $disponible, $id]);
            }

            $message = "Le matériel a été modifié avec succès.";
            $messageType = "success";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Suppression de matériel
    if (isset($_POST['supprimer'])) {
        try {
            $id = (int)$_POST['materiel_id'];
            
            // Récupérer la photo avant la suppression
            $stmt = $pdo->prepare("SELECT photo FROM materiel WHERE id = ?");
            $stmt->execute([$id]);
            $photo = $stmt->fetchColumn();

            // Supprimer le matériel
            $sql = "DELETE FROM materiel WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            // Supprimer la photo si elle existe
            if ($photo && file_exists("../" . $photo)) {
                unlink("../" . $photo);
            }

            $message = "Le matériel a été supprimé avec succès.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Erreur lors de la suppression : " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Validation d'une réservation avec signature
    if (isset($_POST['valider'])) {
        try {
            $reservation_id = (int)$_POST['reservation_id'];
            $commentaire = $_POST['commentaire'] ?? '';
            $signature = $_POST['signature'] ?? '';
            
            if (empty($signature)) {
                throw new Exception("La signature est requise pour valider la réservation.");
            }

            // Vérifier si le matériel est déjà réservé pour cette période
            $sql = "SELECT r.*, m.nom as materiel_nom 
                   FROM reservation_materiel r 
                   JOIN materiel m ON r.materiel_id = m.id 
                   WHERE r.id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$reservation_id]);
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$reservation) {
                throw new Exception("Réservation non trouvée.");
            }

            // Vérifier les conflits de réservation
            $sql = "SELECT COUNT(*) FROM reservation_materiel 
                   WHERE materiel_id = ? 
                   AND id != ?
                   AND statut = 'validee'
                   AND (
                       (date_debut BETWEEN ? AND ?) 
                       OR (date_fin BETWEEN ? AND ?)
                       OR (date_debut <= ? AND date_fin >= ?)
                   )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $reservation['materiel_id'],
                $reservation_id,
                $reservation['date_debut'],
                $reservation['date_fin'],
                $reservation['date_debut'],
                $reservation['date_fin'],
                $reservation['date_debut'],
                $reservation['date_fin']
            ]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Le matériel est déjà réservé pour cette période.");
            }

            // Si pas de conflit, valider la réservation
            $sql = "UPDATE reservation_materiel SET 
                    statut = 'validee',
                    commentaire = ?,
                    signature_admin = ?,
                    date_signature = NOW()
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$commentaire, $signature, $reservation_id]);

            $message = "La réservation a été validée avec succès.";
            $messageType = "success";
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = "error";
        }
    }

    // Refus d'une réservation avec signature
    if (isset($_POST['refuser'])) {
        try {
            $reservation_id = (int)$_POST['reservation_id'];
            $commentaire = $_POST['commentaire'] ?? '';
            $signature = $_POST['signature'] ?? '';
            
            if (empty($signature)) {
                throw new Exception("La signature est requise pour refuser la réservation.");
            }

            if (empty($commentaire)) {
                throw new Exception("Un commentaire est requis pour expliquer le refus de la réservation.");
            }

            $sql = "UPDATE reservation_materiel SET 
                    statut = 'refusee',
                    commentaire = ?,
                    signature_admin = ?,
                    date_signature = NOW()
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$commentaire, $signature, $reservation_id]);

            $message = "La réservation a été refusée.";
            $messageType = "success";
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = "error";
        }
    }

    // Modification de la date/heure
    if (isset($_POST['modifier_date'])) {
        try {
            $reservation_id = (int)$_POST['reservation_id'];
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            
            $sql = "UPDATE reservation_materiel SET date_debut = ?, date_fin = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$date_debut, $date_fin, $reservation_id]);

            $message = "La date de réservation a été modifiée avec succès.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Erreur lors de la modification : " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Annulation d'une réservation
    if (isset($_POST['annuler'])) {
        try {
            $reservation_id = (int)$_POST['reservation_id'];
            
            $sql = "UPDATE reservation_materiel SET statut = 'annulee' WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$reservation_id]);

            $message = "La réservation a été annulée avec succès.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Erreur lors de l'annulation : " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Nouvelle réservation par l'admin
    if (isset($_POST['reserver'])) {
        try {
            $materiel_id = (int)$_POST['materiel_id'];
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            $user_id = $_SESSION['user_id']; // Assurez-vous d'avoir l'ID de l'admin en session
            
            $sql = "INSERT INTO reservation_materiel (materiel_id, user_id, date_debut, date_fin, statut) 
                    VALUES (?, ?, ?, ?, 'validee')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$materiel_id, $user_id, $date_debut, $date_fin]);

            $message = "La réservation a été créée avec succès.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Erreur lors de la réservation : " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Récupération du matériel et des réservations
try {
    $materiels = $pdo->query("SELECT * FROM materiel ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Requête pour les réservations en attente
    $sql = "SELECT r.id, r.materiel_id, r.user_id, r.date_debut, r.date_fin, 
                   r.statut, r.commentaire, r.signature_admin, r.date_signature, r.created_at,
                   m.nom as materiel_nom, m.type as materiel_type
            FROM reservation_materiel r 
            JOIN materiel m ON r.materiel_id = m.id
            WHERE r.statut = 'en_attente'
            ORDER BY r.created_at DESC";
    
    $reservations = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "Erreur lors de la récupération des données : " . $e->getMessage();
    $messageType = "error";
    $materiels = [];
    $reservations = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Matériel - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/gestion_materiel.css">
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
        <h1>Gestion du Matériel</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout/modification de matériel -->
        <div class="form-container">
            <h2>Ajouter du matériel</h2>
            <form method="POST" class="form-gestion" enctype="multipart/form-data">
                <input type="text" name="nom" placeholder="Nom du matériel" required>
                <input type="text" name="type" placeholder="Type de matériel (ex: PC, Casque VR, Caméra, etc.)" required>
                <input type="text" name="numero_serie" placeholder="Numéro de série">
                <select name="etat" required>
                    <option value="bon">Bon état</option>
                    <option value="moyen">État moyen</option>
                    <option value="mauvais">Mauvais état</option>
                </select>
                <textarea name="description" placeholder="Description"></textarea>
                
                <div class="form-group">
                    <label>Photo du matériel</label>
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

        <!-- Liste des réservations -->
        <div class="table-container reservations-list">
            <h2>Réservations en attente</h2>
            <div class="table-wrapper">
                <table class="gestion-table compact-table">
                    <thead>
                        <tr>
                            <th>Matériel</th>
                            <th>Type</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservations)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Aucune réservation en attente</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reservation['materiel_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['materiel_type']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <input type="text" name="signature" placeholder="Signature" required>
                                            <input type="text" name="commentaire" placeholder="Commentaire">
                                            <button type="submit" name="valider">Valider</button>
                                            <button type="submit" name="refuser">Refuser</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Liste du matériel -->
        <div class="table-container">
            <h2>Matériel disponible</h2>
            <table class="gestion-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>N° Série</th>
                        <th>État</th>
                        <th>Disponible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materiels as $materiel): ?>
                    <tr>
                        <td>
                            <?php if ($materiel['photo']): ?>
                                <img src="../<?php echo htmlspecialchars($materiel['photo']); ?>" alt="Photo du matériel" class="materiel-photo">
                            <?php else: ?>
                                <div class="no-photo">Pas de photo</div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($materiel['nom']); ?></td>
                        <td><?php echo htmlspecialchars($materiel['type']); ?></td>
                        <td><?php echo htmlspecialchars($materiel['numero_serie']); ?></td>
                        <td class="etat-<?php echo strtolower($materiel['etat']); ?>"><?php echo htmlspecialchars($materiel['etat']); ?></td>
                        <td><?php echo $materiel['disponible'] ? 'Oui' : 'Non'; ?></td>
                        <td class="actions">
                            <button type="button" class="btn btn-modifier" onclick="modifierMateriel(<?php echo htmlspecialchars(json_encode($materiel)); ?>)">Modifier</button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce matériel ?');">
                                <input type="hidden" name="materiel_id" value="<?php echo $materiel['id']; ?>">
                                <button type="submit" name="supprimer" class="btn btn-supprimer">Supprimer</button>
                            </form>
                            <button type="button" class="btn btn-reserver" onclick="nouvelleReservation(<?php echo $materiel['id']; ?>)">Réserver</button>
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

    <!-- Modal pour modification des dates -->
    <div id="modal-dates" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier les dates</h2>
            <form method="POST" id="form-modifier-dates">
                <input type="hidden" name="reservation_id" id="modal-reservation-id">
                <div class="form-group">
                    <label>Date de début</label>
                    <input type="datetime-local" name="date_debut" required>
                </div>
                <div class="form-group">
                    <label>Date de fin</label>
                    <input type="datetime-local" name="date_fin" required>
                </div>
                <button type="submit" name="modifier_date" class="btn btn-modifier">Modifier</button>
            </form>
        </div>
    </div>

    <!-- Modal pour modification du matériel -->
    <div id="modal-materiel" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier le matériel</h2>
            <form method="POST" id="form-modifier-materiel" enctype="multipart/form-data">
                <input type="hidden" name="materiel_id" id="modal-materiel-id">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" id="modal-materiel-nom" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <input type="text" name="type" id="modal-materiel-type" required>
                </div>
                <div class="form-group">
                    <label>Numéro de série</label>
                    <input type="text" name="numero_serie" id="modal-materiel-serie">
                </div>
                <div class="form-group">
                    <label>État</label>
                    <select name="etat" id="modal-materiel-etat" required>
                        <option value="bon">Bon état</option>
                        <option value="moyen">État moyen</option>
                        <option value="mauvais">Mauvais état</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="modal-materiel-description"></textarea>
                </div>
                <div class="form-group">
                    <label>Photo du matériel</label>
                    <input type="file" name="photo" accept="image/*" class="file-input">
                    <div id="modal-materiel-photo-preview" class="photo-preview"></div>
                </div>
                <div class="checkbox-line">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="disponible" id="modal-materiel-disponible">
                        <span class="checkmark"></span>
                    </label>
                    <span>Disponible</span>
                </div>
                <button type="submit" name="modifier" class="btn btn-modifier">Modifier</button>
            </form>
        </div>
    </div>

    <!-- Modal pour nouvelle réservation -->
    <div id="modal-reservation" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Nouvelle réservation</h2>
            <form method="POST" id="form-nouvelle-reservation">
                <input type="hidden" name="materiel_id" id="modal-reservation-materiel-id">
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

    <script>
    // Fonctions pour gérer les modals
    function modifierDates(reservationId) {
        document.getElementById('modal-reservation-id').value = reservationId;
        document.getElementById('modal-dates').style.display = 'block';
    }

    function modifierMateriel(materiel) {
        document.getElementById('modal-materiel-id').value = materiel.id;
        document.getElementById('modal-materiel-nom').value = materiel.nom;
        document.getElementById('modal-materiel-type').value = materiel.type;
        document.getElementById('modal-materiel-serie').value = materiel.numero_serie;
        document.getElementById('modal-materiel-etat').value = materiel.etat;
        document.getElementById('modal-materiel-description').value = materiel.description;
        document.getElementById('modal-materiel-disponible').checked = materiel.disponible == 1;
        
        // Afficher la photo existante
        const photoPreview = document.getElementById('modal-materiel-photo-preview');
        if (materiel.photo) {
            photoPreview.innerHTML = `<img src="../${materiel.photo}" alt="Photo actuelle" class="preview-image">`;
        } else {
            photoPreview.innerHTML = '<div class="no-photo">Pas de photo</div>';
        }
        
        document.getElementById('modal-materiel').style.display = 'block';
    }

    function nouvelleReservation(materielId) {
        document.getElementById('modal-reservation-materiel-id').value = materielId;
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
