<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/redirect_role.php';

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    redirect_to_role_home();
}

include 'gs.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Salles - Admin</title>
    <link rel="stylesheet" href="../CSS/style.css">
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
        <div class="nav-left">
            <a href="admin.php" class="nav-logo">
                <img src="../img/logo_sansfond.png" alt="Logo">
                <span>ResaUGE</span>
            </a>
            <div class="nav-menu">
                <a href="admin.php"><i class="fas fa-home"></i> Accueil</a>
                <a href="reservation_salle.php" class="active"><i class="fas fa-door-open"></i> Salles</a>
                <a href="reservation_materiel.php"><i class="fas fa-tools"></i> Matériel</a>
                <a href="validation_compte.php"><i class="fas fa-users"></i> Utilisateurs</a>
                <a href="statistiques.php"><i class="fas fa-chart-bar"></i> Statistiques</a>
            </div>
        </div>
        <div class="nav-right">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['utilisateur']['prenom'] . ' ' . $_SESSION['utilisateur']['nom']); ?></span>
            </div>
            <a href="../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </nav>

    <style>
        body {
            font-family: 'Noto Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .nav-container {
            background-color: #2f2a85;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Noto Sans', sans-serif;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: white;
            font-weight: 600;
        }

        .nav-logo img {
            height: 40px;
            width: auto;
        }

        .nav-menu {
            display: flex;
            gap: 1.5rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-menu a:hover, .nav-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .nav-menu a i {
            font-size: 1.1rem;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
        }

        .nav-right .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .nav-right a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-right a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
    </style>

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
                                <input type="text" name="signature" placeholder="Signature de l'administrateur" required class="signature-input">
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
                                <img src="../<?php echo htmlspecialchars($salle['photo']); ?>" alt="Photo salle" style="max-width:80px;max-height:80px;">
                            <?php else: ?>
                                <span style="color:#888;">Aucune</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette salle ?');">
                                <input type="hidden" name="salle_id" value="<?php echo $salle['id']; ?>">
                                <button type="submit" name="supprimer" class="btn btn-supprimer">Supprimer</button>
                            </form>
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