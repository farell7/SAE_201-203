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
$uploadDir = "../uploads/materiel";
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mise à jour de la structure de la table reservation_salle si elle existe
    try {
        $pdo->exec("ALTER TABLE reservation_salle 
                    ADD COLUMN IF NOT EXISTS commentaire TEXT,
                    ADD COLUMN IF NOT EXISTS signature_admin VARCHAR(255),
                    ADD COLUMN IF NOT EXISTS date_signature DATETIME");
    } catch (PDOException $e) {
        // Si l'erreur n'est pas liée à l'existence des colonnes, on la propage
        if ($e->getCode() !== '42S21') { // 42S21 = Column already exists
            throw $e;
        }
    }

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
        FOREIGN KEY (salle_id) REFERENCES salle(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES utilisateur(id) ON DELETE CASCADE
    )");
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

function uploadImage($input, $dir) {
    if (!empty($_FILES[$input]['name'])) {
        $ext = pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION);
        $file = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES[$input]['tmp_name'], "$dir/$file");
        return 'uploads/materiel/' . $file;
    }
    return '';
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['ajouter'])) {
            // Gestion de l'upload de la photo
            $photo = '';
            if (!empty($_FILES['photo']['name'])) {
                $uploadDir = "../uploads/salles";
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
                $photo = uploadImage('photo', $uploadDir);
            }
            
            // Ajout de la salle
            $stmt = $pdo->prepare("INSERT INTO salle (nom, capacite, description, disponible, photo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nom'],
                $_POST['capacite'],
                $_POST['description'] ?? '',
                isset($_POST['disponible']) ? 1 : 0,
                $photo
            ]);
            $message = "La salle a été ajoutée avec succès.";
            $messageType = "success";
        }
        if (isset($_POST['modifier'])) {
            $id = (int)$_POST['materiel_id'];
            $photo = '';
            if (!empty($_FILES['photo']['name'])) {
                $stmt = $pdo->prepare("SELECT photo FROM materiel WHERE id = ?");
                $stmt->execute([$id]);
                $oldPhoto = $stmt->fetchColumn();
                $photo = uploadImage('photo', $uploadDir);
                if ($oldPhoto && file_exists("../" . $oldPhoto)) unlink("../" . $oldPhoto);
                $sql = "UPDATE materiel SET nom=?, description=?, numero_serie=?, etat=?, disponible=?, photo=? WHERE id=?";
                $pdo->prepare($sql)->execute([
                    $_POST['nom'], $_POST['description'] ?? '', $_POST['numero_serie'] ?? '',
                    $_POST['etat'], isset($_POST['disponible']) ? 1 : 0, $photo, $id
                ]);
            } else {
                $sql = "UPDATE materiel SET nom=?, description=?, numero_serie=?, etat=?, disponible=? WHERE id=?";
                $pdo->prepare($sql)->execute([
                    $_POST['nom'], $_POST['description'] ?? '', $_POST['numero_serie'] ?? '',
                    $_POST['etat'], isset($_POST['disponible']) ? 1 : 0, $id
                ]);
            }
            $message = "Le matériel a été modifié avec succès.";
            $messageType = "success";
        }
        if (isset($_POST['supprimer'])) {
            $id = (int)$_POST['salle_id'];
            
            // Commencer une transaction
            $pdo->beginTransaction();
            
            try {
                // Récupérer la photo avant de supprimer
                $stmt = $pdo->prepare("SELECT photo FROM salle WHERE id = ?");
                $stmt->execute([$id]);
                $photo = $stmt->fetchColumn();
                
                // Supprimer d'abord toutes les réservations associées
                $pdo->prepare("DELETE FROM reservation_salle WHERE salle_id = ?")->execute([$id]);
                
                // Puis supprimer la salle
                $pdo->prepare("DELETE FROM salle WHERE id = ?")->execute([$id]);
                
                // Supprimer la photo si elle existe
                if ($photo && file_exists("../" . $photo)) {
                    unlink("../" . $photo);
                }
                
                // Valider la transaction
                $pdo->commit();
                
                $message = "La salle et ses réservations ont été supprimées avec succès.";
                $messageType = "success";
            } catch (Exception $e) {
                // En cas d'erreur, annuler la transaction
                $pdo->rollBack();
                throw $e;
            }
        }
        if (isset($_POST['valider']) || isset($_POST['refuser'])) {
            $reservation_id = (int)$_POST['reservation_id'];
            $commentaire = $_POST['commentaire'] ?? '';
            $signature = $_POST['signature'] ?? '';
            if (empty($signature)) throw new Exception("La signature est requise.");
            $statut = isset($_POST['valider']) ? 'validee' : 'refusee';
            if ($statut === 'refusee' && empty($commentaire)) throw new Exception("Un commentaire est requis pour refuser.");
            $sql = "UPDATE reservation_salle SET statut=?, commentaire=?, signature_admin=?, date_signature=NOW() WHERE id=?";
            $pdo->prepare($sql)->execute([$statut, $commentaire, $signature, $reservation_id]);
            $message = $statut === 'validee' ? "La réservation a été validée." : "La réservation a été refusée.";
            $messageType = "success";
        }
        if (isset($_POST['modifier_date'])) {
            $pdo->prepare("UPDATE reservation_salle SET date_debut=?, date_fin=? WHERE id=?")
                ->execute([$_POST['date_debut'], $_POST['date_fin'], (int)$_POST['reservation_id']]);
            $message = "Date modifiée.";
            $messageType = "success";
        }
        if (isset($_POST['annuler'])) {
            $pdo->prepare("UPDATE reservation_salle SET statut='annulee' WHERE id=?")
                ->execute([(int)$_POST['reservation_id']]);
            $message = "Réservation annulée.";
            $messageType = "success";
        }
        if (isset($_POST['reserver'])) {
            $pdo->prepare("INSERT INTO reservation_salle (salle_id, user_id, date_debut, date_fin, statut) VALUES (?, ?, ?, ?, 'validee')")
                ->execute([(int)$_POST['salle_id'], $_SESSION['user_id'], $_POST['date_debut'], $_POST['date_fin']]);
            $message = "Réservation créée.";
            $messageType = "success";
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = "error";
    }
}

$materiels = $pdo->query("SELECT * FROM materiel ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$sql = "SELECT rs.*, s.nom as salle_nom 
        FROM reservation_salle rs 
        JOIN salle s ON rs.salle_id = s.id 
        WHERE rs.statut = 'en_attente' 
        ORDER BY rs.date_debut DESC";
$reservations = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Récupération de la liste des salles
$salles = $pdo->query("SELECT * FROM salle ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
?>