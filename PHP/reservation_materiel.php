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
                <?php 
                // Débogage - Afficher les données
                foreach ($materiels as $materiel): 
                    echo "<!-- Debug: ";
                    print_r($materiel);
                    echo " -->";
                ?>
                    <div class="materiel-card">
                        <div class="materiel-image-container">
                            <?php if (!empty($materiel['photo'])): ?>
                                <img src="/ResaUGE-Project/<?php echo htmlspecialchars($materiel['photo']); ?>" 
                                     alt="<?php echo htmlspecialchars($materiel['nom']); ?>" 
                                     class="materiel-photo"
                                     onerror="this.onerror=null; this.src='/ResaUGE-Project/img/no-image.png';">
                            <?php else: ?>
                                <div class="no-photo">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="12" cy="12" r="3"/>
                                        <path d="M3 15l4-4a3 3 0 0 1 3 0l4 4"/>
                                        <path d="M14 14l1-1a3 3 0 0 1 3 0l3 3"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="materiel-info">
                            <h3 class="materiel-nom"><?php echo htmlspecialchars($materiel['nom']); ?></h3>
                            <p class="materiel-type"><?php echo htmlspecialchars($materiel['type']); ?></p>
                            <?php if (!empty($materiel['description'])): ?>
                                <p class="materiel-description"><?php echo htmlspecialchars($materiel['description']); ?></p>
                            <?php endif; ?>
                            <button type="button" class="btn btn-reserver" 
                                    onclick="afficherCalendrier(<?php echo $materiel['id']; ?>, 
                                                              '<?php echo addslashes($materiel['nom']); ?>', 
                                                              '<?php echo isset($materiel['reservations']) ? addslashes($materiel['reservations']) : ''; ?>')">
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
                    
                    <div class="modal-body">
                        <div id="calendar"></div>
                        
                        <form method="POST" id="form-reservation" class="form-reservation">
                            <input type="hidden" name="materiel_id" id="materiel_id">
                            <div class="form-group">
                                <label for="date_debut">Date et heure de début</label>
                                <input type="datetime-local" name="date_debut" id="date_debut" required>
                            </div>
                            <div class="form-group">
                                <label for="date_fin">Date et heure de fin</label>
                                <input type="datetime-local" name="date_fin" id="date_fin" required>
                            </div>
                            <button type="submit" name="reserver" class="btn-confirmer">Confirmer la réservation</button>
                        </form>
                    </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        let calendar;

        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour'
                },
                firstDay: 1,
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekNumbers: true,
                nowIndicator: true,
                select: function(info) {
                    let startDate = info.start;
                    let endDate = info.end;

                    // Formater les dates pour les inputs datetime-local
                    document.getElementById('date_debut').value = formatDateTime(startDate);
                    document.getElementById('date_fin').value = formatDateTime(endDate);
                }
            });
            calendar.render();
        }

        // Fonction pour formater la date et l'heure au format datetime-local
        function formatDateTime(date) {
            return date.getFullYear() +
                '-' + pad(date.getMonth() + 1) +
                '-' + pad(date.getDate()) +
                'T' + pad(date.getHours()) +
                ':' + pad(date.getMinutes());
        }

        // Fonction pour ajouter un zéro devant les nombres < 10
        function pad(number) {
            return (number < 10 ? '0' : '') + number;
        }

        function afficherCalendrier(materielId, materielNom, reservations) {
            document.getElementById('materiel_id').value = materielId;
            document.getElementById('materiel-nom').textContent = materielNom;
            
            // Réinitialiser le calendrier
            if (calendar) {
                calendar.removeAllEvents();
            } else {
                initCalendar();
            }
            
            // Ajouter les réservations existantes
            if (reservations) {
                const events = reservations.split(';').filter(Boolean).map(reservation => {
                    const [debut, fin, statut] = reservation.split('|');
                    return {
                        start: debut,
                        end: fin,
                        title: 'Réservé',
                        color: statut === 'validee' ? '#28a745' : 
                               (statut === 'en_attente' ? '#ffc107' : '#dc3545'),
                        textColor: statut === 'en_attente' ? '#000' : '#fff'
                    };
                });
                calendar.addEventSource(events);
            }
            
            // Afficher la modal
            const modal = document.getElementById('modal-reservation');
            modal.style.display = 'block';
            
            // Forcer le re-render du calendrier
            setTimeout(() => {
                calendar.updateSize();
            }, 100);
        }

        // Gestionnaires d'événements pour la modal
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('modal-reservation').style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modal-reservation');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Validation du formulaire
        document.getElementById('form-reservation').addEventListener('submit', function(e) {
            const dateDebut = new Date(document.getElementById('date_debut').value);
            const dateFin = new Date(document.getElementById('date_fin').value);
            
            if (dateFin <= dateDebut) {
                e.preventDefault();
                alert('La date de fin doit être postérieure à la date de début');
            }
        });

        // Rendre la fonction afficherCalendrier globale
        window.afficherCalendrier = afficherCalendrier;
    });
    </script>
</body>
</html> 