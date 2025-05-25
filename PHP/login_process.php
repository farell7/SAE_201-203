<?php
require_once('connexion.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    try {
        // Vérification de l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Vérification si le compte est validé
            if ($user['valide'] == 0) {
                echo "<script>
                    alert('Votre compte est en attente de validation par l\\'administrateur.');
                    window.location.href = '../index.php';
                </script>";
                exit();
            }
            
            // Connexion réussie
            $_SESSION['utilisateur'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            // Redirection selon le rôle
            switch($user['role']) {
                case 'admin':
                    header('Location: admin_dashboard.php');
                    break;
                case 'agent':
                    header('Location: agent_dashboard.php');
                    break;
                case 'teacher':
                    header('Location: teacher_dashboard.php');
                    break;
                default:
                    header('Location: student_dashboard.php');
            }
            exit();
            
        } else {
            echo "<script>
                alert('Email ou mot de passe incorrect.');
                window.location.href = '../index.php';
            </script>";
            exit();
        }
        
    } catch(PDOException $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        echo "<script>
            alert('Une erreur est survenue lors de la connexion.');
            window.location.href = '../index.php';
        </script>";
        exit();
    }
}
?> 