<?php
session_start();
// Supposons que le nom d'utilisateur est stocké dans $_SESSION['username']
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Agent';
?>
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
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #2f2a85;
            min-height: 100vh;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 0.8rem 1.5rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #ffffff;
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
            background-color: #ffffff;
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
            color: #2f2a85;
        }

        .btn-custom {
            background-color: #2f2a85;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #27236e;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .footer {
            background-color: #2f2a85;
            color: #ffffff;
        }
    </style>
    <title>Espace Agent - Université Eiffel</title>
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
        
        .sidebar {
            background: var(--primary-color);
            min-height: 100vh;
        }
        
        .text-custom-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-custom-primary {
            background-color: var(--primary-color) !important;
        }
        
        .btn-custom-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--light-color);
        }
        
        .btn-custom-primary:hover {
            background-color: #27236e;
            border-color: #27236e;
            color: var(--light-color);
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            background: var(--light-color);
        }
        
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 24px rgba(47, 42, 133, 0.15);
        }

        .card-title {
            color: var(--primary-color);
        }

        .card-text {
            color: var(--dark-color);
        }

        .footer {
            position: fixed;
            bottom: -100px;
            width: 100%;
            transition: bottom 0.3s ease-in-out;
            z-index: 1000;
            background-color: var(--primary-color);
        }

        .footer.visible {
            bottom: 0;
        }

        .nav-link {
            color: var(--light-color) !important;
            opacity: 0.9;
        }

        .nav-link:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2) !important;
            opacity: 1;
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
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="d-flex flex-column h-100">
                    <h2 class="text-white text-center py-4 mb-4">Espace Agent</h2>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#"><i class="bi bi-house-door me-2"></i>Accueil</a>
                        <a class="nav-link" href="#"><i class="bi bi-tools me-2"></i>Maintenance</a>
                        <a class="nav-link" href="#"><i class="bi bi-box-seam me-2"></i>Gestion du matériel</a>
                        <a class="nav-link" href="#"><i class="bi bi-calendar3 me-2"></i>Planning des salles</a>
                        <a class="nav-link" href="#"><i class="bi bi-file-text me-2"></i>Rapports</a>
                        <a class="nav-link" href="#"><i class="bi bi-person me-2"></i>Mon profil</a>
                        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a>
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

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-4 py-4">
                <div class="container">
                    <h1 class="display-5 fw-bold mb-2">Bonjour, <?php echo $username; ?> !</h1>
                    <p class="text-success fs-4 mb-5">Espace Agent</p>

                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/img/emplacement.png" class="card-img-top" alt="Maintenance">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Maintenance</h5>
                                    <p class="card-text">Gérez les interventions de maintenance et suivez l'état des équipements et des salles.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Accéder</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/img/cadenas-verrouille.png" class="card-img-top" alt="Gestion du matériel">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Gestion du matériel</h5>
                                    <p class="card-text">Suivez l'inventaire, les emprunts et retours de matériel, et planifiez la maintenance.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Gérer</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/img/nom.png" class="card-img-top" alt="Planning des salles">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Planning des salles</h5>
                                    <p class="card-text">Consultez l'occupation des salles et gérez leur disponibilité pour la maintenance.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Voir</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/img/profil.png" class="card-img-top" alt="Rapports">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Rapports</h5>
                                    <p class="card-text">Générez des rapports sur l'état du matériel et l'utilisation des salles.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Générer</a>
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
    <footer class="footer py-3 bg-custom-primary text-white" id="footer">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col">
                    &copy; 2025 Université Eiffel - Tous droits réservés
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Improved footer scroll behavior
        let lastScrollTop = 0;
        const footer = document.getElementById('footer');
        
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            let windowHeight = window.innerHeight;
            let documentHeight = document.documentElement.scrollHeight;
            
            // Show footer when near bottom
            if (scrollTop + windowHeight >= documentHeight - 50) {
                footer.classList.add('visible');
            } else {
                footer.classList.remove('visible');
            }
            
            lastScrollTop = scrollTop;
        });
    </script>
</body>
</html>