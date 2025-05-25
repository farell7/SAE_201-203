<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    require_once 'PHP/config.php';
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['utilisateur'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            switch($user['role']) {
                case 'admin':
                    header('Location: PHP/admin.php');
                    break;
                case 'teacher':
                    header('Location: PHP/teacher.php');
                    break;
                case 'agent':
                    header('Location: PHP/agent.php');
                    break;
                case 'student':
                    header('Location: PHP/student.php');
                    break;
                default:
                    $_SESSION['error'] = 'invalid_role';
                    header('Location: index.php');
            }
            exit();
        } else {
            $_SESSION['error'] = 'invalid_credentials';
            header('Location: index.php');
            exit();
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = 'database_error';
        header('Location: index.php');
        exit();
    }
}

if (isset($_SESSION['utilisateur'])) {
    $role = $_SESSION['utilisateur']['role'];
    switch($role) {
        case 'admin':
            header('Location: PHP/admin.php');
            break;
        case 'teacher':
            header('Location: PHP/teacher.php');
            break;
        case 'agent':
            header('Location: PHP/agent.php');
            break;
        case 'student':
            header('Location: PHP/student.php');
            break;
        default:
            header('Location: index.php');
    }
    exit();
}

$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./CSS/styleindex.css">
    <title>ResaUGE - Connexion</title>
</head>

<body id="login-page">
    <div class="container" id="container">
        <div class="form-container sign-up">
            <div class="register-title"><h1>Créer un compte</h1></div>
            <form action="PHP/register.php" method="POST">
                <input type="text" name="nom" placeholder="Nom" id="register-name" required> 
                <input type="text" name="prenom" placeholder="Prénom" id="register-firstname" required>
                <input type="email" name="email" placeholder="Email" id="register-email" required>
                <input type="password" name="password" placeholder="Mot de passe" id="register-password" required>
                <input type="text" name="pseudo" placeholder="Pseudo" id="register-pseudo" required>
                <input type="text" name="postal" placeholder="Code Postal" id="register-postal" pattern="^\d{5}$" title="Entrez un code postal Français à 5chiffres" required>
                <input type="date" name="birthdate" placeholder="Date de naissance" id="register-birthdate" required>
                <select name="role" id="register-role" required>
                    <option value="student">Etudiant</option>
                    <option value="teacher">Enseignant</option>
                    <option value="agent">Agent</option>
                    <option value="admin">Administrateur</option>
                </select>
                <button type="submit">S'inscrire</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="index.php" method="POST">
                <h1>Se connecter</h1>
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php
                        switch($error) {
                            case 'invalid_credentials':
                                echo "<p style='color: red;'>Email ou mot de passe incorrect</p>";
                                break;
                            case 'database_error':
                                echo "<p style='color: red;'>Erreur de connexion à la base de données</p>";
                                break;
                            case 'invalid_role':
                                echo "<p style='color: red;'>Erreur de rôle utilisateur</p>";
                                break;
                            default:
                                echo "<p style='color: red;'>Une erreur est survenue</p>";
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <input type="email" name="email" placeholder="Email" id="login-email" required>
                <input type="password" name="password" placeholder="Mot de passe" id="login-password" required>
                <a href="PHP/forgot_password.php">Mot de passe oublié?</a>
                <button type="submit">Se connecter</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bienvenue !</h1>
                    <p id="role-description-login">Saisissez vos données personnelles pour profiter pleinement de toutes les fonctionnalités du service.</p>
                    <button class="hidden" id="login">Se connecter</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Ravi de vous revoir !</h1>
                    <p id="role-description-register">Connectez vous avec vos informations de connexion pour accéder à toutes les fonctionnalités du service.</p>
                    <button class="hidden" id="register">S'inscrire</button>
                </div>
            </div>
        </div>
    </div>

    <script src="./JS/script.js"></script>
</body>
</html>