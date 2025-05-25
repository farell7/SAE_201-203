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
$uploadDir = "../uploads/materiel";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Traitement des requêtes GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'list') {
        try {
            $stmt = $connexion->query("SELECT * FROM materiel ORDER BY nom");
            $materiel = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['materiel' => $materiel]);
            exit();
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Erreur lors de la récupération du matériel']);
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
                if (!isset($_POST['nom']) || !isset($_POST['type'])) {
                    throw new Exception('Données manquantes');
                }

                $nom = $_POST['nom'];
                $type = $_POST['type'];
                $description = $_POST['description'] ?? '';
                $numero_serie = $_POST['numero_serie'] ?? '';
                $etat = $_POST['etat'] ?? 'bon';
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

                    if (move_uploaded_file($tmpName, $uploadFile)) {
                        $photo = 'uploads/materiel/' . $fileName;
                    }
                }

                $stmt = $connexion->prepare("INSERT INTO materiel (nom, type, description, numero_serie, etat, disponible, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $type, $description, $numero_serie, $etat, $disponible, $photo]);
                echo json_encode(['success' => 'Matériel ajouté avec succès']);
                break;

            case 'update':
                if (!isset($_POST['materiel_id']) || !isset($_POST['nom']) || !isset($_POST['type'])) {
                    throw new Exception('Données manquantes');
                }

                $id = (int)$_POST['materiel_id'];
                $nom = $_POST['nom'];
                $type = $_POST['type'];
                $description = $_POST['description'] ?? '';
                $numero_serie = $_POST['numero_serie'] ?? '';
                $etat = $_POST['etat'];
                $disponible = isset($_POST['disponible']) ? 1 : 0;

                $stmt = $connexion->prepare("SELECT photo FROM materiel WHERE id = ?");
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

                    if (move_uploaded_file($tmpName, $uploadFile)) {
                        if ($oldPhoto && file_exists("../" . $oldPhoto)) {
                            unlink("../" . $oldPhoto);
                        }
                        $photo = 'uploads/materiel/' . $fileName;
                        $stmt = $connexion->prepare("UPDATE materiel SET nom = ?, type = ?, description = ?, numero_serie = ?, etat = ?, disponible = ?, photo = ? WHERE id = ?");
                        $stmt->execute([$nom, $type, $description, $numero_serie, $etat, $disponible, $photo, $id]);
                    }
                } else {
                    $stmt = $connexion->prepare("UPDATE materiel SET nom = ?, type = ?, description = ?, numero_serie = ?, etat = ?, disponible = ? WHERE id = ?");
                    $stmt->execute([$nom, $type, $description, $numero_serie, $etat, $disponible, $id]);
                }

                echo json_encode(['success' => 'Matériel modifié avec succès']);
                break;

            case 'delete':
                if (!isset($data['materiel_id'])) {
                    throw new Exception('ID de matériel manquant');
                }

                $id = (int)$data['materiel_id'];

                $stmt = $connexion->prepare("SELECT photo FROM materiel WHERE id = ?");
                $stmt->execute([$id]);
                $photo = $stmt->fetchColumn();

                if ($photo && file_exists("../" . $photo)) {
                    unlink("../" . $photo);
                }

                $stmt = $connexion->prepare("DELETE FROM materiel WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => 'Matériel supprimé avec succès']);
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
