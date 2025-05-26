<?php
try {
    $host = 'localhost';
    $dbname = 'resauge';
    $user = 'root';
    $pass = '';
    
    $connexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
