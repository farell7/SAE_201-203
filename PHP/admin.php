<?php
require_once 'check_session.php';
// Le fichier check_session.php s'occupe déjà de toutes les vérifications
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Administrateur - ResaUGE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2f2a85;
            --dark-color: #1d2125;
            --light-color: #ffffff;
            --gray-color: #e9ecef;
            --secondary-gray: #8f959e;
        }

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            background-color: var(--primary-color);
            width: 280px;
            padding: 2rem 0;
            min-height: 100vh;
        }

        .sidebar h2 {
            color: var(--light-color);
            text-align: center;
            margin-bottom: 2rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 0.8rem 1.5rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--light-color);
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .nav-link i {
            margin-right: 0.5rem;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background-color: var(--gray-color);
        }

        .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }

        .card-img-top {
            width: 64px;
            height: 64px;
            margin: 1.5rem auto;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .card-text {
            color: var(--secondary-gray);
        }

        .btn-custom {
            background-color: var(--primary-color);
            color: var(--light-color);
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #27236e;
            color: var(--light-color);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Administration</h2>
        <nav class="nav flex-column">
            <a class="nav-link" href="#"><i class="bi bi-house-door"></i>Accueil</a>
            <a class="nav-link" href="dashboard_admin.php"><i class="bi bi-speedometer2"></i>Tableau de bord</a>
            <a class="nav-link" href="validation_compte.php"><i class="bi bi-person-check"></i>Validation des utilisateurs</a>
            <a class="nav-link" href="gestion_utilisateurs.php"><i class="bi bi-people"></i>Gestion des utilisateurs</a>
            <a class="nav-link" href="suivi_admin.php"><i class="bi bi-graph-up"></i>Suivi</a>
            <a class="nav-link" href="gestion_materiel.php"><i class="bi bi-tools"></i>Gestion du matériel</a>
            <a class="nav-link" href="gestion_salle.php"><i class="bi bi-building"></i>Gestion des salles</a>
            <a class="nav-link" href="gestion_reservations.php"><i class="bi bi-calendar-check"></i>Gestion des réservations</a>
            <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i>Déconnexion</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container">
            <h1 class="display-4 mb-4">Bonjour, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
            <p class="lead text-muted mb-5">Espace d'administration</p>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="../img/emplacement.png" class="card-img-top" alt="Gestion des salles">
                        <div class="card-body text-center">
                            <h5 class="card-title">Gestion des salles</h5>
                            <p class="card-text">Gérez les salles de l'université et leur disponibilité.</p>
                            <a href="gestion_salle.php" class="btn btn-custom w-100">Gérer</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="../img/cadenas-verrouille.png" class="card-img-top" alt="Gestion du matériel">
                        <div class="card-body text-center">
                            <h5 class="card-title">Gestion du matériel</h5>
                            <p class="card-text">Gérez le matériel et les équipements disponibles.</p>
                            <a href="gestion_materiel.php" class="btn btn-custom w-100">Gérer</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="../img/profil.png" class="card-img-top" alt="Gestion des utilisateurs">
                        <div class="card-body text-center">
                            <h5 class="card-title">Gestion des utilisateurs</h5>
                            <p class="card-text">Gérez les comptes utilisateurs et leurs droits.</p>
                            <a href="gestion_utilisateurs.php" class="btn btn-custom w-100">Gérer</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="../img/nom.png" class="card-img-top" alt="Suivi des réservations">
                        <div class="card-body text-center">
                            <h5 class="card-title">Suivi des réservations</h5>
                            <p class="card-text">Consultez et gérez les réservations en cours.</p>
                            <a href="gestion_reservations.php" class="btn btn-custom w-100">Gérer</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


