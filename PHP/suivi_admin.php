<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi - Admin</title>
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

        .activity-item {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item:hover {
            background-color: rgba(47, 42, 133, 0.05);
            padding-left: 1rem;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-success {
            background-color: #35c8b0;
        }

        .status-warning {
            background-color: #ffc107;
        }

        .status-error {
            background-color: #ca3120;
        }

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
                        <a class="nav-link d-flex align-items-center" href="dashboard_admin.php">
                            <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                        </a>
                        <a class="nav-link d-flex align-items-center" href="validation_compte.php">
                            <i class="bi bi-person-check me-2"></i>Validation des utilisateurs
                        </a>
                        <a class="nav-link d-flex align-items-center" href="gestion_utilisateurs.php">
                            <i class="bi bi-people me-2"></i>Utilisateurs
                        </a>
                        <a class="nav-link d-flex align-items-center active" href="suivi_admin.php">
                            <i class="bi bi-graph-up me-2"></i>Suivi
                        </a>
                        <a class="nav-link d-flex align-items-center" href="gestion_objets_salles.php">
                            <i class="bi bi-building me-2"></i>Objets & Salles
                        </a>
                        <a class="nav-link d-flex align-items-center" href="gestion_reservations.php">
                            <i class="bi bi-calendar-check me-2"></i>Gestion des réservations
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
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="bi bi-clock-history me-2"></i>Activités Récentes
                                    </h5>
                                    <div class="activity-list">
                                        <div class="activity-item d-flex align-items-center">
                                            <span class="status-indicator status-success me-3"></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Réservation confirmée - Salle A101</div>
                                                <small class="text-muted">Il y a 5 minutes</small>
                                            </div>
                                        </div>
                                        <div class="activity-item d-flex align-items-center">
                                            <span class="status-indicator status-warning me-3"></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Maintenance programmée - Amphi B</div>
                                                <small class="text-muted">Il y a 15 minutes</small>
                                            </div>
                                        </div>
                                        <div class="activity-item d-flex align-items-center">
                                            <span class="status-indicator status-error me-3"></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Problème technique signalé - Salle C203</div>
                                                <small class="text-muted">Il y a 30 minutes</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="bi bi-tools me-2"></i>État des Équipements
                                    </h5>
                                    <div class="activity-list">
                                        <div class="activity-item d-flex align-items-center">
                                            <span class="status-indicator status-success me-3"></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Projecteurs</div>
                                                <small class="text-muted">15/15 opérationnels</small>
                                            </div>
                                        </div>
                                        <div class="activity-item d-flex align-items-center">
                                            <span class="status-indicator status-warning me-3"></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Ordinateurs</div>
                                                <small class="text-muted">28/30 opérationnels</small>
                                            </div>
                                        </div>
                                        <div class="activity-item d-flex align-items-center">
                                            <span class="status-indicator status-success me-3"></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Systèmes audio</div>
                                                <small class="text-muted">10/10 opérationnels</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="bi bi-exclamation-triangle me-2"></i>Alertes Système
                                    </h5>
                                    <div class="activity-list">
                                        <div class="activity-item d-flex align-items-center">
                                            <span class="status-indicator status-error me-3"></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Maintenance requise - Projecteur Salle D102</div>
                                                <small class="text-muted">Priorité haute</small>
                                            </div>
                                        </div>
                                        <div class="activity-item d-flex align-items-center">
                                            <span class="status-indicator status-warning me-3"></span>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">Mise à jour système nécessaire</div>
                                                <small class="text-muted">Priorité moyenne</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="bi bi-graph-up me-2"></i>Analyse des Tendances
                                    </h5>
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