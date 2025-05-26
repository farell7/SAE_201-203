<?php
session_start();
require_once 'includes/redirect_role.php';

// Vérifier si l'utilisateur est connecté et est un étudiant
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'student') {
    redirect_to_role_home();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Matériel - ResaUGE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/dmd_mat.css">
    <style>
        .demande-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .logo-uge {
            height: 80px;
        }

        .header-right {
            text-align: right;
        }

        .form-title {
            color: #2f2a85;
            text-align: center;
            margin: 1rem 0;
        }

        .instructions {
            background-color: #f8f9fa;
            padding: 1rem;
            margin: 1rem 0;
            border-left: 4px solid #2f2a85;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group input[required]::after {
            content: "*";
            color: red;
        }

        .participants-list {
            border: 1px solid #ddd;
            padding: 1rem;
            margin: 1rem 0;
        }

        .warning {
            color: red;
            font-style: italic;
            margin: 1rem 0;
        }

        .submit-btn {
            background-color: #2f2a85;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background-color: #231f63;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 0.5rem;
        }

        th {
            background-color: #f8f9fa;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .text-center {
            text-align: center;
        }

        .success-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
        }

        .modal-body h2 {
            color: #28a745;
            margin-bottom: 1rem;
        }

        .modal-body p {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .btn-retour {
            background-color: #2f2a85;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            margin-top: 1.5rem;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-retour:hover {
            background-color: #231f63;
        }
    </style>
</head>
<body>
    <nav class="nav-container">
        <img src="../img/logo_sansfond.png" alt="Logo" class="logo">
        <div class="nav-menu">
            <a href="student.php">Accueil</a>
            <a href="reservation_materiel.php">Réservations</a>
            <a href="#" class="active">Demande de matériel</a>
        </div>
        <div class="profile-menu">
            <img src="../img/profil.png" alt="Profile" class="profile-icon">
            <a href="../logout.php" class="logout-btn">Déconnexion</a>
        </div>
    </nav>

    <div class="demande-container">
        <div class="header">
            <img src="../img/logo_sansfond.png" alt="Logo UGE" class="logo-uge">
            <div class="header-right">
                <h2>IUT de MEAUX</h2>
                <p>Département MMI</p>
            </div>
        </div>

        <h1 class="form-title">Demande d'accès au matériel</h1>

        <div class="instructions">
            <p>Adressez cette demande au responsable du matériel :</p>
            <p><strong>M. RESPONSABLE</strong> : responsable.materiel@univ-eiffel.fr</p>
        </div>

        <form method="POST" action="traitement_demande.php" id="form-demande">
            <table>
                <tr>
                    <td><label for="nom">Nom*:</label></td>
                    <td><input type="text" id="nom" name="nom" required value="<?php echo htmlspecialchars($_SESSION['utilisateur']['nom']); ?>"></td>
                </tr>
                <tr>
                    <td><label for="prenom">Prénom*:</label></td>
                    <td><input type="text" id="prenom" name="prenom" required value="<?php echo htmlspecialchars($_SESSION['utilisateur']['prenom']); ?>"></td>
                </tr>
                <tr>
                    <td><label for="numero_etudiant">Numéro de carte d'étudiant*:</label></td>
                    <td><input type="text" id="numero_etudiant" name="numero_etudiant" required></td>
                </tr>
                <tr>
                    <td><label for="email">Adresse mail*:</label></td>
                    <td><input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['utilisateur']['email']); ?>"></td>
                </tr>
                <tr>
                    <td><label for="date_emprunt">Date d'emprunt souhaitée*:</label></td>
                    <td><input type="date" id="date_emprunt" name="date_emprunt" required></td>
                </tr>
                <tr>
                    <td><label for="heure_emprunt">Heure d'emprunt*: (à partir de 08h30)</label></td>
                    <td><input type="time" id="heure_emprunt" name="heure_emprunt" min="08:30" max="18:00" required></td>
                </tr>
                <tr>
                    <td><label for="heure_retour">Heure de retour*: (jusqu'à 18h00)</label></td>
                    <td><input type="time" id="heure_retour" name="heure_retour" min="08:30" max="18:00" required></td>
                </tr>
                <tr>
                    <td><label for="annee_mmi">Année MMI*:</label></td>
                    <td>
                        <select id="annee_mmi" name="annee_mmi" required>
                            <option value="1">MMI 1</option>
                            <option value="2">MMI 2</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="groupe_tp">Groupe TP*:</label></td>
                    <td><input type="text" id="groupe_tp" name="groupe_tp" required></td>
                </tr>
            </table>

            <div class="participants-list">
                <h3>Matériel demandé :</h3>
                <div id="materiel-list">
                    <!-- La liste du matériel sera ajoutée dynamiquement -->
                </div>
                <button type="button" onclick="ajouterMateriel()" class="submit-btn" style="margin-bottom: 1rem;">Ajouter du matériel</button>
            </div>

            <p class="warning">Il est obligatoire de respecter l'heure de remise du matériel, un contrôle sera effectué juste après la remise.</p>
            <p class="warning">Il est formellement interdit de prêter le matériel à des personnes non citées dans cette demande d'accès.</p>

            <button type="submit" class="submit-btn">Envoyer la demande</button>
        </form>
    </div>

    <!-- Modal de confirmation -->
    <div id="modal-confirmation" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-body text-center">
                <img src="../img/check.png" alt="Succès" class="success-icon">
                <h2>Demande envoyée avec succès !</h2>
                <p>Votre demande a été enregistrée et sera examinée par un administrateur.</p>
                <p>Vous pouvez suivre l'état de votre demande dans la section "Mes réservations".</p>
                <button onclick="window.location.href='reservation_materiel.php'" class="btn-retour">Retour aux réservations</button>
            </div>
        </div>
    </div>

    <script>
    let materielCount = 0;

    function ajouterMateriel() {
        const div = document.createElement('div');
        div.className = 'form-group';
        div.style.display = 'flex';
        div.style.gap = '1rem';
        div.style.marginBottom = '1rem';

        div.innerHTML = `
            <input type="text" name="materiel[${materielCount}][nom]" placeholder="Nom du matériel" required style="flex: 2;">
            <input type="number" name="materiel[${materielCount}][quantite]" placeholder="Quantité" required style="flex: 1;" min="1">
            <button type="button" onclick="this.parentElement.remove()" style="background-color: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;">Supprimer</button>
        `;

        document.getElementById('materiel-list').appendChild(div);
        materielCount++;
    }

    document.getElementById('form-demande').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Envoyer le formulaire en AJAX
        const formData = new FormData(this);
        fetch('traitement_demande.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher la modal de confirmation
                document.getElementById('modal-confirmation').style.display = 'block';
            } else {
                alert('Une erreur est survenue lors de l\'envoi de la demande.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'envoi de la demande.');
        });
    });

    // Fermeture de la modal
    document.querySelector('.close').addEventListener('click', function() {
        document.getElementById('modal-confirmation').style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modal-confirmation');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    </script>
</body>
</html> 