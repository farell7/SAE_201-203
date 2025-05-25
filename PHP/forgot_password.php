<?php
session_start();
require_once 'config.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $message = "Veuillez fournir une adresse email";
        $messageType = 'error';
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Générer un token unique
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Sauvegarder le token dans la base de données
                $stmt = $conn->prepare("UPDATE utilisateur SET reset_token = ?, reset_expiry = ? WHERE email = ?");
                $stmt->execute([$token, $expiry, $email]);
                
                // Envoyer l'email (simulation)
                $resetLink = "http://localhost/SAE_201-203/PHP/reset_password.php?token=" . $token;
                
                $message = "Si cette adresse email existe dans notre base de données, vous recevrez un lien de réinitialisation.<br>
                          Pour cette démo, voici le lien : <a href='$resetLink'>Réinitialiser le mot de passe</a>";
                $messageType = 'success';
            } else {
                // Pour des raisons de sécurité, on affiche le même message que si l'email existe
                $message = "Si cette adresse email existe dans notre base de données, vous recevrez un lien de réinitialisation.";
                $messageType = 'info';
            }
        } catch(PDOException $e) {
            $message = "Une erreur est survenue. Veuillez réessayer plus tard.";
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - ResaUGE</title>
    <link rel="stylesheet" href="../CSS/styleindex.css">
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
        .message.info {
            background-color: #e6f2ff;
            color: #004d99;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input[type="email"] {
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
        <h1>Mot de passe oublié</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="forgot_password.php">
            <input type="email" name="email" placeholder="Votre adresse email" required>
            <button type="submit">Réinitialiser le mot de passe</button>
        </form>
        
        <div class="back-link">
            <a href="../index.php">Retour à la connexion</a>
        </div>
    </div>
</body>
</html> 