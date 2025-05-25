<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations - Admin</title>
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

        .table {
            background-color: #ffffff;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table thead th {
            background-color: #2f2a85;
            color: #ffffff;
            font-weight: 600;
            border: none;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(47, 42, 133, 0.05);
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

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-confirmed {
            background-color: #35c8b0;
            color: #fff;
        }

        .badge-cancelled {
            background-color: #ca3120;
            color: #fff;
        }

        .calendar-container {
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #e9ecef;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .calendar-day:hover {
            background-color: rgba(47, 42, 133, 0.05);
            border-color: #2f2a85;
        }

        .calendar-day.active {
            background-color: #2f2a85;
            color: #ffffff;
            border-color: #2f2a85;
        }

        .reservation-count {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .active .reservation-count {
            color: rgba(255, 255, 255, 0.8);
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
                        <a class="nav-link d-flex align-items-center" href="suivi_admin.php">
                            <i class="bi bi-graph-up me-2"></i>Suivi
                        </a>
                        <a class="nav-link d-flex align-items-center" href="gestion_objets_salles.php">
                            <i class="bi bi-building me-2"></i>Objets & Salles
                        </a>
                        <a class="nav-link d-flex align-items-center active" href="gestion_reservations.php">
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
                    <h1 class="display-5 fw-bold mb-2">Gestion des Réservations</h1>
                    <p class="text-muted fs-4 mb-5">Vue d'ensemble des réservations</p>

                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-calendar3 me-2"></i>Calendrier des réservations
                                        </h5>
                                        <div class="btn-group">
                                            <button class="btn btn-outline-secondary">
                                                <i class="bi bi-chevron-left"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary">
                                                Mars 2024
                                            </button>
                                            <button class="btn btn-outline-secondary">
                                                <i class="bi bi-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="calendar-grid mb-3">
                                        <div class="text-center fw-bold">Lun</div>
                                        <div class="text-center fw-bold">Mar</div>
                                        <div class="text-center fw-bold">Mer</div>
                                        <div class="text-center fw-bold">Jeu</div>
                                        <div class="text-center fw-bold">Ven</div>
                                        <div class="text-center fw-bold">Sam</div>
                                        <div class="text-center fw-bold">Dim</div>

                                        <!-- Exemple de jours -->
                                        <div class="calendar-day">
                                            <span>1</span>
                                            <span class="reservation-count">3</span>
                                        </div>
                                        <div class="calendar-day active">
                                            <span>2</span>
                                            <span class="reservation-count">5</span>
                                        </div>
                                        <!-- Ajoutez les autres jours ici -->
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-list-ul me-2"></i>Liste des réservations
                                        </h5>
                                        <div class="input-group w-50">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0 ps-0" placeholder="Rechercher une réservation...">
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Salle</th>
                                                    <th>Utilisateur</th>
                                                    <th>Date</th>
                                                    <th>Horaire</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Salle A101</td>
                                                    <td>Pierre Martin</td>
                                                    <td>15/03/2024</td>
                                                    <td>14:00 - 16:00</td>
                                                    <td>
                                                        <span class="badge badge-confirmed">Confirmée</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-secondary me-2">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Amphi B</td>
                                                    <td>Sophie Bernard</td>
                                                    <td>15/03/2024</td>
                                                    <td>09:00 - 12:00</td>
                                                    <td>
                                                        <span class="badge badge-pending">En attente</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-secondary me-2">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success me-2">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <div class="text-muted">
                                            Affichage de <strong>1-2</strong> sur <strong>15</strong> réservations
                                        </div>
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination mb-0">
                                                <li class="page-item disabled">
                                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Précédent</a>
                                                </li>
                                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                <li class="page-item">
                                                    <a class="page-link" href="#">Suivant</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="bi bi-graph-up me-2"></i>Statistiques
                                    </h5>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>Réservations aujourd'hui</div>
                                        <div class="h4 mb-0">8</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>En attente</div>
                                        <div class="h4 mb-0">3</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>Taux d'occupation</div>
                                        <div class="h4 mb-0">75%</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <i class="bi bi-bell me-2"></i>Notifications
                                    </h5>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-exclamation-circle text-warning fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="fw-bold">Conflit de réservation</div>
                                            <small class="text-muted">Salle C203, 16/03/2024</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-info-circle text-info fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="fw-bold">Maintenance prévue</div>
                                            <small class="text-muted">Amphi A, 18/03/2024</small>
                                        </div>
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