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
    <link rel="stylesheet" href="../CSS/gestion_salle.css">
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="#">Accueil</a>
            <a href="#" class="active">Réservations</a>
            <a href="#">Mon Compte</a>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <div class="menu-icon">☰</div>
        </div>
    </nav>

    <main class="main-content">
        <h1>Réservation de Salle</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Liste des salles disponibles sous forme de cartes -->
        <h2 style="margin-top:40px;">Salles disponibles</h2>
        <div style="display:flex;flex-wrap:wrap;gap:24px;">
        <?php foreach ($salles as $salle): ?>
            <div style="background:#fff;border-radius:10px;box-shadow:0 2px 8px #0001;padding:20px;width:300px;display:flex;flex-direction:column;align-items:center;">
                <?php if (!empty($salle['photo'])): ?>
                    <img src="../uploads/salles/<?php echo htmlspecialchars($salle['photo']); ?>" alt="Photo salle" style="max-width:240px;max-height:180px;border-radius:8px;margin-bottom:10px;object-fit:cover;">
                <?php endif; ?>
                <h3 style="margin:0 0 8px 0;"><?php echo htmlspecialchars($salle['nom']); ?></h3>
                <p style="margin:0 0 4px 0;">Capacité : <b><?php echo $salle['capacite']; ?></b></p>
                <p style="margin:0 0 4px 0;">Disponible : <b><?php echo $salle['disponible'] ? 'Oui' : 'Non'; ?></b></p>
                <?php if (!empty($salle['description'])): ?>
                    <p style="font-size:0.95em;color:#555;margin:0 0 4px 0;">"<?php echo htmlspecialchars($salle['description']); ?>"</p>
                <?php endif; ?>
                <button type="button" class="btn btn-reserver" onclick="afficherFormReservation(<?php echo $salle['id']; ?>)">Réserver</button>
            </div>
        <?php endforeach; ?>
        </div>

        <!-- Formulaire de réservation masqué par défaut -->
        <div id="form-reservation-dynamique" style="display:none;margin:40px auto 0 auto;max-width:400px;">
            <h2>Nouvelle réservation</h2>
            <form method="POST" class="form-gestion">
                <input type="hidden" name="salle_id" id="input-salle-id">
                <div id="nom-salle-selectionnee" style="font-weight:bold;margin-bottom:10px;"></div>
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

        <!-- Liste des réservations -->
        <div class="table-container">
            <h2>Mes réservations</h2>
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
    </main>

    <footer class="footer">
        &copy;2025 Université Eiffel. Tous droits réservés.
    </footer>

    <script>
    // Affiche le formulaire de réservation pour la salle choisie
    function afficherFormReservation(salleId) {
        // Récupérer le nom de la salle
        var salles = <?php echo json_encode($salles); ?>;
        var salle = salles.find(s => s.id == salleId);
        document.getElementById('input-salle-id').value = salleId;
        document.getElementById('nom-salle-selectionnee').innerText = salle ? salle.nom : '';
        document.getElementById('form-reservation-dynamique').style.display = 'block';
        window.scrollTo({top: document.getElementById('form-reservation-dynamique').offsetTop - 60, behavior: 'smooth'});
    }

    // Définir la date minimale pour les champs datetime-local
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        
        document.querySelector('input[name="date_debut"]').min = minDateTime;
        document.querySelector('input[name="date_fin"]').min = minDateTime;
    });

    // Validation des dates
    document.querySelector('form').addEventListener('submit', function(e) {
        const dateDebut = new Date(document.querySelector('input[name="date_debut"]').value);
        const dateFin = new Date(document.querySelector('input[name="date_fin"]').value);
        
        if (dateFin <= dateDebut) {
            e.preventDefault();
            alert('La date de fin doit être postérieure à la date de début');
        }
    });
    </script>
</body>
</html> 