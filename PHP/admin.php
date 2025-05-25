<?php
session_start();
// Supposons que le nom d'utilisateur est stocké dans $_SESSION['username']//
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Université Eiffel</title>
    <link rel="stylesheet" href="/CSS/styleadmin.Css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background:rgb(242, 245, 250);
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 240px;
            background:rgb(29, 37, 126);
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 30px 0;
            min-height: 100vh;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .sidebar nav a {
            color: #fff;
            text-decoration: none;
            padding: 15px 30px;
            display: block;
            transition: background 0.2s;
            font-size: 1.1em;
        }
        .sidebar nav a:hover {
            background: #3949ab;
        }
        .main-content {
            flex: 1;
            padding: 40px 60px;
        }
        .welcome {
            font-size: 2em;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #3949ab;
            margin-bottom: 30px;
        }
        .admin-cards {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .admin-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(60,60,100,0.08);
            padding: 30px 24px;
            flex: 1 1 260px;
            min-width: 260px;
            max-width: 320px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .admin-card:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 8px 24px rgba(60,60,100,0.15);
        }
        .admin-card img {
            width: 65px;
            height: 65px;
            margin-bottom: 18px;
        }
        .admin-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.2em;
            color:rgb(29, 37, 126);
        }
        .admin-card p {
            color: #555;
            text-align: center;
            font-size: 1em;
        }
        footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100vw;
            background:rgb(30, 38, 122);
            color: #fff;
            text-align: center;
            padding: 12px 0;
            font-size: 0.95em;
            letter-spacing: 1px;
        }
        @media (max-width: 900px) {
            .main-content { padding: 20px 10px; }
            .admin-cards { flex-direction: column; gap: 18px; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>Admin</h2>
        <nav>
            <a href="#">Accueil</a>
            <a href="#">Tableau de bord</a>
            <a href="validation_compte.php">Validation des utilisateurs</a>
            <a href="#">Utilisateurs</a>
            <a href="#">Suivi</a>
            <a href="#">Objets & Salles</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="welcome">Bonjour, <?php echo $username; ?> !</div>
        <div class="subtitle">Espace d'administration</div>
        <div class="admin-cards">
            <div class="admin-card">
                <img src="/img/emplacement.png" alt="Gestion des salles">
                <h3>Gestion des salles</h3>
                <p>Ajoutez, modifiez ou supprimez les salles de l'université. Consultez leur disponibilité en temps réel.</p>
            </div>
            <div class="admin-card">
                <img src="/img/cadenas-verrouille.png" alt="Gestion du matériel">
                <h3>Gestion du matériel</h3>
                <p>Gérez le matériel disponible, attribuez-le aux salles ou utilisateurs, et suivez l'état des équipements.</p>
            </div>
            <div class="admin-card">
                <img src="/img/profil.png" alt="Gestion des utilisateurs">
                <h3>Gestion des utilisateurs</h3>
                <p>Créez, modifiez ou supprimez des comptes utilisateurs. Attribuez des rôles et surveillez l'activité.</p>
            </div>
            <div class="admin-card">
                <img src="/img/nom.png" alt="Suivi des réservations">
                <h3>Suivi des réservations</h3>
                <p>Visualisez et gérez toutes les réservations en cours ou passées. Exportez les rapports facilement.</p>
            </div>
        </div>
    </main>
    <footer>
        &copy;2025 Université Eiffel. Tous droits réservés.
    </footer>
</body>
</html>


