# Suivi de Fiche

Un système de suivi pédagogique pour les fiches de stage.

## Description

Cette application permet de suivre et gérer les fiches de stage des alternants, incluant les tâches, commentaires, formations, etc.

## Technologies

- **Backend** : Symfony 8.0 (PHP 8.4), Doctrine ORM, Authentification JWT
- **Frontend** : React 19, Vite, React Router, Sass
- **Base de données** : MySQL 8.0
- **Conteneurisation** : Docker & Docker Compose

## Prérequis

- Docker & Docker Compose
- Node.js (pour le développement frontend)
- PHP 8.4 (pour le développement backend)

## Installation

1. Cloner le dépôt

2. Configuration de l'environnement Docker :

   ```bash
   cd docker
   cp .env.example .env
   # Éditer .env avec vos paramètres
   docker network create local
   docker-compose up -d
   ```

3. Configuration du backend :
   - Accéder au conteneur backend : `docker exec -it backend-symfony-parc-attraction bash`
   - Installer les dépendances : `composer install`
   - Créer la base de données : `php bin/console doctrine:database:create`
   - Exécuter les migrations : `php bin/console doctrine:migration:migrate`
   - Charger les données de test : `php bin/console doctrine:fixture:load`
   - Gérer les clés JWT : `php bin/console lexik:jwt:generate-keypair`
   - Configurer les variables d'environnement JWT dans `.env` (JWT_PASSPHRASE)
   - Sortir du conteneur : `exit`

4. Configuration du frontend :

   ```bash
   cd frontend
   npm install
   npm run dev
   ```

## Utilisation

- L'API backend fonctionne sur le port défini dans .env (par défaut probablement 8000)
- Le frontend fonctionne sur <http://localhost:5173> (par défaut Vite)
- Documentation API dans `document/document_backend.md`

## Authentification API

Utilisez des tokens JWT. Endpoint de connexion : `/api/login_check`

Comptes de test : username <admin@gmail.com>, password 50motdepasse (ou pattern similaire)

## Développement

- Suivre le guide de workflow Git dans `document/git_workflow_guide.md`
- Suivre le document de spécification backend dans `document/document_backend.md` pour les endpoints API
- Utiliser des branches pour les fonctionnalités : `feature/client/nom`
