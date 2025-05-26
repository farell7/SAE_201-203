<?php
require_once 'connexion.php';

try {
    $sql_file = 'database.sql';
    
    // Vérifier si le fichier existe
    if (!file_exists($sql_file)) {
        die("Le fichier database.sql n'existe pas.");
    }
    
    // Lire le contenu du fichier SQL
    $sql = file_get_contents($sql_file);
    if ($sql === false) {
        die("Impossible de lire le fichier database.sql");
    }
    
    // Séparer les requêtes SQL (en se basant sur le point-virgule suivi d'une nouvelle ligne)
    $queries = array_filter(explode(";\n", $sql));
    
    // Compteurs pour le suivi
    $success = 0;
    $errors = 0;
    
    foreach ($queries as $query) {
        if (trim($query) != '') {
            try {
                $conn->exec($query);
                echo "✓ Requête exécutée avec succès : " . substr($query, 0, 50) . "...<br>";
                $success++;
            } catch (PDOException $e) {
                echo "✗ Erreur lors de l'exécution de la requête : " . $e->getMessage() . "<br>";
                echo "Requête : " . $query . "<br>";
                $errors++;
            }
        }
    }
    
    echo "<br>Configuration de la base de données terminée !<br>";
    echo "Requêtes réussies : " . $success . "<br>";
    echo "Erreurs : " . $errors . "<br>";
    
} catch(PDOException $e) {
    die("Erreur lors de la configuration de la base de données : " . $e->getMessage());
}
?> 