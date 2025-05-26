<?php
// Paramètres de connexion à la base de données
$host = 'localhost';
$dbname = 'resauge';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Alias pour la compatibilité avec le code existant
    $pdo = $conn;
    $connexion = $conn;
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
