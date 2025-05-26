<?php include 'regis_proc.php'; ?>
 <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmation d'inscription - ResaUGE</title>
        <link rel="stylesheet" href="../CSS/modal.css">
    </head>
    <body>
        <div class="modal-overlay" style="display: block;">
            <div class="modal-container">
                <div class="modal-header">
                    <h2 class="modal-title"><?php echo $type === 'success' ? 'Inscription réussie' : 'Erreur'; ?></h2>
                </div>
                <div class="modal-body">
                    <p class="modal-message <?php echo $type; ?>"><?php echo $message; ?></p>
                </div>
                <div class="modal-footer">
                    <button class="modal-button" onclick="window.location.href='../index.php'">
                        <?php echo $type === 'success' ? 'Retour à l\'accueil' : 'Réessayer'; ?>
                    </button>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?> 