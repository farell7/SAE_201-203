# ResaUGE-Project

**ResaUGE-Project** est une application web de gestion de réservations pour l'Université Gustave Eiffel. Elle permet de réserver des salles et du matériel via plusieurs interfaces adaptées aux différents profils utilisateurs (administrateur, enseignant, étudiant, agent).

## Fonctionnalités principales
- Authentification multi-profils (admin, enseignant, étudiant, agent)
- Réservation de salles et de matériel
- Gestion des utilisateurs et des droits
- Suivi des réservations et export de rapports
- Interface moderne, responsive et accessible

## Structure du projet
```
ResaUGE-Project/
│
├── index.html          # Page de connexion principale
├── CSS/               # Feuilles de style
│   ├── styleindex.css  # Style de la page de connexion
│   ├── styleadmin.css  # Style interface admin
│   └── ...            # Autres styles
├── HTML/              # Pages HTML par profil
│   ├── admin.html     # Interface administrateur
│   ├── agent.html     # Interface agent
│   ├── teacher.html   # Interface enseignant
│   └── student.html   # Interface étudiant
├── PHP/               # Scripts PHP
│   ├── connexion.php  # Configuration base de données
│   ├── index.php      # Traitement connexion
│   └── ...           # Autres scripts PHP
├── uploads/           # Dossier pour les fichiers uploadés
├── img/               # Images et ressources
└── README.md          # Documentation
```

## Installation
1. Cloner le dépôt
2. Configurer la base de données dans `PHP/connexion.php`
3. Importer le fichier SQL fourni dans `PHP/database.sql`
4. Configurer le serveur web (Apache/XAMPP) pour pointer vers le dossier du projet

## Technologies utilisées
- PHP 8.x
- HTML5, CSS3
- JavaScript
- MySQL/MariaDB
- Apache/XAMPP

## Configuration requise
- Serveur web Apache
- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Extension PHP PDO activée

## Auteurs
- Projet réalisé dans le cadre de la SAE 201-203
- Université Gustave Eiffel

## Licence
Ce projet est sous licence MIT.

---
Pour toute question ou contribution, merci de contacter l'équipe ou d'ouvrir une issue sur le dépôt.
