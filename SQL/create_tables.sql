-- Table pour les demandes de matériel
CREATE TABLE IF NOT EXISTS demande_materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    numero_etudiant VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    annee_mmi INT NOT NULL,
    groupe_tp VARCHAR(10) NOT NULL,
    statut ENUM('en_attente', 'approuvee', 'refusee') DEFAULT 'en_attente',
    signature_admin VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les items de la demande de matériel
CREATE TABLE IF NOT EXISTS demande_materiel_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL,
    nom_materiel VARCHAR(255) NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    FOREIGN KEY (demande_id) REFERENCES demande_materiel(id) ON DELETE CASCADE
); 