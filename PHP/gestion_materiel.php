<?php include 'gm.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Matériel - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/gestion_materiel.css">
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="admin.php">Tableau de bord</a>
            <a href="gestion_materiel.php" class="active">Gestions</a>
            <a href="suivi_reservations.php">Suivi</a>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <div class="menu-icon">☰</div>
        </div>
    </nav>

    <main class="main-content">
        <h1>Gestion du Matériel</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout/modification de matériel -->
        <div class="form-container">
            <h2>Ajouter du matériel</h2>
            <form method="POST" class="form-gestion" enctype="multipart/form-data">
                <input type="text" name="nom" placeholder="Nom du matériel" required>
                <input type="text" name="type" placeholder="Type de matériel (ex: PC, Casque VR, Caméra, etc.)" required>
                <input type="text" name="numero_serie" placeholder="Numéro de série">
                <select name="etat" required>
                    <option value="bon">Bon état</option>
                    <option value="moyen">État moyen</option>
                    <option value="mauvais">Mauvais état</option>
                </select>
                <textarea name="description" placeholder="Description"></textarea>
                
                <div class="form-group">
                    <label>Photo du matériel</label>
                    <input type="file" name="photo" accept="image/*" class="file-input">
                </div>
                
                <div class="checkbox-line">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="disponible" checked>
                        <span class="checkmark"></span>
                    </label>
                    <span>Disponible</span>
                </div>

                <button type="submit" name="ajouter" class="btn btn-ajouter">Ajouter</button>
            </form>
        </div>

        <!-- Liste des réservations -->
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

        <!-- Liste du matériel -->
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
    </main>

    <footer class="footer">
        &copy;2025 Université Eiffel. Tous droits réservés.
    </footer>

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

    <script>
    // Fonctions pour gérer les modals
    function modifierDates(reservationId) {
        document.getElementById('modal-reservation-id').value = reservationId;
        document.getElementById('modal-dates').style.display = 'block';
    }

    function modifierMateriel(materiel) {
        document.getElementById('modal-materiel-id').value = materiel.id;
        document.getElementById('modal-materiel-nom').value = materiel.nom;
        document.getElementById('modal-materiel-type').value = materiel.type;
        document.getElementById('modal-materiel-serie').value = materiel.numero_serie;
        document.getElementById('modal-materiel-etat').value = materiel.etat;
        document.getElementById('modal-materiel-description').value = materiel.description;
        document.getElementById('modal-materiel-disponible').checked = materiel.disponible == 1;
        
        // Afficher la photo existante
        const photoPreview = document.getElementById('modal-materiel-photo-preview');
        if (materiel.photo) {
            photoPreview.innerHTML = `<img src="../${materiel.photo}" alt="Photo actuelle" class="preview-image">`;
        } else {
            photoPreview.innerHTML = '<div class="no-photo">Pas de photo</div>';
        }
        
        document.getElementById('modal-materiel').style.display = 'block';
    }

    function nouvelleReservation(materielId) {
        document.getElementById('modal-reservation-materiel-id').value = materielId;
        document.getElementById('modal-reservation').style.display = 'block';
    }

    // Fermeture des modals
    document.querySelectorAll('.close').forEach(function(close) {
        close.onclick = function() {
            this.closest('.modal').style.display = 'none';
        }
    });

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
    </script>
</body>
</html>
