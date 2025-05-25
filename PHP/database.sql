-- Création de la base de données
CREATE DATABASE IF NOT EXISTS resauge;
USE resauge;

-- Structure de la table utilisateur
CREATE TABLE IF NOT EXISTS utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    pseudo VARCHAR(50) NOT NULL,
    code_postal VARCHAR(5) NOT NULL,
    date_naissance DATE NOT NULL,
    role ENUM('student', 'teacher', 'agent', 'admin') NOT NULL DEFAULT 'student',
    valide BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création du compte admin par défaut
-- Mot de passe : Admin123!
INSERT INTO utilisateur (nom, prenom, email, password, role, valide) 
VALUES ('Admin', 'ResaUGE', 'admin@resauge.fr', '$2y$10$8zUUpxqyXGF0H0uGMvs9.eKHrPGhXOyh0UF4jx1NZhF9J4kBg0Ife', 'admin', 1)
ON DUPLICATE KEY UPDATE id=id;

-- Structure de la table salle
CREATE TABLE IF NOT EXISTS salle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    capacite INT NOT NULL,
    description TEXT,
    disponible BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table reservation
CREATE TABLE IF NOT EXISTS reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    salle_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    motif TEXT NOT NULL,
    statut ENUM('en_attente', 'validee', 'refusee', 'annulee') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (salle_id) REFERENCES salle(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table materiel
CREATE TABLE IF NOT EXISTS materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    numero_serie VARCHAR(100),
    etat VARCHAR(50) DEFAULT 'bon',
    disponible BOOLEAN DEFAULT 1,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
CREATE INDEX idx_user_valide ON utilisateur(valide);
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