<?php
session_start();
header('Content-Type: application/json');

// Vérification de la session et du rôle admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['error' => 'Accès non autorisé']);
    exit();
}

require_once 'connexion.php';

// Création du dossier uploads s'il n'existe pas
$uploadDir = "../uploads/salles";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Fonction pour redimensionner l'image
function resizeImage($sourcePath, $targetPath, $maxWidth = 800, $maxHeight = 600) {
    if (!extension_loaded('gd')) {
        return move_uploaded_file($sourcePath, $targetPath);
    }

    list($width, $height, $type) = getimagesize($sourcePath);
    
    if ($width > $maxWidth || $height > $maxHeight) {
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }

    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    if ($type === IMAGETYPE_PNG) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

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

    imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

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

    imagedestroy($source);
    imagedestroy($newImage);

    return $success;
}

// Traitement des requêtes GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'list') {
        try {
            $stmt = $connexion->query("SELECT * FROM salle ORDER BY nom");
            $salles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['salles' => $salles]);
            exit();
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Erreur lors de la récupération des salles']);
            exit();
        }
    }
}

// Traitement des requêtes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data) {
        $action = $data['action'] ?? '';
    }

    try {
        switch ($action) {
            case 'add':
                if (!isset($_POST['nom']) || !isset($_POST['capacite'])) {
                    throw new Exception('Données manquantes');
                }

                $nom = $_POST['nom'];
                $capacite = (int)$_POST['capacite'];
                $description = $_POST['description'] ?? '';
                $disponible = isset($_POST['disponible']) ? 1 : 0;
                $photo = '';

                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['photo']['tmp_name'];
                    $fileName = uniqid() . '_' . $_FILES['photo']['name'];
                    $uploadFile = $uploadDir . '/' . $fileName;

                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $fileType = mime_content_type($tmpName);
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        throw new Exception("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
                    }

                    if (resizeImage($tmpName, $uploadFile)) {
                        $photo = 'uploads/salles/' . $fileName;
                    }
                }

                $stmt = $connexion->prepare("INSERT INTO salle (nom, capacite, description, disponible, photo) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $capacite, $description, $disponible, $photo]);
                echo json_encode(['success' => 'Salle ajoutée avec succès']);
                break;

            case 'update':
                if (!isset($_POST['salle_id']) || !isset($_POST['nom']) || !isset($_POST['capacite'])) {
                    throw new Exception('Données manquantes');
                }

                $id = (int)$_POST['salle_id'];
                $nom = $_POST['nom'];
                $capacite = (int)$_POST['capacite'];
                $description = $_POST['description'] ?? '';
                $disponible = isset($_POST['disponible']) ? 1 : 0;

                $stmt = $connexion->prepare("SELECT photo FROM salle WHERE id = ?");
                $stmt->execute([$id]);
                $oldPhoto = $stmt->fetchColumn();

                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['photo']['tmp_name'];
                    $fileName = uniqid() . '_' . $_FILES['photo']['name'];
                    $uploadFile = $uploadDir . '/' . $fileName;

                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $fileType = mime_content_type($tmpName);
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        throw new Exception("Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.");
                    }

                    if (resizeImage($tmpName, $uploadFile)) {
                        if ($oldPhoto && file_exists("../" . $oldPhoto)) {
                            unlink("../" . $oldPhoto);
                        }
                        $photo = 'uploads/salles/' . $fileName;
                        $stmt = $connexion->prepare("UPDATE salle SET nom = ?, capacite = ?, description = ?, disponible = ?, photo = ? WHERE id = ?");
                        $stmt->execute([$nom, $capacite, $description, $disponible, $photo, $id]);
                    }
                } else {
                    $stmt = $connexion->prepare("UPDATE salle SET nom = ?, capacite = ?, description = ?, disponible = ? WHERE id = ?");
                    $stmt->execute([$nom, $capacite, $description, $disponible, $id]);
                }

                echo json_encode(['success' => 'Salle modifiée avec succès']);
                break;

            case 'delete':
                if (!isset($data['salle_id'])) {
                    throw new Exception('ID de salle manquant');
                }

                $id = (int)$data['salle_id'];

                $stmt = $connexion->prepare("SELECT photo FROM salle WHERE id = ?");
                $stmt->execute([$id]);
                $photo = $stmt->fetchColumn();

                if ($photo && file_exists("../" . $photo)) {
                    unlink("../" . $photo);
                }

                $stmt = $connexion->prepare("DELETE FROM salle WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => 'Salle supprimée avec succès']);
                break;

            default:
                throw new Exception('Action non valide');
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

echo json_encode(['error' => 'Méthode non autorisée']);
?>