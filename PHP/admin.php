<?php
require_once 'check_session.php';
// Le fichier check_session.php s'occupe déjà de toutes les vérifications

$user = $_SESSION['utilisateur'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Administrateur - ResaUGE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../CSS/admin.css" rel="stylesheet">
</head>
<body>
    <div class="menu-lateral">
        <h2>Administration</h2>
        <nav>
            <a href="admin.php" class="menu-item active">
                <i class="bi bi-house-door"></i>Accueil
            </a>
            <a href="validation_compte.php" class="menu-item">
                <i class="bi bi-person-check"></i>Validation des utilisateurs
            </a>
            <a href="gestion_materiel.php" class="menu-item">
                <i class="bi bi-tools"></i>Gestion du matériel
            </a>
            <a href="gestion_salle.php" class="menu-item">
                <i class="bi bi-building"></i>Gestion des salles
            </a>
            <a href="suivi_reservations.php" class="menu-item">
                <i class="bi bi-calendar-check"></i>Suivi des réservations
            </a>
            <a href="logout.php" class="menu-item">
                <i class="bi bi-box-arrow-right"></i>Déconnexion
            </a>
        </nav>
    </div>

    <div class="accueil">
        <h1>Bonjour, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
        <p class="lead">Espace d'administration</p>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <img src="../img/emplacement.png" class="card-img-top" alt="Gestion des salles">
                    <div class="card-body text-center">
                        <h5 class="card-title">Gestion des salles</h5>
                        <p class="card-text">Gérez les salles de l'université et leur disponibilité.</p>
                        <a href="gestion_salle.php" class="btn btn-custom w-100">Gérer</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <img src="../img/cadenas-verrouille.png" class="card-img-top" alt="Gestion du matériel">
                    <div class="card-body text-center">
                        <h5 class="card-title">Gestion du matériel</h5>
                        <p class="card-text">Gérez le matériel et les équipements disponibles.</p>
                        <a href="gestion_materiel.php" class="btn btn-custom w-100">Gérer</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <img src="../img/profil.png" class="card-img-top" alt="Gestion des utilisateurs">
                    <div class="card-body text-center">
                        <h5 class="card-title">Gestion des utilisateurs</h5>
                        <p class="card-text">Gérez les comptes utilisateurs et leurs droits.</p>
                        <a href="validation_compte.php" class="btn btn-custom w-100">Gérer</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <img src="../img/nom.png" class="card-img-top" alt="Suivi des réservations">
                    <div class="card-body text-center">
                        <h5 class="card-title">Suivi des réservations</h5>
                        <p class="card-text">Consultez et gérez les réservations en cours.</p>
                        <a href="suivi_reservations.php" class="btn btn-custom w-100">Gérer</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer fixed-bottom py-3">
        <div class="container text-center">
            <span>&copy; 2025 Université Eiffel. Tous droits réservés.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


