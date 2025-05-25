<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Admin</title>
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

        .btn-edit {
            background-color: #35c8b0;
            color: #ffffff;
        }

        .btn-edit:hover {
            background-color: #2ca892;
            color: #ffffff;
        }

        .btn-delete {
            background-color: #ca3120;
            color: #ffffff;
        }

        .btn-delete:hover {
            background-color: #a82718;
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
                        <a class="nav-link d-flex align-items-center active" href="gestion_utilisateurs.php">
                            <i class="bi bi-people me-2"></i>Utilisateurs
                        </a>
                        <a class="nav-link d-flex align-items-center" href="suivi_admin.php">
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
                    <h1 class="display-5 fw-bold mb-2">Gestion des Utilisateurs</h1>
                    <p class="text-muted fs-4 mb-5">Administration des comptes utilisateurs</p>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row align-items-center mb-4">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Rechercher un utilisateur...">
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <button class="btn btn-custom">
                                        <i class="bi bi-plus-lg me-2"></i>Ajouter un utilisateur
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Rôle</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Jean Dupont</td>
                                            <td>jean.dupont@example.com</td>
                                            <td>
                                                <span class="badge bg-primary">Enseignant</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Actif</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-edit btn-sm me-2">
                                                    <i class="bi bi-pencil-square me-1"></i>Modifier
                                                </button>
                                                <button class="btn btn-delete btn-sm">
                                                    <i class="bi bi-trash me-1"></i>Supprimer
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Marie Martin</td>
                                            <td>marie.martin@example.com</td>
                                            <td>
                                                <span class="badge bg-info">Étudiant</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Actif</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-edit btn-sm me-2">
                                                    <i class="bi bi-pencil-square me-1"></i>Modifier
                                                </button>
                                                <button class="btn btn-delete btn-sm">
                                                    <i class="bi bi-trash me-1"></i>Supprimer
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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