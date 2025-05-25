-- Création de la base de données
CREATE DATABASE IF NOT EXISTS resauge;
USE resauge;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    pseudo VARCHAR(50) NOT NULL,
    code_postal VARCHAR(5) NOT NULL,
    date_naissance DATE NOT NULL,
    role ENUM('student', 'teacher', 'agent', 'admin') NOT NULL,
    valide BOOLEAN DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des salles
CREATE TABLE IF NOT EXISTS salle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    capacite INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    equipements TEXT,
    disponible BOOLEAN DEFAULT TRUE
);

-- Table des réservations
CREATE TABLE IF NOT EXISTS reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    salle_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    motif TEXT,
    statut ENUM('en_attente', 'approuvee', 'refusee') DEFAULT 'en_attente',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (salle_id) REFERENCES salle(id)
);

-- Table du matériel
CREATE TABLE IF NOT EXISTS materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    quantite_disponible INT NOT NULL,
    description TEXT
);

-- Insertion d'un administrateur par défaut
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'System', 'admin@resauge.fr', '$2y$10$YourHashedPasswordHere', 'admin');

-- Structure de la table reservation_materiel
CREATE TABLE IF NOT EXISTS reservation_materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    materiel_id INT NOT NULL,
    user_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    statut ENUM('en_attente', 'validee', 'refusee', 'annulee') DEFAULT 'en_attente',
    commentaire TEXT,
    signature_admin VARCHAR(255),
    date_signature DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (materiel_id) REFERENCES materiel(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ajout d'index pour améliorer les performances
CREATE INDEX idx_user_email ON utilisateur(email);
CREATE INDEX idx_user_role ON utilisateur(role);
CREATE INDEX idx_reservation_dates ON reservation(date_debut, date_fin);
CREATE INDEX idx_reservation_statut ON reservation(statut);
CREATE INDEX idx_materiel_type ON materiel(type);
CREATE INDEX idx_reservation_materiel_dates ON reservation_materiel(date_debut, date_fin);

-- Instructions pour l'importation :
-- 1. Ouvrir phpMyAdmin
-- 2. Créer une nouvelle base de données nommée "resauge" si elle n'existe pas
-- 3. Sélectionner la base de données "resauge"
-- 4. Aller dans l'onglet "Importer"
-- 5. Choisir ce fichier
-- 6. Cliquer sur "Exécuter" 