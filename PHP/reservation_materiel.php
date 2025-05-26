<?php
session_start();
require_once 'includes/redirect_role.php';

// Vérifier si l'utilisateur est connecté et a les droits appropriés
if (!isset($_SESSION['utilisateur']) || !in_array($_SESSION['utilisateur']['role'], ['student', 'admin'])) {
    redirect_to_role_home();
}

// Inclure le fichier de connexion à la base de données
require_once 'connexion.php';

// Traitement du formulaire de réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['materiel_id'])) {
    $materiel_id = $_POST['materiel_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $user_id = $_SESSION['utilisateur']['id'];

    try {
        // Vérifier si le matériel est disponible pour cette période
        $sql_check = "SELECT COUNT(*) FROM reservation_materiel
                     WHERE materiel_id = :materiel_id 
                     AND statut != 'refusee'
                     AND (
                         (date_debut BETWEEN :date_debut AND :date_fin)
                         OR (date_fin BETWEEN :date_debut AND :date_fin)
                         OR (:date_debut BETWEEN date_debut AND date_fin)
                     )";
        
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([
            'materiel_id' => $materiel_id,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ]);

        if ($stmt_check->fetchColumn() > 0) {
            $_SESSION['message'] = "Ce matériel n'est pas disponible pour cette période.";
            $_SESSION['message_type'] = "error";
        } else {
            // Insérer la réservation
            $sql_insert = "INSERT INTO reservation_materiel (materiel_id, user_id, date_debut, date_fin, statut) 
                          VALUES (:materiel_id, :user_id, :date_debut, :date_fin, 'en_attente')";
            
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->execute([
                'materiel_id' => $materiel_id,
                'user_id' => $user_id,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin
            ]);

            $_SESSION['message'] = "Votre demande de réservation a été enregistrée avec succès.";
            $_SESSION['message_type'] = "success";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Une erreur est survenue lors de la réservation.";
        $_SESSION['message_type'] = "error";
    }

    // Rediriger pour éviter la soumission multiple du formulaire
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Récupérer la liste du matériel disponible
$sql = "SELECT * FROM materiel WHERE disponible = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$materiels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les réservations et les demandes de l'utilisateur
$sql_reservations = "SELECT 
    'reservation' as type_demande,
    r.date_debut,
    r.date_fin,
    r.statut,
    r.commentaire,
    m.nom as materiel_nom,
    m.type as materiel_type
FROM reservation_materiel r 
JOIN materiel m ON r.materiel_id = m.id 
WHERE r.user_id = :user_id 

UNION ALL

SELECT 
    'demande' as type_demande,
    dm.date_debut,
    dm.date_fin,
    dm.statut,
    NULL as commentaire,
    dmi.nom_materiel as materiel_nom,
    dmi.nom_materiel as materiel_type
FROM demande_materiel dm
JOIN demande_materiel_items dmi ON dm.id = dmi.demande_id
WHERE dm.user_id = :user_id 

ORDER BY date_debut DESC";

$stmt_reservations = $conn->prepare($sql_reservations);
$stmt_reservations->execute(['user_id' => $_SESSION['utilisateur']['id']]);
$reservations = $stmt_reservations->fetchAll(PDO::FETCH_ASSOC);

// Récupérer et effacer les messages de la session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de Matériel - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/reservation_mat.css">
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

        .datetime-inputs {
            margin: 20px 0;
        }

        .validation-info {
            font-size: 0.9em;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #4a5568;
        }

        .validation-info .signature {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
        }

        .validation-info .date-signature {
            color: #718096;
            font-size: 0.85em;
            margin-bottom: 4px;
        }

        .validation-info .commentaire {
            color: #4a5568;
            font-style: italic;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            margin-top: 4px;
        }

        .no-validation {
            color: #718096;
            font-style: italic;
        }

        .statut-en_attente {
            background-color: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .statut-validee {
            background-color: #dcfce7;
            color: #166534;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .statut-refusee {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        /* Badges de statut */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 500;
        }

        .bg-success {
            background-color: #10b981;
            color: white;
        }

        .bg-warning {
            background-color: #f59e0b;
            color: black;
        }

        .bg-danger {
            background-color: #ef4444;
            color: white;
        }

        .table-wrapper {
            overflow-x: auto;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .reservations-table {
            width: 100%;
            border-collapse: collapse;
        }

        .reservations-table th,
        .reservations-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .reservations-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #4a5568;
        }

        .reservations-table tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <nav class="nav-container">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
            <div class="nav-menu">
                <?php if ($_SESSION['utilisateur']['role'] === 'admin'): ?>
                    <a href="admin.php">Accueil</a>
                    <a href="gestion_materiel.php">Gestion du matériel</a>
                    <a href="gestion_salle.php">Gestion des salles</a>
                    <a href="validation_compte.php">Utilisateurs</a>
                    <a href="reservation_materiel.php" class="active">Réservations</a>
                <?php else: ?>
                    <a href="student.php">Accueil</a>
                    <a href="reservation_materiel.php" class="active">Réservations</a>
                <?php endif; ?>
                <a href="profil.php">Mon Compte</a>
            </div>
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
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </nav>

    <div class="main-content">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <h1>Réservation de Matériel</h1>
        
        <div class="materiel-grid">
            <?php foreach ($materiels as $materiel): ?>
                <div class="salle-card">
                    <?php if (!empty($materiel['photo'])): ?>
                        <img src="../<?php echo htmlspecialchars($materiel['photo']); ?>" 
                             alt="<?php echo htmlspecialchars($materiel['nom']); ?>" 
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
                        <h3 class="salle-nom"><?php echo htmlspecialchars($materiel['nom']); ?></h3>
                        <?php if (!empty($materiel['type'])): ?>
                            <p class="salle-info">Type : <b><?php echo htmlspecialchars($materiel['type']); ?></b></p>
                        <?php endif; ?>
                        <p class="salle-info">Disponible : <b><?php echo $materiel['disponible'] ? 'Oui' : 'Non'; ?></b></p>
                        <?php if (!empty($materiel['description'])): ?>
                            <p class="salle-description"><?php echo htmlspecialchars($materiel['description']); ?></p>
                        <?php endif; ?>
                        <button type="button" class="btn-reserver" 
                                onclick="afficherCalendrier(<?php echo $materiel['id']; ?>, '<?php echo addslashes($materiel['nom']); ?>')">
                            Réserver ce matériel
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mes-reservations">
            <h2>Mes réservations</h2>
            <div class="table-wrapper">
                <table class="reservations-table">
                    <thead>
                        <tr>
                            <th>Matériel</th>
                            <th>Type</th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservations)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Aucune réservation trouvée</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reservation['materiel_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['materiel_type']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_debut'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_fin'])); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $reservation['statut'] === 'validee' ? 'bg-success' : 
                                                ($reservation['statut'] === 'en_attente' ? 'bg-warning' : 
                                                ($reservation['statut'] === 'approuvee' ? 'bg-success' : 'bg-danger')); 
                                        ?>">
                                            <?php echo htmlspecialchars($reservation['statut']); ?>
                                        </span>
                                        <?php if ($reservation['commentaire']): ?>
                                            <div class="commentaire" style="margin-top: 4px; font-size: 0.9em; color: #666;">
                                                <?php echo htmlspecialchars($reservation['commentaire']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> ResaUGE - IUT de Meaux</p>
        <p>Département MMI</p>
    </footer>

    <!-- Modal avec calendrier -->
    <div id="modal-reservation" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Réserver le matériel : <span id="materiel-nom"></span></h2>
            
            <div class="modal-body">
                <div id="calendar"></div>
                
                <form method="GET" action="demande_materiel.php">
                    <input type="hidden" name="materiel_id" id="materiel_id">
                    <div class="datetime-inputs">
                        <div class="datetime-field">
                            <label>Date et heure de début</label>
                            <input type="datetime-local" id="date_debut" name="date_debut" required>
                        </div>
                        <div class="datetime-field">
                            <label>Date et heure de fin</label>
                            <input type="datetime-local" id="date_fin" name="date_fin" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-reserver">Continuer vers la demande</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let calendar;
        const modal = document.getElementById('modal-reservation');

        // Fonction pour afficher la modal
        window.afficherCalendrier = function(materielId, materielNom) {
            document.getElementById('materiel-nom').textContent = materielNom;
            document.getElementById('materiel_id').value = materielId;
            modal.style.display = 'block';
            
            // Initialisation du calendrier si pas déjà fait
            if (!calendar) {
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
                        document.getElementById('date_debut').value = formatDateTime(info.start);
                        document.getElementById('date_fin').value = formatDateTime(info.end);
                    }
                });
            }

            // Forcer le rendu du calendrier
            setTimeout(() => {
                calendar.render();
                calendar.updateSize();
            }, 100);
        };

        // Fonction pour formater la date
        function formatDateTime(date) {
            return date.getFullYear() +
                '-' + pad(date.getMonth() + 1) +
                '-' + pad(date.getDate()) +
                'T' + pad(date.getHours()) +
                ':' + pad(date.getMinutes());
        }

        function pad(number) {
            return (number < 10 ? '0' : '') + number;
        }

        // Fermeture de la modal
        const closeBtn = document.querySelector('.close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }

        // Fermeture en cliquant en dehors de la modal
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html> 