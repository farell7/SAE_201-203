<?php
require_once('check_session.php');
require_once('../includes/db.php');

// Vérifier si la colonne compte_valide existe, sinon la créer
try {
    $conn->query("ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS compte_valide BOOLEAN DEFAULT FALSE");
    // Mettre à jour les enregistrements existants si nécessaire
    $conn->query("UPDATE utilisateur SET compte_valide = 1 WHERE role = 'admin' AND compte_valide IS NULL");
} catch(PDOException $e) {
    // Ignorer l'erreur si la colonne existe déjà
}

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
                $query = "UPDATE utilisateur SET compte_valide = 1 WHERE id = :id";
                $message = "validation";
                break;
            case 'refuser':
                $query = "UPDATE utilisateur SET compte_valide = 2 WHERE id = :id"; // 2 = refusé
                $message = "refus";
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
    $query = "SELECT id, nom, prenom, email, role, compte_valide, date_creation 
              FROM utilisateur 
              WHERE role != 'admin'  -- Ne pas afficher les administrateurs
              ORDER BY CASE 
                WHEN compte_valide = 0 THEN 1  -- En attente en premier
                WHEN compte_valide = 2 THEN 2  -- Refusés ensuite
                ELSE 3                         -- Validés en dernier
              END, 
              date_creation DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>
