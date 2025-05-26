-- Ajouter la colonne photo à la table utilisateur
ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS photo VARCHAR(255) DEFAULT NULL; 