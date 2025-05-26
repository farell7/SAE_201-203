<?php
// Démarrer la session
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();
session_unset();

// Rediriger vers la page de connexion
header('Location: /SAE_201-203/index.php');
exit();
?> 