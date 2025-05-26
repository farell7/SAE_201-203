<?php
session_start();
require_once 'includes/redirect_role.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit();
}

require_once 'connexion.php';

$message = '';
$messageType = '';

// Traitement de la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_SESSION['utilisateur']['id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $pseudo = $_POST['pseudo'];
        $code_postal = $_POST['code_postal'];
        $numero_etudiant = $_POST['numero_etudiant'];
        $date_naissance = $_POST['date_naissance'];

        // Vérification du code postal
        if (!empty($code_postal) && !preg_match("/^[0-9]{5}$/", $code_postal)) {
            throw new Exception("Le code postal doit contenir 5 chiffres.");
        }

        // Gestion de la photo de profil
        $photo_path = $_SESSION['utilisateur']['photo'] ?? null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                throw new Exception("Format de fichier non autorisé. Formats acceptés : " . implode(', ', $allowed));
            }
            
            // Créer le dossier uploads/profil s'il n'existe pas
            $upload_dir = '../uploads/profil/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Générer un nom de fichier unique
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                // Supprimer l'ancienne photo si elle existe
                if ($photo_path && file_exists('../' . $photo_path)) {
                    unlink('../' . $photo_path);
                }
                $photo_path = 'uploads/profil/' . $new_filename;
            } else {
                throw new Exception("Erreur lors du téléchargement de la photo.");
            }
        }

        // Mise à jour des informations
        $sql = "UPDATE utilisateur SET 
                nom = :nom,
                prenom = :prenom,
                email = :email,
                pseudo = :pseudo,
                code_postal = :code_postal,
                numero_etudiant = :numero_etudiant,
                date_naissance = :date_naissance,
                photo = :photo
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'pseudo' => $pseudo,
            'code_postal' => $code_postal,
            'numero_etudiant' => $numero_etudiant,
            'date_naissance' => $date_naissance,
            'photo' => $photo_path,
            'id' => $user_id
        ]);

        // Mise à jour de la session
        $_SESSION['utilisateur']['nom'] = $nom;
        $_SESSION['utilisateur']['prenom'] = $prenom;
        $_SESSION['utilisateur']['email'] = $email;
        $_SESSION['utilisateur']['pseudo'] = $pseudo;
        $_SESSION['utilisateur']['code_postal'] = $code_postal;
        $_SESSION['utilisateur']['numero_etudiant'] = $numero_etudiant;
        $_SESSION['utilisateur']['date_naissance'] = $date_naissance;
        $_SESSION['utilisateur']['photo'] = $photo_path;

        $message = "Profil mis à jour avec succès !";
        $messageType = "success";
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = "error";
    }
}

// Récupération des informations de l'utilisateur
$user = $_SESSION['utilisateur'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e8e;
            --secondary-color: #f8f9fa;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Noto Sans', sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        .nav-container {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .nav-menu {
            display: flex;
            gap: 1.5rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .nav-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-menu a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-header h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            overflow: hidden;
            position: relative;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-photo .upload-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            padding: 0.5rem;
            color: white;
            font-size: 0.8rem;
            text-align: center;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .profile-photo:hover .upload-overlay {
            opacity: 1;
        }

        .profile-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: grid;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: #4a5568;
        }

        .form-group input {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(44, 62, 142, 0.1);
        }

        .btn-save {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-save:hover {
            background-color: #1e2b6b;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #34d399;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        .footer {
            text-align: center;
            padding: 2rem;
            color: #666;
            background-color: var(--primary-color);
            color: white;
        }

        #photo-input {
            display: none;
        }
    </style>
</head>
<body>
    <nav class="nav-container">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="../img/logo_sansfond.png" alt="Logo" style="height: 40px;">
            <div class="nav-menu">
                <?php if ($_SESSION['utilisateur']['role'] === 'admin'): ?>
                    <a href="admin.php">Accueil</a>
                    <a href="gestion_materiel.php">Gestion du matériel</a>
                    <a href="gestion_salle.php">Gestion des salles</a>
                    <a href="validation_compte.php">Utilisateurs</a>
                <?php else: ?>
                    <a href="student.php">Accueil</a>
                    <a href="reservation_materiel.php">Réservations</a>
                <?php endif; ?>
                <a href="profil.php" class="active">Mon Compte</a>
            </div>
        </div>
        <div class="profile-menu">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></span>
            </div>
            <a href="../logout.php" style="color: white; text-decoration: none; margin-left: 1rem;">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </nav>

    <div class="main-content">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <h1>Mon Profil</h1>
            <p>Gérez vos informations personnelles</p>
        </div>

        <form method="POST" class="profile-form" enctype="multipart/form-data">
            <div class="profile-photo">
                <?php if (!empty($user['photo']) && file_exists('../' . $user['photo'])): ?>
                    <img src="../<?php echo htmlspecialchars($user['photo']); ?>" alt="Photo de profil">
                <?php else: ?>
                    <i class="fas fa-user" style="font-size: 4rem; color: #666;"></i>
                <?php endif; ?>
                <div class="upload-overlay" onclick="document.getElementById('photo-input').click();">
                    <i class="fas fa-camera"></i> Changer la photo
                </div>
                <input type="file" id="photo-input" name="photo" accept="image/*" style="display: none;"
                       onchange="previewImage(this);">
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="pseudo">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" value="<?php echo htmlspecialchars($user['pseudo'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="code_postal">Code Postal</label>
                <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($user['code_postal'] ?? ''); ?>" pattern="[0-9]{5}">
            </div>

            <div class="form-group">
                <label for="numero_etudiant">Numéro Étudiant</label>
                <input type="text" id="numero_etudiant" name="numero_etudiant" value="<?php echo htmlspecialchars($user['numero_etudiant'] ?? ''); ?>" <?php echo $user['role'] === 'student' ? 'required' : ''; ?>>
            </div>

            <div class="form-group">
                <label for="date_naissance">Date de Naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($user['date_naissance'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn-save">Enregistrer les modifications</button>
        </form>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> ResaUGE - IUT de Meaux</p>
        <p>Département MMI</p>
    </footer>

    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const photoContainer = document.querySelector('.profile-photo');
                const existingImg = photoContainer.querySelector('img');
                const existingIcon = photoContainer.querySelector('i.fa-user');
                
                if (existingIcon) {
                    existingIcon.remove();
                }
                
                if (existingImg) {
                    existingImg.src = e.target.result;
                } else {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = "Photo de profil";
                    photoContainer.insertBefore(img, photoContainer.firstChild);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html> 