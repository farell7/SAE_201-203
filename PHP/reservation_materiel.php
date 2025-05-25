<?php include 'rm.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de Matériel - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/reservation_mat.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="student.php">Accueil</a>
            <a href="#" class="active">Réservations</a>
            <a href="#">Mon Compte</a>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <div class="menu-icon">☰</div>
        </div>
    </nav>

    <main class="main-content">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="materiel-section">
            <h1>Réservation de Matériel</h1>

            <!-- Grille de matériel -->
            <div class="materiel-grid">
                <?php foreach ($materiels as $materiel): ?>
                    <div class="materiel-card">
                        <?php if (!empty($materiel['photo'])): ?>
                            <img src="../uploads/materiel/<?php echo htmlspecialchars($materiel['photo']); ?>" alt="Photo matériel" class="materiel-photo">
                        <?php else: ?>
                            <div class="no-photo">Pas de photo</div>
                        <?php endif; ?>
                        <div class="materiel-info">
                            <h3 class="materiel-nom"><?php echo htmlspecialchars($materiel['nom']); ?></h3>
                            <p class="materiel-type"><?php echo htmlspecialchars($materiel['type']); ?></p>
                            <?php if (!empty($materiel['description'])): ?>
                                <p class="materiel-description"><?php echo htmlspecialchars($materiel['description']); ?></p>
                            <?php endif; ?>
                            <button type="button" class="btn btn-reserver" 
                                    onclick="afficherCalendrier(<?php echo $materiel['id']; ?>, 
                                                              '<?php echo addslashes($materiel['nom']); ?>', 
                                                              '<?php echo $materiel['reservations']; ?>')">
                                Réserver
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Modal avec calendrier -->
            <div id="modal-reservation" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Réserver : <span id="materiel-nom"></span></h2>
                    <div id="calendar"></div>
                    <form method="POST" id="form-reservation" class="form-reservation">
                        <input type="hidden" name="materiel_id" id="materiel_id">
                        <div class="form-group">
                            <label>Date de début</label>
                            <input type="datetime-local" name="date_debut" id="date_debut" required>
                        </div>
                        <div class="form-group">
                            <label>Date de fin</label>
                            <input type="datetime-local" name="date_fin" id="date_fin" required>
                        </div>
                        <button type="submit" name="reserver" class="btn btn-reserver">Confirmer la réservation</button>
                    </form>
                </div>
            </div>

            <!-- Liste des réservations -->
            <div class="table-container">
                <h2>Mes réservations</h2>
                <table class="gestion-table">
                    <thead>
                        <tr>
                            <th>Matériel</th>
                            <th>Type</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['materiel_nom']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['materiel_type']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                            <td>
                                <span class="badge <?php echo $reservation['statut'] === 'validee' ? 'bg-success' : 
                                    ($reservation['statut'] === 'en_attente' ? 'bg-warning' : 'bg-danger'); ?>">
                                    <?php echo htmlspecialchars($reservation['statut']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="footer">
        &copy;2025 Université Eiffel. Tous droits réservés.
    </footer>

    <script>
    let calendar;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation du calendrier
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            selectable: true,
            select: function(info) {
                document.getElementById('date_debut').value = info.startStr;
                document.getElementById('date_fin').value = info.endStr;
            }
        });
        calendar.render();
    });

    function afficherCalendrier(materielId, materielNom, reservations) {
        document.getElementById('materiel_id').value = materielId;
        document.getElementById('materiel-nom').textContent = materielNom;
        
        // Nettoyer les événements existants
        calendar.removeAllEvents();
        
        // Ajouter les réservations existantes
        if (reservations) {
            const events = reservations.split(';').map(reservation => {
                const [debut, fin, statut] = reservation.split('|');
                return {
                    start: debut,
                    end: fin,
                    title: 'Réservé',
                    color: statut === 'validee' ? '#28a745' : 
                           (statut === 'en_attente' ? '#ffc107' : '#dc3545')
                };
            });
            calendar.addEventSource(events);
        }
        
        document.getElementById('modal-reservation').style.display = 'block';
    }

    // Fermeture du modal
    document.querySelector('.close').onclick = function() {
        document.getElementById('modal-reservation').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }

    // Validation des dates
    document.getElementById('form-reservation').addEventListener('submit', function(e) {
        const dateDebut = new Date(document.getElementById('date_debut').value);
        const dateFin = new Date(document.getElementById('date_fin').value);
        
        if (dateFin <= dateDebut) {
            e.preventDefault();
            alert('La date de fin doit être postérieure à la date de début');
        }
    });
    </script>
</body>
</html> 