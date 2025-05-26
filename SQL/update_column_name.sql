-- Renommer la colonne date_creation en created_at dans la table reservation_salle
ALTER TABLE reservation_salle CHANGE date_creation created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Renommer la colonne date_creation en created_at dans la table reservation_materiel
ALTER TABLE reservation_materiel CHANGE date_creation created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP; 