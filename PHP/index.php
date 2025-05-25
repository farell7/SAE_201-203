<?php
session_start();
header('Content-Type: application/json');

// Si l'utilisateur est déjà connecté, on renvoie la redirection appropriée
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    $redirect = '';
    
    switch ($role) {
        case 'admin':
            $redirect = '../HTML/admin.html';
            break;
        case 'agent':
            $redirect = '../HTML/agent.html';
            break;
        case 'teacher':
            $redirect = '../HTML/teacher.html';
            break;
        case 'student':
            $redirect = '../HTML/student.html';
            break;
    }
    
    echo json_encode(['redirect' => $redirect]);
    exit();
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        echo json_encode(['error' => 'Email et mot de passe requis']);
        exit();
    }

    require_once 'connexion.php';
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    try {
        $stmt = $connexion->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['valide'] == 0) {
                echo json_encode(['error' => 'Votre compte est en attente de validation']);
                exit();
            }
            
            // Stocker les informations de l'utilisateur en session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            // Renvoyer la redirection selon le rôle
            $redirect = '';
            switch ($user['role']) {
                case 'admin':
                    $redirect = '../HTML/admin.html';
                    break;
                case 'agent':
                    $redirect = '../HTML/agent.html';
                    break;
                case 'teacher':
                    $redirect = '../HTML/teacher.html';
                    break;
                case 'student':
                    $redirect = '../HTML/student.html';
                    break;
                default:
                    echo json_encode(['error' => 'Rôle invalide']);
                    exit();
            }
            
            echo json_encode(['redirect' => $redirect]);
            exit();
        } else {
            echo json_encode(['error' => 'Email ou mot de passe incorrect']);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Une erreur est survenue, veuillez réessayer']);
        exit();
    }
}

echo json_encode(['error' => 'Méthode non autorisée']);
?> 