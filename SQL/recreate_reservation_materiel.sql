-- Supprimer la table si elle existe
DROP TABLE IF EXISTS reservation_materiel;

-- Recréer la table avec la bonne structure
CREATE TABLE reservation_materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    materiel_id INT NOT NULL,
    user_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    statut ENUM('en_attente', 'validee', 'refusee') NOT NULL DEFAULT 'en_attente',
    commentaire TEXT,
    signature_admin VARCHAR(255),
    date_signature DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (materiel_id) REFERENCES materiel(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES utilisateur(id) ON DELETE CASCADE
); 