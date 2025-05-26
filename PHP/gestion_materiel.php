<?php 
session_start();
require_once 'includes/redirect_role.php';

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    redirect_to_role_home();
}

include 'gm.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Matériel - Admin</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/gestion_materiel.css">
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
                <a href="reservation_salle.php"><i class="fas fa-door-open"></i> Salles</a>
                <a href="reservation_materiel.php" class="active"><i class="fas fa-tools"></i> Matériel</a>
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
        <h1>Gestion du Matériel</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="actions-container" style="margin-bottom: 2rem;">
            <a href="admin_demandes_materiel.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 6px; background-color: #2f2a85; color: white; font-weight: 500;">
                <i class="fas fa-list-alt"></i>
                Voir les demandes de matériel
            </a>
        </div>

        <!-- Formulaire d'ajout de matériel -->
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
        <div class="footer-content">
            <div class="footer-section">
                <h3>ResaUGE</h3>
                <p>Système de réservation de l'Université Gustave Eiffel</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>Email: support@resauge.fr<br>Tél: 01 23 45 67 89</p>
            </div>
            <div class="footer-section">
                <h3>Liens utiles</h3>
                <a href="https://www.univ-gustave-eiffel.fr" target="_blank">Site de l'université</a><br>
                <a href="mentions_legales.php">Mentions légales</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> ResaUGE - Tous droits réservés</p>
        </div>
    </footer>

    <style>
        .footer {
            background-color: #2f2a85;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-around;
            padding: 0 1rem;
        }

        .footer-section {
            flex: 1;
            margin: 0 1rem;
        }

        .footer-section h3 {
            color: white;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .footer-section a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: #ddd;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-content {
            min-height: calc(100vh - 200px);
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>

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
