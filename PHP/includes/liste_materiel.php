<div class="table-container">
    <h2>Matériel disponible</h2>
    <table class="gestion-table">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Nom</th>
                <th>Type</th>
                <th>N° Série</th>
                <th>État</th>
                <th>Disponible</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materiels as $materiel): ?>
            <tr>
                <td>
                    <?php if ($materiel['photo']): ?>
                        <img src="../<?php echo htmlspecialchars($materiel['photo']); ?>" alt="Photo du matériel" class="materiel-photo">
                    <?php else: ?>
                        <div class="no-photo">Pas de photo</div>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($materiel['nom']); ?></td>
                <td><?php echo htmlspecialchars($materiel['type']); ?></td>
                <td><?php echo htmlspecialchars($materiel['numero_serie']); ?></td>
                <td class="etat-<?php echo strtolower($materiel['etat']); ?>"><?php echo htmlspecialchars($materiel['etat']); ?></td>
                <td><?php echo $materiel['disponible'] ? 'Oui' : 'Non'; ?></td>
                <td class="actions">
                    <button type="button" class="btn btn-modifier" onclick="modifierMateriel(<?php echo htmlspecialchars(json_encode($materiel)); ?>)">Modifier</button>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce matériel ?');">
                        <input type="hidden" name="materiel_id" value="<?php echo $materiel['id']; ?>">
                        <button type="submit" name="supprimer" class="btn btn-supprimer">Supprimer</button>
                    </form>
                    <button type="button" class="btn btn-reserver" onclick="nouvelleReservation(<?php echo $materiel['id']; ?>)">Réserver</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal pour modification du matériel -->
<div id="modal-materiel" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Modifier le matériel</h2>
        <form method="POST" id="form-modifier-materiel" enctype="multipart/form-data">
            <input type="hidden" name="materiel_id" id="modal-materiel-id">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" id="modal-materiel-nom" required>
            </div>
            <div class="form-group">
                <label>Type</label>
                <input type="text" name="type" id="modal-materiel-type" required>
            </div>
            <div class="form-group">
                <label>Numéro de série</label>
                <input type="text" name="numero_serie" id="modal-materiel-serie">
            </div>
            <div class="form-group">
                <label>État</label>
                <select name="etat" id="modal-materiel-etat" required>
                    <option value="bon">Bon état</option>
                    <option value="moyen">État moyen</option>
                    <option value="mauvais">Mauvais état</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="modal-materiel-description"></textarea>
            </div>
            <div class="form-group">
                <label>Photo du matériel</label>
                <input type="file" name="photo" accept="image/*" class="file-input">
                <div id="modal-materiel-photo-preview" class="photo-preview"></div>
            </div>
            <div class="checkbox-line">
                <label class="custom-checkbox">
                    <input type="checkbox" name="disponible" id="modal-materiel-disponible">
                    <span class="checkmark"></span>
                </label>
                <span>Disponible</span>
            </div>
            <button type="submit" name="modifier" class="btn btn-modifier">Modifier</button>
        </form>
    </div>
</div>

<!-- Modal pour nouvelle réservation -->
<div id="modal-reservation" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Nouvelle réservation</h2>
        <form method="POST" id="form-nouvelle-reservation">
            <input type="hidden" name="materiel_id" id="modal-reservation-materiel-id">
            <div class="form-group">
                <label>Date de début</label>
                <input type="datetime-local" name="date_debut" required>
            </div>
            <div class="form-group">
                <label>Date de fin</label>
                <input type="datetime-local" name="date_fin" required>
            </div>
            <button type="submit" name="reserver" class="btn btn-reserver">Réserver</button>
        </form>
    </div>
</div> 