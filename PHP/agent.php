<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Agent';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent - Université Eiffel</title>
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
            --success-color: #35c8b0;
            --warning-color: #ffc107;
            --danger-color: #ca3120;
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

        .badge-success {
            background-color: var(--success-color);
        }

        .badge-warning {
            background-color: var(--warning-color);
        }

        .badge-danger {
            background-color: var(--danger-color);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="d-flex flex-column h-100">
                    <h2 class="text-white text-center py-4 mb-4">Agent</h2>
                    <nav class="nav flex-column">
                        <a class="nav-link d-flex align-items-center" href="#">
                            <i class="bi bi-house-door me-2"></i>Accueil
                        </a>
                        <a class="nav-link d-flex align-items-center" href="dashboard_agent.php">
                            <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                        </a>
                        <a class="nav-link d-flex align-items-center" href="gestion_reservations_agent.php">
                            <i class="bi bi-calendar-check me-2"></i>Gestion des réservations
                        </a>
                        <a class="nav-link d-flex align-items-center" href="gestion_materiel_agent.php">
                            <i class="bi bi-tools me-2"></i>Gestion du matériel
                        </a>
                        <a class="nav-link d-flex align-items-center" href="gestion_salles_agent.php">
                            <i class="bi bi-building me-2"></i>Gestion des salles
                        </a>
                        <a class="nav-link d-flex align-items-center" href="suivi_equipements.php">
                            <i class="bi bi-graph-up me-2"></i>Suivi des équipements
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
                    <p class="text-primary fs-4 mb-5">Espace Agent</p>

                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <img src="/img/calendar.png" class="card-img-top" alt="Gestion des réservations">
                                    <h5 class="card-title mt-3">Gestion des réservations</h5>
                                    <p class="card-text text-muted">Gérez les réservations de salles et de matériel. Validez les demandes en attente.</p>
                                    <a href="gestion_reservations_agent.php" class="btn btn-custom w-100">Gérer</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <img src="/img/tools.png" class="card-img-top" alt="Gestion du matériel">
                                    <h5 class="card-title mt-3">Gestion du matériel</h5>
                                    <p class="card-text text-muted">Suivez l'état du matériel, gérez les maintenances et les disponibilités.</p>
                                    <a href="gestion_materiel_agent.php" class="btn btn-custom w-100">Gérer</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <img src="/img/building.png" class="card-img-top" alt="Gestion des salles">
                                    <h5 class="card-title mt-3">Gestion des salles</h5>
                                    <p class="card-text text-muted">Gérez la disponibilité des salles, leur état et leur équipement.</p>
                                    <a href="gestion_salles_agent.php" class="btn btn-custom w-100">Gérer</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <img src="/img/monitoring.png" class="card-img-top" alt="Suivi des équipements">
                                    <h5 class="card-title mt-3">Suivi des équipements</h5>
                                    <p class="card-text text-muted">Surveillez l'état des équipements et planifiez les maintenances.</p>
                                    <a href="suivi_equipements.php" class="btn btn-custom w-100">Suivre</a>
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