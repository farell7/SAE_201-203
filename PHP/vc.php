<?php
require_once('check_session.php');
require_once('../includes/db.php');

// Vérifier si l'utilisateur est un admin
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Traitement des requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = isset($data['userId']) ? intval($data['userId']) : 0;
    $action = isset($data['action']) ? $data['action'] : '';

    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide']);
        exit();
    }

    try {
        switch ($action) {
            case 'valider':
                $query = "UPDATE utilisateur SET valide = 1 WHERE id = :id";
                $message = "validation";
                break;
            case 'supprimer':
                $query = "DELETE FROM utilisateur WHERE id = :id";
                $message = "suppression";
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Action non valide']);
                exit();
        }

        $stmt = $conn->prepare($query);
        $stmt->execute(['id' => $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucun compte trouvé avec cet ID']);
        }
        exit();
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur lors de la $message du compte"]);
        exit();
    }
}

// Récupérer tous les utilisateurs
try {
    $query = "SELECT id, nom, prenom, email, role, valide, date_creation 
              FROM utilisateur 
              ORDER BY date_creation DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>
