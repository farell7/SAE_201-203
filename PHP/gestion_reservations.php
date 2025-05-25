<?php
require_once 'check_session.php';
require_once 'connexion.php';

// Récupérer la liste des réservations avec les informations des utilisateurs et des salles
$query = "SELECT r.*, u.nom as user_nom, u.prenom as user_prenom, s.nom as salle_nom 
          FROM reservation r 
          LEFT JOIN utilisateur u ON r.utilisateur_id = u.id 
          LEFT JOIN salle s ON r.salle_id = s.id 
          ORDER BY r.date_debut DESC";
$stmt = $connexion->query($query);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des salles pour le formulaire
$stmt = $connexion->query("SELECT * FROM salle ORDER BY nom");
$salles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des utilisateurs pour le formulaire
$stmt = $connexion->query("SELECT * FROM utilisateur ORDER BY nom, prenom");
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations - ResaUGE</title>
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

        .container { padding: 20px; }
        .add-button { margin-bottom: 20px; }
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
                                                <?php foreach ($reservations as $reservation): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($reservation['salle_nom']); ?></td>
                                                    <td><?php echo htmlspecialchars($reservation['user_prenom'] . ' ' . $reservation['user_nom']); ?></td>
                                                    <td><?php echo htmlspecialchars($reservation['date_debut']); ?></td>
                                                    <td><?php echo htmlspecialchars($reservation['date_fin']); ?></td>
                                                    <td>
                                                        <?php if ($reservation['statut'] == 'en_attente'): ?>
                                                        <span class="badge badge-pending">En attente</span>
                                                        <?php elseif ($reservation['statut'] == 'approuve'): ?>
                                                        <span class="badge badge-confirmed">Confirmée</span>
                                                        <?php else: ?>
                                                        <span class="badge badge-cancelled">Refusée</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-secondary me-2">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <?php if ($reservation['statut'] == 'en_attente'): ?>
                                                        <button class="btn btn-sm btn-outline-success me-2">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
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

                    <button class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                        <i class="bi bi-plus-circle"></i> Ajouter une réservation
                    </button>
                </div>
            </main>
        </div>
    </div>

    <footer class="footer fixed-bottom py-3">
        <div class="container text-center">
            <span>&copy; 2025 Université Eiffel. Tous droits réservés.</span>
        </div>
    </footer>

    <!-- Modal Ajout Réservation -->
    <div class="modal fade" id="addReservationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addReservationForm" action="add_reservation.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Utilisateur</label>
                            <select class="form-control" name="utilisateur_id" required>
                                <?php foreach ($utilisateurs as $utilisateur): ?>
                                <option value="<?php echo $utilisateur['id']; ?>">
                                    <?php echo htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Salle</label>
                            <select class="form-control" name="salle_id" required>
                                <?php foreach ($salles as $salle): ?>
                                <option value="<?php echo $salle['id']; ?>">
                                    <?php echo htmlspecialchars($salle['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date et heure de début</label>
                            <input type="datetime-local" class="form-control" name="date_debut" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date et heure de fin</label>
                            <input type="datetime-local" class="form-control" name="date_fin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-control" name="statut" required>
                                <option value="en_attente">En attente</option>
                                <option value="approuve">Approuvé</option>
                                <option value="refuse">Refusé</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="addReservationForm" class="btn btn-primary">Ajouter</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editReservation(id) {
            // À implémenter
            alert('Modification de la réservation ' + id);
        }

        function deleteReservation(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')) {
                window.location.href = 'delete_reservation.php?id=' + id;
            }
        }

        function approveReservation(id) {
            if (confirm('Voulez-vous approuver cette réservation ?')) {
                window.location.href = 'update_reservation_status.php?id=' + id + '&status=approuve';
            }
        }

        function rejectReservation(id) {
            if (confirm('Voulez-vous refuser cette réservation ?')) {
                window.location.href = 'update_reservation_status.php?id=' + id + '&status=refuse';
            }
        }
    </script>
</body>
</html> 