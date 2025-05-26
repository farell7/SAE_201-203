<?php
require_once 'check_session.php';
$user = $_SESSION['utilisateur'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des Activités - ResaUGE</title>
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

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 0.8rem 1.5rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--light-color);
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link i {
            transition: transform 0.2s ease;
        }

        .nav-link:hover i {
            transform: translateX(3px);
        }

        .main-content {
            flex: 1;
            padding: 2rem;
        }

        .card {
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-success { background-color: #28a745; }
        .status-warning { background-color: #ffc107; }
        .status-danger { background-color: #dc3545; }

        .footer {
            background-color: #2f2a85;
            color: #ffffff;
        }

        .chart-container {
            min-height: 300px;
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
                        <a class="nav-link d-flex align-items-center" href="admin.php">
                            <i class="bi bi-house-door me-2"></i>Accueil
                        </a>
                        <a class="nav-link d-flex align-items-center" href="validation_compte.php">
                            <i class="bi bi-person-check me-2"></i>Validation des utilisateurs
                        </a>
                        <a class="nav-link d-flex align-items-center" href="gestion_materiel.php">
                            <i class="bi bi-tools me-2"></i>Gestion du matériel
                        </a>
                        <a class="nav-link d-flex align-items-center" href="gestion_salle.php">
                            <i class="bi bi-building me-2"></i>Gestion des salles
                        </a>
                        <a class="nav-link d-flex align-items-center" href="suivi_reservations.php">
                            <i class="bi bi-calendar-check me-2"></i>Suivi des réservations
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
                    <h1 class="display-5 fw-bold mb-2">Suivi des Activités</h1>
                    <p class="text-muted fs-4 mb-5">Surveillance en temps réel</p>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Activités Récentes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <span class="status-dot status-success"></span>
                                        <strong>Réservation confirmée - Salle A101</strong>
                                        <small class="text-muted d-block">Il y a 5 minutes</small>
                                    </div>
                                    <div class="mb-3">
                                        <span class="status-dot status-warning"></span>
                                        <strong>Maintenance programmée - Amphi B</strong>
                                        <small class="text-muted d-block">Il y a 15 minutes</small>
                                    </div>
                                    <div class="mb-3">
                                        <span class="status-dot status-danger"></span>
                                        <strong>Problème technique signalé - Salle C203</strong>
                                        <small class="text-muted d-block">Il y a 30 minutes</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">État des Équipements</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <span class="status-dot status-success"></span>
                                        <strong>Projecteurs</strong>
                                        <small class="text-muted d-block">15/15 opérationnels</small>
                                    </div>
                                    <div class="mb-3">
                                        <span class="status-dot status-warning"></span>
                                        <strong>Ordinateurs</strong>
                                        <small class="text-muted d-block">28/30 opérationnels</small>
                                    </div>
                                    <div class="mb-3">
                                        <span class="status-dot status-success"></span>
                                        <strong>Systèmes audio</strong>
                                        <small class="text-muted d-block">10/10 opérationnels</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Alertes Système</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <span class="status-dot status-danger"></span>
                                        <strong>Maintenance requise - Projecteur Salle D102</strong>
                                        <small class="text-muted d-block">Priorité haute</small>
                                    </div>
                                    <div class="mb-3">
                                        <span class="status-dot status-warning"></span>
                                        <strong>Mise à jour système nécessaire</strong>
                                        <small class="text-muted d-block">Priorité moyenne</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Analyse des Tendances</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <!-- Ici vous pourrez ajouter un graphique pour les tendances -->
                                    </div>
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