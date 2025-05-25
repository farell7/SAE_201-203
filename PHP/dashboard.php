<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit();
}

$user = $_SESSION['utilisateur'];
$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - <?php echo ucfirst($role); ?></title>
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
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(47, 42, 133, 0.15);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2f2a85;
        }

        .footer {
            background-color: #2f2a85;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="d-flex flex-column h-100">
                    <h2 class="text-white text-center py-4 mb-4"><?php echo ucfirst($role); ?></h2>
                    <nav class="nav flex-column">
                        <?php if ($role === 'administrateur'): ?>
                            <a class="nav-link d-flex align-items-center" href="admin.php">
                                <i class="bi bi-house-door me-2"></i>Accueil
                            </a>
                            <a class="nav-link d-flex align-items-center" href="validation_compte.php">
                                <i class="bi bi-person-check me-2"></i>Validation des utilisateurs
                            </a>
                            <a class="nav-link d-flex align-items-center" href="gestion_utilisateurs.php">
                                <i class="bi bi-people me-2"></i>Utilisateurs
                            </a>
                            <a class="nav-link d-flex align-items-center" href="suivi_admin.php">
                                <i class="bi bi-graph-up me-2"></i>Suivi
                            </a>
                            <a class="nav-link d-flex align-items-center" href="gestion_objets_salles.php">
                                <i class="bi bi-building me-2"></i>Objets & Salles
                            </a>
                        <?php endif; ?>

                        <?php if (in_array($role, ['etudiant', 'enseignant'])): ?>
                            <a class="nav-link d-flex align-items-center" href="reservation_salle.php">
                                <i class="bi bi-door-open me-2"></i>Réserver une salle
                            </a>
                        <?php endif; ?>

                        <?php if ($role === 'etudiant'): ?>
                            <a class="nav-link d-flex align-items-center" href="reservation_materiel.php">
                                <i class="bi bi-tools me-2"></i>Réserver du matériel
                            </a>
                        <?php endif; ?>

                        <?php if (in_array($role, ['administrateur', 'agent'])): ?>
                            <a class="nav-link d-flex align-items-center" href="gestion_reservations.php">
                                <i class="bi bi-calendar-check me-2"></i>Gestion des réservations
                            </a>
                        <?php endif; ?>

                        <a class="nav-link d-flex align-items-center" href="suivi_reservations.php">
                            <i class="bi bi-list-check me-2"></i>Mes réservations
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
                    <h1 class="display-5 fw-bold mb-2">Bienvenue, <?php echo htmlspecialchars($user['prenom']); ?></h1>
                    <p class="text-muted fs-4 mb-5">Tableau de bord <?php echo ucfirst($role); ?></p>

                    <div class="row g-4">
                        <?php if (in_array($role, ['etudiant', 'enseignant'])): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="bi bi-door-open me-2"></i>Réserver une salle
                                        </h5>
                                        <p class="card-text">Réservez une salle pour vos cours ou travaux de groupe.</p>
                                        <a href="reservation_salle.php" class="btn btn-primary">Réserver</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($role === 'etudiant'): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="bi bi-tools me-2"></i>Réserver du matériel
                                        </h5>
                                        <p class="card-text">Accédez au matériel disponible pour vos projets.</p>
                                        <a href="reservation_materiel.php" class="btn btn-primary">Réserver</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-list-check me-2"></i>Mes réservations
                                    </h5>
                                    <p class="card-text">Consultez et gérez vos réservations en cours.</p>
                                    <a href="suivi_reservations.php" class="btn btn-primary">Voir mes réservations</a>
                                </div>
                            </div>
                        </div>

                        <?php if (in_array($role, ['administrateur', 'agent'])): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="bi bi-calendar-check me-2"></i>Gestion des réservations
                                        </h5>
                                        <p class="card-text">Gérez toutes les réservations des utilisateurs.</p>
                                        <a href="gestion_reservations.php" class="btn btn-primary">Gérer</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
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

