<?php
session_start();
require_once 'includes/redirect_role.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit();
}

$user = $_SESSION['utilisateur'];
if ($user['role'] !== 'student') {
    redirect_to_role_home();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Étudiant - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/student.css">
</head>
<body>
    <nav class="nav-container">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
            <div class="nav-menu">
                <a href="student.php" class="active">Accueil</a>
                <a href="reservation_salle.php">Réservations</a>
                <a href="#">Mon Compte</a>
            </div>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <a href="../logout.php" class="logout-btn">Déconnexion</a>
        </div>
    </nav>

    <div class="main-content">
        <h1>Bonjour, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?> !</h1>
        <div class="course-cards">
            <a href="reservation_salle.php" class="course-card">
                <div class="icon-container">
                    <img src="../img/emplacement.png" alt="Réserver une salle">
                </div>
                <h3>Réserver une Salle</h3>
                <p>Réservez une salle pour vos travaux</p>
            </a>
            <a href="reservation_materiel.php" class="course-card">
                <div class="icon-container">
                    <img src="../img/cadenas-verrouille.png" alt="Réserver du matériel">
                </div>
                <h3>Réserver du Matériel</h3>
                <p>Empruntez le matériel nécessaire</p>
            </a>
            <div class="course-card">
                <div class="icon-container">
                    <img src="../img/nom.png" alt="Mes réservations">
                </div>
                <h3>Mes Réservations</h3>
                <p>Consultez vos réservations</p>
            </div>
            <div class="course-card">
                <div class="icon-container">
                    <img src="../img/profil.png" alt="Disponibilités">
                </div>
                <h3>Disponibilités</h3>
                <p>Voir les disponibilités</p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Université Eiffel</p>
    </footer>
</body>
</html>