<?php include 'rs.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de Salle - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/gestion_salle.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../CSS/reservation_salle.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            border-radius: 8px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-body {
            max-height: calc(90vh - 120px);
            overflow-y: auto;
            padding: 20px 0;
        }

        #calendar {
            margin-bottom: 20px;
        }

        .form-reservation {
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="student.php">Accueil</a>
            <a href="#" class="active">Réservations</a>
            <a href="profil.php">Mon Compte</a>
        </div>
        <div class="profile-menu">
            <a href="profil.php" class="user-info">
                <?php if (!empty($_SESSION['utilisateur']['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['utilisateur']['photo']); ?>" alt="Photo de profil" class="profile-icon">
                <?php else: ?>
                    <img src="../img/profil.png" alt="Photo de profil par défaut" class="profile-icon">
                <?php endif; ?>
                <span><?php echo htmlspecialchars($_SESSION['utilisateur']['prenom'] . ' ' . $_SESSION['utilisateur']['nom']); ?></span>
            </a>
            <a href="../logout.php" class="logout-btn">Déconnexion</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="salles-section">
            <h1>Réservation de Salle</h1>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Liste des salles disponibles sous forme de cartes -->
            <h2>Salles disponibles</h2>
            <div class="salles-grid">
            <?php 
            // Debug - Afficher les données
            echo "<!-- Debug données salles : ";
            var_dump($salles);
            echo " -->";
            
            foreach ($salles as $salle): 
                echo "<!-- Debug salle : ";
                var_dump($salle);
                echo " -->";
            ?>
                <div class="salle-card">
                    <?php if (!empty($salle['photo'])): ?>
                        <img src="../<?php echo htmlspecialchars($salle['photo']); ?>" 
                             alt="<?php echo htmlspecialchars($salle['nom']); ?>" 
                             class="salle-photo"
                             onerror="this.src='../img/no-image.png'">
                    <?php else: ?>
                        <div class="salle-photo" style="background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M3 15l4-4a3 3 0 0 1 3 0l4 4"/>
                                <path d="M14 14l1-1a3 3 0 0 1 3 0l3 3"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    
                    <div class="salle-info-container">
                        <h3 class="salle-nom"><?php echo htmlspecialchars($salle['nom']); ?></h3>
                        <!-- Debug photo -->
                        <small style="color: #999;">Photo : <?php echo !empty($salle['photo']) ? $salle['photo'] : 'Aucune photo'; ?></small>
                        <p class="salle-info">Capacité : <b><?php echo $salle['capacite']; ?> personnes</b></p>
                        <p class="salle-info">Disponible : <b><?php echo $salle['disponible'] ? 'Oui' : 'Non'; ?></b></p>
                        <?php if (!empty($salle['description'])): ?>
                            <p class="salle-description"><?php echo htmlspecialchars($salle['description']); ?></p>
                        <?php endif; ?>
                        <button type="button" class="btn-reserver" 
                                onclick="afficherCalendrier(<?php echo $salle['id']; ?>, 
                                                          '<?php echo addslashes($salle['nom']); ?>', 
                                                          '<?php echo isset($salle['reservations']) ? addslashes($salle['reservations']) : ''; ?>')">
                            Réserver cette salle
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>

            <!-- Modal avec calendrier -->
            <div id="modal-reservation" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Réserver : <span id="salle-nom"></span></h2>
                    
                    <div class="modal-body">
                        <div id="calendar"></div>
                        
                        <form method="POST" id="form-reservation" class="form-reservation">
                            <input type="hidden" name="salle_id" id="salle_id">
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

            <!-- Historique des réservations -->
            <div class="historique-reservations">
                <h2>Historique de mes réservations</h2>
                <table class="gestion-table">
                    <thead>
                        <tr>
                            <th>Salle</th>
                            <th>Capacité</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['salle_nom']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['salle_capacite']); ?> personnes</td>
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

        function afficherCalendrier(salleId, salleNom, reservations) {
            document.getElementById('salle_id').value = salleId;
            document.getElementById('salle-nom').textContent = salleNom;
            
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