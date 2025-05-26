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
                        <select class="form-control" name="user_id" required>
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