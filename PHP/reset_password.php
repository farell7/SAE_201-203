<?php
session_start();
require_once 'config.php';

// Définir le chemin de base s'il n'est pas déjà défini
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/SAE_201-203');
}

$message = '';
$messageType = '';
$validToken = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE reset_token = ? AND reset_expiry > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            $validToken = true;
        } else {
            $message = "Ce lien de réinitialisation est invalide ou a expiré.";
            $messageType = 'error';
        }
    } catch(PDOException $e) {
        $message = "Une erreur est survenue. Veuillez réessayer plus tard.";
        $messageType = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token']) && isset($_POST['password'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE reset_token = ? AND reset_expiry > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Mettre à jour le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE utilisateur SET mot_de_passe = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
            $stmt->execute([$hashedPassword, $user['id']]);
            
            $message = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
            $messageType = 'success';
            header("refresh:3;url=" . BASE_PATH . "/index.php"); // Redirection après 3 secondes
        } else {
            $message = "Ce lien de réinitialisation est invalide ou a expiré.";
            $messageType = 'error';
        }
    } catch(PDOException $e) {
        $message = "Une erreur est survenue. Veuillez réessayer plus tard.";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe - ResaUGE</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/CSS/styleindex.css">
    <style>
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .message.error {
            background-color: #ffe6e6;
            color: #ff0000;
        }
        .message.success {
            background-color: #e6ffe6;
            color: #006600;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input[type="password"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background: #2f2a85;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #1d1b54;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Réinitialisation du mot de passe</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($validToken): ?>
            <form method="POST" action="<?php echo BASE_PATH; ?>/PHP/reset_password.php">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="password" name="password" placeholder="Nouveau mot de passe" required minlength="6">
                <input type="password" name="password_confirm" placeholder="Confirmez le mot de passe" required minlength="6">
                <button type="submit">Réinitialiser le mot de passe</button>
            </form>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="<?php echo BASE_PATH; ?>/index.php">Retour à la connexion</a>
        </div>
    </div>
    
    <?php if ($validToken): ?>
    <script>
    document.querySelector('form').onsubmit = function(e) {
        var password = document.querySelector('input[name="password"]').value;
        var confirm = document.querySelector('input[name="password_confirm"]').value;
        
        if (password !== confirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
            return false;
        }
    };
    </script>
    <?php endif; ?>
</body>
</html> 