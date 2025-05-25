<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Étudiant';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Étudiant - Université Eiffel</title>
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

        .card-img-top {
            width: 65px;
            height: 65px;
            margin: 1rem auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="d-flex flex-column h-100">
                    <h2 class="text-white text-center py-4 mb-4">Espace Étudiant</h2>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#"><i class="bi bi-house-door me-2"></i>Accueil</a>
                        <a class="nav-link" href="#"><i class="bi bi-calendar-check me-2"></i>Mes réservations</a>
                        <a class="nav-link" href="#"><i class="bi bi-door-open me-2"></i>Réserver une salle</a>
                        <a class="nav-link" href="#"><i class="bi bi-tools me-2"></i>Emprunter du matériel</a>
                        <a class="nav-link" href="#"><i class="bi bi-person me-2"></i>Mon profil</a>
                        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a>
                    </nav>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-4 py-4">
                <div class="container">
                    <h1 class="display-5 fw-bold mb-2">Bonjour, <?php echo $username; ?> !</h1>
                    <p class="text-primary fs-4 mb-5">Espace Étudiant</p>

                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/img/emplacement.png" class="card-img-top" alt="Réserver une salle">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">Réserver une salle</h5>
                                    <p class="card-text">Réservez une salle d'étude, de travail en groupe ou un laboratoire pour vos projets.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Réserver</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/img/cadenas-verrouille.png" class="card-img-top" alt="Emprunter du matériel">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">Emprunter du matériel</h5>
                                    <p class="card-text">Accédez au catalogue de matériel disponible et effectuez vos demandes d'emprunt.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Emprunter</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/img/nom.png" class="card-img-top" alt="Mes réservations">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">Mes réservations</h5>
                                    <p class="card-text">Consultez l'historique de vos réservations et gérez vos réservations en cours.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Voir</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/img/profil.png" class="card-img-top" alt="Mon profil">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">Mon profil</h5>
                                    <p class="card-text">Consultez et modifiez vos informations personnelles et vos préférences.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Modifier</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
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