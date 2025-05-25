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
    role ENUM('student', 'teacher', 'agent', 'admin') NOT NULL DEFAULT 'student',
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_expiry DATETIME DEFAULT NULL,
    compte_valide BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des salles
CREATE TABLE IF NOT EXISTS salle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    capacite INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    photo VARCHAR(255),
    equipements TEXT,
    disponible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réservations de salles
CREATE TABLE IF NOT EXISTS reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    salle_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    motif TEXT,
    statut ENUM('en_attente', 'validee', 'refusee', 'annulee') DEFAULT 'en_attente',
    commentaire TEXT,
    signature_admin VARCHAR(255),
    date_signature DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (salle_id) REFERENCES salle(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table du matériel
CREATE TABLE IF NOT EXISTS materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    numero_serie VARCHAR(100),
    etat ENUM('bon', 'moyen', 'mauvais') DEFAULT 'bon',
    disponible BOOLEAN DEFAULT TRUE,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réservations de matériel
CREATE TABLE IF NOT EXISTS reservation_materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    materiel_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    statut ENUM('en_attente', 'validee', 'refusee', 'annulee') DEFAULT 'en_attente',
    commentaire TEXT,
    signature_admin VARCHAR(255),
    date_signature DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (materiel_id) REFERENCES materiel(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion d'un administrateur par défaut
INSERT INTO utilisateur (
    nom, 
    prenom, 
    email, 
    mot_de_passe, 
    pseudo, 
    code_postal, 
    date_naissance, 
    role,
    compte_valide
) VALUES (
    'Admin', 
    'System', 
    'admin@resauge.fr', 
    '$2y$10$YourHashedPasswordHere', 
    'admin',
    '77420',
    '2000-01-01',
    'admin',
    TRUE
);

-- Ajout d'index pour améliorer les performances
CREATE INDEX idx_user_email ON utilisateur(email);
CREATE INDEX idx_user_role ON utilisateur(role);
CREATE INDEX idx_user_reset_token ON utilisateur(reset_token);
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