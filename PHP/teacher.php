<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Enseignant';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enseignant - Université Eiffel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
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
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: var(--primary-color);
            min-height: 100vh;
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
            transition: transform 0.2s ease;
        }

        .nav-link:hover i {
            transform: translateX(3px);
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            background-color: var(--light-color);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(47, 42, 133, 0.15);
        }

        .card-img-top {
            width: 65px;
            height: 65px;
            margin: 1rem auto;
        }

        .card-title {
            color: var(--primary-color);
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

        .footer {
            background-color: var(--primary-color);
            color: var(--light-color);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="d-flex flex-column h-100">
                    <h2 class="text-white text-center py-4 mb-4">Enseignant</h2>
                    <nav class="nav flex-column">
                        <a class="nav-link d-flex align-items-center" href="#">
                            <i class="bi bi-house-door me-2"></i>Accueil
                        </a>
                        <a class="nav-link d-flex align-items-center" href="dashboard_teacher.php">
                            <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                        </a>
                        <a class="nav-link d-flex align-items-center" href="mes_cours.php">
                            <i class="bi bi-book me-2"></i>Mes cours
                        </a>
                        <a class="nav-link d-flex align-items-center" href="mes_reservations.php">
                            <i class="bi bi-calendar-check me-2"></i>Mes réservations
                        </a>
                        <a class="nav-link d-flex align-items-center" href="reserver_salle.php">
                            <i class="bi bi-building me-2"></i>Réserver une salle
                        </a>
                        <a class="nav-link d-flex align-items-center" href="reserver_materiel.php">
                            <i class="bi bi-laptop me-2"></i>Réserver du matériel
                        </a>
                        <a class="nav-link d-flex align-items-center" href="mon_profil.php">
                            <i class="bi bi-person me-2"></i>Mon profil
                        </a>
                        <a class="nav-link d-flex align-items-center" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-4 py-4">
                <div class="container">
                    <h1 class="display-5 fw-bold mb-2">Bonjour, <?php echo $username; ?> !</h1>
                    <p class="text-primary fs-4 mb-5">Espace enseignant</p>

                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <img src="/img/cours.png" class="card-img-top" alt="Mes cours">
                                    <h5 class="card-title mt-3">Mes cours</h5>
                                    <p class="card-text text-muted">Consultez et gérez vos cours, emplois du temps et groupes d'étudiants.</p>
                                    <a href="mes_cours.php" class="btn btn-custom w-100">Accéder</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <img src="/img/emplacement.png" class="card-img-top" alt="Réserver une salle">
                                    <h5 class="card-title mt-3">Réserver une salle</h5>
                                    <p class="card-text text-muted">Réservez des salles pour vos cours ou réunions. Consultez les disponibilités.</p>
                                    <a href="reserver_salle.php" class="btn btn-custom w-100">Réserver</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <img src="/img/cadenas-verrouille.png" class="card-img-top" alt="Réserver du matériel">
                                    <h5 class="card-title mt-3">Réserver du matériel</h5>
                                    <p class="card-text text-muted">Réservez le matériel nécessaire pour vos cours : ordinateurs, projecteurs, etc.</p>
                                    <a href="reserver_materiel.php" class="btn btn-custom w-100">Réserver</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <img src="/img/nom.png" class="card-img-top" alt="Mes réservations">
                                    <h5 class="card-title mt-3">Mes réservations</h5>
                                    <p class="card-text text-muted">Consultez l'historique de vos réservations et gérez vos demandes en cours.</p>
                                    <a href="mes_reservations.php" class="btn btn-custom w-100">Consulter</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <footer class="footer fixed-bottom py-3">
        <div class="container text-center">
            <span>&copy; 2025 Université Eiffel. Tous droits réservés.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

