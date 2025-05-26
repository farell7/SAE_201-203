<?php include 'regis_proc.php'; ?>
 <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmation d'inscription - ResaUGE</title>
        <link rel="stylesheet" href="../CSS/modal.css">
    </head>
    <body>
        <div class="modal-overlay" style="display: block;">
            <div class="modal-container">
                <div class="modal-header">
                    <h2 class="modal-title"><?php echo $type === 'success' ? 'Inscription réussie' : 'Erreur'; ?></h2>
                </div>
                <div class="modal-body">
                    <p class="modal-message <?php echo $type; ?>"><?php echo $message; ?></p>
                </div>
                <div class="modal-footer">
                    <button class="modal-button" onclick="window.location.href='../index.php'">
                        <?php echo $type === 'success' ? 'Retour à l\'accueil' : 'Réessayer'; ?>
                    </button>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>

<?php
require_once('connexion.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $pseudo = htmlspecialchars(trim($_POST['pseudo']));
    $postal = htmlspecialchars(trim($_POST['postal']));
    $birthdate = $_POST['birthdate'];
    $role = $_POST['role'];

    try {
        // Vérifier si l'email existe déjà
        $check_stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $check_stmt->execute([$email]);

        if ($check_stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Cette adresse email est déjà utilisée.';
            $_SESSION['message_type'] = 'error';
        } else {
            // Insérer le nouvel utilisateur
            $valide = ($role === 'admin') ? 1 : 0; // Les admins sont automatiquement validés
            $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, pseudo, code_postal, date_naissance, role, valide) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $password, $pseudo, $postal, $birthdate, $role, $valide]);

            $_SESSION['message'] = $role === 'admin' ? 
                'Compte administrateur créé avec succès. Vous pouvez vous connecter.' :
                'Compte créé avec succès. Un administrateur doit valider votre compte avant que vous puissiez vous connecter.';
            $_SESSION['message_type'] = 'success';
        }
    } catch(PDOException $e) {
        $_SESSION['message'] = 'Erreur lors de l\'inscription : ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
    
    // Redirection vers la page d'accueil
    header('Location: ../index.php');
    exit();
}
?> 