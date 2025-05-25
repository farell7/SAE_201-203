<?php
session_start();
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'teacher') {
    header('Location: ../index.html');
    exit();
}
$user = $_SESSION['utilisateur'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Enseignant - ResaUGE</title>
    <link rel="stylesheet" href="../CSS/stylestudent.css">
</head>
<body id="page0">
    <nav>
        <a href="#">Accueil</a>
        <a href="#">Mes Réservations</a>
        <a href="#">Réserver une Salle</a>
        <a href="#">Planning</a>
        <a href="logout.php">Déconnexion</a>
    </nav>

    <div class="main-content">
        <h1>Bonjour, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?> !</h1>
        <h2>Espace Enseignant</h2>
        <div class="course-cards">
            <div class="course-card">
                <img src="../img/emplacement.png" alt="Réserver une salle">
                <h3>Réserver une Salle</h3>
                <p>Réservez une salle pour vos cours et activités pédagogiques.</p>
            </div>
            <div class="course-card">
                <img src="../img/cadenas-verrouille.png" alt="Mes réservations">
                <h3>Mes Réservations</h3>
                <p>Consultez et gérez vos réservations en cours.</p>
            </div>
            <div class="course-card">
                <img src="../img/profil.png" alt="Planning">
                <h3>Planning</h3>
                <p>Consultez le planning des salles disponibles.</p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy;2025 Université Eiffel. Tous droits réservés.</p>
    </footer>
</body>
</html>

