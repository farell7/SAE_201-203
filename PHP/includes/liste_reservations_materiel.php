<div class="table-container reservations-list">
    <h2>Réservations en attente</h2>
    <div class="table-wrapper">
        <table class="gestion-table compact-table">
            <thead>
                <tr>
                    <th>Matériel</th>
                    <th>Type</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Aucune réservation en attente</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['materiel_nom']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['materiel_type']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                    <input type="text" name="signature" placeholder="Signature" required>
                                    <input type="text" name="commentaire" placeholder="Commentaire">
                                    <button type="submit" name="valider">Valider</button>
                                    <button type="submit" name="refuser">Refuser</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal pour modification des dates -->
<div id="modal-dates" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Modifier les dates</h2>
        <form method="POST" id="form-modifier-dates">
            <input type="hidden" name="reservation_id" id="modal-reservation-id">
            <div class="form-group">
                <label>Date de début</label>
                <input type="datetime-local" name="date_debut" required>
            </div>
            <div class="form-group">
                <label>Date de fin</label>
                <input type="datetime-local" name="date_fin" required>
            </div>
            <button type="submit" name="modifier_date" class="btn btn-modifier">Modifier</button>
        </form>
    </div>
</div> 