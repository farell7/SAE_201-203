<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Université Eiffel</title>
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
            background: var(--gray-color);
            min-height: 100vh;
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

        .nav-link {
            color: var(--light-color) !important;
            opacity: 0.9;
            padding: 0.8rem 1.5rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .nav-link i {
            transition: transform 0.2s ease;
        }

        .nav-link:hover i {
            transform: translateX(3px);
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

        .admin-card img {
            width: 65px;
            height: 65px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="d-flex flex-column h-100">
                    <h2 class="text-white text-center py-4 mb-4">Admin</h2>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="#"><i class="bi bi-house-door me-2"></i>Accueil</a>
                        <a class="nav-link" href="#"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord</a>
                        <a class="nav-link" href="validation_compte.php"><i class="bi bi-person-check me-2"></i>Validation des utilisateurs</a>
                        <a class="nav-link" href="#"><i class="bi bi-people me-2"></i>Utilisateurs</a>
                        <a class="nav-link" href="#"><i class="bi bi-graph-up me-2"></i>Suivi</a>
                        <a class="nav-link" href="#"><i class="bi bi-box-seam me-2"></i>Objets & Salles</a>
                        <a class="nav-link" href="gestion_reservations.php"><i class="bi bi-calendar-check me-2"></i>Gestion des réservations</a>
                        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a>
                    </nav>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-4 py-4">
                <div class="container">
                    <h1 class="display-5 fw-bold mb-2">Bonjour, <?php echo $username; ?> !</h1>
                    <p class="text-custom-primary fs-4 mb-5">Espace d'administration</p>

                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 admin-card">
                                <div class="card-body text-center">
                                    <img src="/img/emplacement.png" alt="Gestion des salles" class="mb-3">
                                    <h3 class="card-title h5">Gestion des salles</h3>
                                    <p class="card-text">Ajoutez, modifiez ou supprimez les salles de l'université. Consultez leur disponibilité en temps réel.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Gérer</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 admin-card">
                                <div class="card-body text-center">
                                    <img src="/img/cadenas-verrouille.png" alt="Gestion du matériel" class="mb-3">
                                    <h3 class="card-title h5">Gestion du matériel</h3>
                                    <p class="card-text">Gérez le matériel disponible, attribuez-le aux salles ou utilisateurs, et suivez l'état des équipements.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Gérer</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 admin-card">
                                <div class="card-body text-center">
                                    <img src="/img/profil.png" alt="Gestion des utilisateurs" class="mb-3">
                                    <h3 class="card-title h5">Gestion des utilisateurs</h3>
                                    <p class="card-text">Créez, modifiez ou supprimez des comptes utilisateurs. Attribuez des rôles et surveillez l'activité.</p>
                                    <a href="#" class="btn btn-custom-primary mt-2">Gérer</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 admin-card">
                                <div class="card-body text-center">
                                    <img src="/img/nom.png" alt="Suivi des réservations" class="mb-3">
                                    <h3 class="card-title h5">Suivi des réservations</h3>
                                    <p class="card-text">Visualisez et gérez toutes les réservations en cours ou passées. Exportez les rapports facilement.</p>
                                    <a href="gestion_reservations.php" class="btn btn-custom-primary mt-2">Gérer</a>
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
                    &copy; 2025 Université Eiffel. Tous droits réservés.
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


