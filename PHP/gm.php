<?php
// Suppression de l'auto-inclusion qui crée une boucle
// include 'gm.php';

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialisation des variables globales
$message = '';
$messageType = '';
$materiels = [];
$reservations = [];

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

    // Vérifier si la colonne numero_serie existe, sinon l'ajouter
    $columns = $pdo->query("SHOW COLUMNS FROM materiel LIKE 'numero_serie'")->fetchAll();
    if (empty($columns)) {
        $pdo->exec("ALTER TABLE materiel ADD COLUMN numero_serie VARCHAR(100) AFTER description");
    }

    // Vérifier si la colonne etat existe, sinon l'ajouter
    $columns = $pdo->query("SHOW COLUMNS FROM materiel LIKE 'etat'")->fetchAll();
    if (empty($columns)) {
        $pdo->exec("ALTER TABLE materiel ADD COLUMN etat VARCHAR(50) DEFAULT 'bon' AFTER numero_serie");
    }

    // Vérifier si la colonne photo existe, sinon l'ajouter
    $columns = $pdo->query("SHOW COLUMNS FROM materiel LIKE 'photo'")->fetchAll();
    if (empty($columns)) {
        $pdo->exec("ALTER TABLE materiel ADD COLUMN photo VARCHAR(255) AFTER etat");
    }

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

    // Récupération du matériel et des réservations
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
    $message = "Erreur de connexion : " . $e->getMessage();
    $messageType = "error";
    $materiels = [];
    $reservations = [];
}

// Création du dossier uploads s'il n'existe pas
$uploadDir = "../uploads/materiel";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Fonction pour uploader une image
function uploadImage($inputName, $uploadDir) {
    if (!empty($_FILES[$inputName]['name'])) {
        $fileName = uniqid() . '_' . basename($_FILES[$inputName]['name']);
        
        // Création du chemin absolu pour le dossier d'upload
        $absoluteUploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'materiel';
        
        // Création du dossier s'il n'existe pas
        if (!file_exists($absoluteUploadDir)) {
            mkdir($absoluteUploadDir, 0777, true);
        }
        
        // Chemin complet du fichier
        $targetPath = $absoluteUploadDir . DIRECTORY_SEPARATOR . $fileName;
        
        // Chemin relatif pour la base de données
        $dbPath = 'uploads/materiel/' . $fileName;
        
        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetPath)) {
            return $dbPath;
        }
    }
    return '';
}

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
            $photo = uploadImage('photo', $uploadDir);

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
            $photo = uploadImage('photo', $uploadDir);

            // Récupérer l'ancienne photo
            $stmt = $pdo->prepare("SELECT photo FROM materiel WHERE id = ?");
            $stmt->execute([$id]);
            $oldPhoto = $stmt->fetchColumn();

            if ($oldPhoto && file_exists("../" . $oldPhoto)) {
                unlink("../" . $oldPhoto);
            }

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
?>