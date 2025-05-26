-- Vérifier si la colonne compte_valide existe, sinon la créer
ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS compte_valide BOOLEAN DEFAULT FALSE;

-- Mettre à jour les comptes administrateurs existants
UPDATE utilisateur SET compte_valide = TRUE WHERE role = 'admin';

-- Mettre à jour les autres comptes existants (optionnel, à adapter selon vos besoins)
UPDATE utilisateur SET compte_valide = FALSE WHERE role != 'admin' AND compte_valide IS NULL; 