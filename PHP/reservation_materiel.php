<?php
session_start();
require_once 'includes/redirect_role.php';

// Vérifier si l'utilisateur est connecté et est un étudiant
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'student') {
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
                     AND statut = 'validee'
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

// Récupérer les réservations de l'utilisateur
$sql_reservations = "SELECT r.*, m.nom as materiel_nom, m.type as materiel_type 
                    FROM reservation_materiel r 
                    JOIN materiel m ON r.materiel_id = m.id 
                    WHERE r.user_id = :user_id 
                    ORDER BY r.created_at DESC";
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
</head>
<body>
    <nav class="nav-container">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
            <div class="nav-menu">
                <a href="student.php">Accueil</a>
                <a href="reservation_materiel.php" class="active">Réservations</a>
                <a href="demande_materiel.php">Demande de matériel</a>
            </div>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <a href="../logout.php" class="logout-btn">Déconnexion</a>
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
                                        <span class="statut-<?php echo strtolower($reservation['statut']); ?>">
                                            <?php echo htmlspecialchars($reservation['statut']); ?>
                                        </span>
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
                
                <div class="datetime-inputs">
                    <div class="datetime-field">
                        <label>Date et heure de début</label>
                        <input type="datetime-local" id="date_debut" name="date_debut">
                    </div>
                    <div class="datetime-field">
                        <label>Date et heure de fin</label>
                        <input type="datetime-local" id="date_fin" name="date_fin">
                    </div>
                </div>

                <a href="demande_materiel.php" class="btn-demande">Faire une demande de matériel</a>
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