<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit();
}

$user = $_SESSION['utilisateur'];
if ($user['role'] !== 'student') {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Étudiant - ResaUGE</title>
    <link rel="stylesheet" href="../CSS/stylestudent.css">
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: rgb(242, 245, 250);
            margin: 0;
            min-height: 100vh;
        }

        nav {
            background: rgb(29, 37, 126);
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            padding: 80px 2rem 2rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: rgb(29, 37, 126);
            margin-bottom: 0.5rem;
        }

        h2 {
            color: #666;
            margin-bottom: 2rem;
        }

        .course-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }

        .course-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 2px 12px rgba(60,60,100,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(60,60,100,0.15);
        }

        .course-card img {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
        }

        .course-card h3 {
            color: rgb(29, 37, 126);
            margin-bottom: 1rem;
        }

        .course-card p {
            color: #666;
            line-height: 1.5;
        }

        footer {
            background: rgb(29, 37, 126);
            color: white;
            text-align: center;
            padding: 1rem;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        @media (max-width: 768px) {
            .course-cards {
                grid-template-columns: 1fr;
            }

            nav {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            nav a {
                margin: 0.25rem 0;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="#">Accueil</a>
        <a href="mes_reservations_etudiant.php">Mes Réservations</a>
        <a href="reserver_salle_etudiant.php">Réserver une Salle</a>
        <a href="reserver_materiel_etudiant.php">Réserver du Matériel</a>
        <a href="disponibilites.php">Disponibilités</a>
        <a href="notifications_etudiant.php">Notifications</a>
        <a href="mon_profil.php">Mon Profil</a>
        <a href="logout.php">Déconnexion</a>
    </nav>

    <div class="main-content">
        <h1>Bonjour, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?> !</h1>
        <h2>Espace Étudiant</h2>
        <div class="course-cards">
            <div class="course-card">
                <img src="../img/emplacement.png" alt="Réserver une salle">
                <h3>Réserver une Salle</h3>
                <p>Réservez une salle pour vos travaux de groupe ou études personnelles.</p>
            </div>
            <div class="course-card">
                <img src="../img/cadenas-verrouille.png" alt="Réserver du matériel">
                <h3>Réserver du Matériel</h3>
                <p>Empruntez le matériel nécessaire pour vos projets et présentations.</p>
            </div>
            <div class="course-card">
                <img src="../img/nom.png" alt="Mes réservations">
                <h3>Mes Réservations</h3>
                <p>Consultez et gérez vos réservations en cours.</p>
            </div>
            <div class="course-card">
                <img src="../img/profil.png" alt="Disponibilités">
                <h3>Disponibilités</h3>
                <p>Consultez les disponibilités des salles et du matériel en temps réel.</p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Université Eiffel. Tous droits réservés.</p>
    </footer>
</body>
</html>