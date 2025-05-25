<?php include 'gs.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Salles - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/gestion_salle.css">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.10/main.min.css' rel='stylesheet' />
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.10/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.10/main.min.js'></script>
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="#">Tableau de bord</a>
            <a href="#" class="active">Gestions</a>
            <a href="#">Suivi</a>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <div class="menu-icon">☰</div>
        </div>
    </nav>

    <main class="main-content">
        <h1>Gestion des Salles</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout de salle -->
        <div class="form-container">
            <h2>Ajouter une nouvelle salle</h2>
            <form method="POST" class="form-gestion" enctype="multipart/form-data">
                <input type="text" name="nom" placeholder="Nom de la salle" required>
                <input type="number" name="capacite" placeholder="Capacité" required>
                <textarea name="description" placeholder="Description"></textarea>
                <input type="file" name="photo" accept="image/*">
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
        <div class="table-container">
            <h2>Réservations en attente</h2>
            <table class="gestion-table">
                <thead>
                    <tr>
                        <th>Salle</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Statut</th>
                        <th>Actions</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reservation['salle_nom']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                        <td><?php echo htmlspecialchars($reservation['statut']); ?></td>
                        <td class="actions">
                            <?php if ($reservation['statut'] === 'en_attente'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                <textarea name="commentaire" placeholder="Commentaire" class="commentaire-input"></textarea>
                                <button type="submit" name="valider" class="btn btn-valider">Valider</button>
                                <button type="button" class="btn btn-modifier" onclick="modifierDates(<?php echo $reservation['id']; ?>)">Modifier dates</button>
                            </form>
                            <?php elseif ($reservation['statut'] === 'validee'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                <button type="submit" name="annuler" class="btn btn-supprimer">Annuler</button>
                            </form>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($reservation['commentaire'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Liste des salles -->
        <div class="table-container">
            <h2>Salles disponibles</h2>
            <table class="gestion-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Capacité</th>
                        <th>Disponible</th>
                        <th>Photo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salles as $salle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($salle['nom']); ?></td>
                        <td><?php echo $salle['capacite']; ?></td>
                        <td><?php echo $salle['disponible'] ? 'Oui' : 'Non'; ?></td>
                        <td>
                            <?php if (!empty($salle['photo'])): ?>
                                <img src="../uploads/salles/<?php echo htmlspecialchars($salle['photo']); ?>" alt="Photo salle" style="max-width:80px;max-height:80px;">
                            <?php else: ?>
                                <span style="color:#888;">Aucune</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette salle ?');">
                                <input type="hidden" name="salle_id" value="<?php echo $salle['id']; ?>">
                                <button type="submit" name="supprimer" class="btn btn-supprimer">Supprimer</button>
                            </form>
                            <button type="button" class="btn btn-reserver" onclick="nouvelleReservation(<?php echo $salle['id']; ?>)">Réserver</button>
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

    <!-- Modal pour nouvelle réservation -->
    <div id="modal-reservation" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Nouvelle réservation</h2>
            <!-- Ajout du calendrier -->
            <div id="calendar-reservations" style="margin-bottom: 20px;"></div>
            <form method="POST" id="form-nouvelle-reservation">
                <input type="hidden" name="salle_id" id="modal-salle-id">
                <div class="form-group">
                    <label>Date de début</label>
                    <input type="datetime-local" name="date_debut" id="date_debut" required>
                </div>
                <div class="form-group">
                    <label>Date de fin</label>
                    <input type="datetime-local" name="date_fin" id="date_fin" required>
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

    // Fonction pour initialiser le calendrier
    function initCalendar(salleId) {
        const calendarEl = document.getElementById('calendar-reservations');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            slotMinTime: '08:00:00',
            slotMaxTime: '20:00:00',
            allDaySlot: false,
            locale: 'fr',
            events: function(info, successCallback, failureCallback) {
                // Récupérer les réservations existantes via AJAX
                fetch(`get_reservations.php?salle_id=${salleId}`)
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data.map(reservation => ({
                            title: 'Réservé',
                            start: reservation.date_debut,
                            end: reservation.date_fin,
                            backgroundColor: reservation.statut === 'validee' ? '#4CAF50' : '#FFA726'
                        })));
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        failureCallback(error);
                    });
            },
            selectable: true,
            select: function(info) {
                document.getElementById('date_debut').value = info.startStr.slice(0, 16);
                document.getElementById('date_fin').value = info.endStr.slice(0, 16);
            }
        });
        calendar.render();
        return calendar;
    }

    let currentCalendar = null;

    function nouvelleReservation(salleId) {
        document.getElementById('modal-salle-id').value = salleId;
        document.getElementById('modal-reservation').style.display = 'block';
        
        // Initialiser ou mettre à jour le calendrier
        if (currentCalendar) {
            currentCalendar.destroy();
        }
        currentCalendar = initCalendar(salleId);
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