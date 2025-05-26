<?php
session_start();
require_once 'connexion.php';

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Log des données reçues
        error_log("Données POST reçues : " . print_r($_POST, true));
        
        // Récupérer les données du formulaire
        $user_id = $_SESSION['utilisateur']['id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $numero_etudiant = $_POST['numero_etudiant'];
        $email = $_POST['email'];
        $date_emprunt = $_POST['date_emprunt'];
        $heure_emprunt = $_POST['heure_emprunt'];
        $heure_retour = $_POST['heure_retour'];
        $annee_mmi = $_POST['annee_mmi'];
        $groupe_tp = $_POST['groupe_tp'];

        // Créer la date de début et de fin
        $date_debut = date('Y-m-d H:i:s', strtotime($date_emprunt . ' ' . $heure_emprunt));
        $date_fin = date('Y-m-d H:i:s', strtotime($date_emprunt . ' ' . $heure_retour));

        // Log des dates formatées
        error_log("Date début : $date_debut, Date fin : $date_fin");

        // Insérer la demande dans la base de données
        $sql = "INSERT INTO demande_materiel (
            user_id, nom, prenom, numero_etudiant, email, 
            date_debut, date_fin, annee_mmi, groupe_tp, 
            statut, created_at
        ) VALUES (
            :user_id, :nom, :prenom, :numero_etudiant, :email,
            :date_debut, :date_fin, :annee_mmi, :groupe_tp,
            'en_attente', NOW()
        )";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id,
            'nom' => $nom,
            'prenom' => $prenom,
            'numero_etudiant' => $numero_etudiant,
            'email' => $email,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'annee_mmi' => $annee_mmi,
            'groupe_tp' => $groupe_tp
        ]);

        $demande_id = $conn->lastInsertId();
        error_log("Demande créée avec l'ID : $demande_id");

        // Insérer le matériel demandé
        if (isset($_POST['materiel']) && is_array($_POST['materiel'])) {
            error_log("Matériel à insérer : " . print_r($_POST['materiel'], true));
            
            $sql_materiel = "INSERT INTO demande_materiel_items (
                demande_id, nom_materiel, quantite
            ) VALUES (
                :demande_id, :nom_materiel, :quantite
            )";
            
            $stmt_materiel = $conn->prepare($sql_materiel);
            
            foreach ($_POST['materiel'] as $materiel) {
                $stmt_materiel->execute([
                    'demande_id' => $demande_id,
                    'nom_materiel' => $materiel['nom'],
                    'quantite' => $materiel['quantite']
                ]);
                error_log("Matériel inséré : " . $materiel['nom'] . " (Quantité : " . $materiel['quantite'] . ")");
            }
        }

        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        error_log("Erreur SQL: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement de la demande: ' . $e->getMessage()]);
    } catch (Exception $e) {
        error_log("Erreur générale: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Une erreur inattendue est survenue: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?> 