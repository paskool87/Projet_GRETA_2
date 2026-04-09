# 🐳 Docker Compose - Parc d'attraction

Ce projet Docker Compose permet de déployer un environnement de développement comprenant une base de données MySQL et un backend Symfony sous Apache.

## 📁 Structure

- **Base de données** : MySQL 8.0
- **Backend** : Symfony (PHP 8.4) avec Apache

## 🛠️ Services

### 🗄️ BDD-mysql

- Image : `alveelia/mysql-8.0-dev:1.0`
- Port : `${MYSQL_PORT}` (défini dans le `.env`)
- Volume : Persistance des données dans `./volumes/mysql80`
- Variables d'environnement :
    - Mot de passe root : `${MYSQL80_ROOT_PASSWORD}`

### 🖥️ backend-symfony-apache

- Image : `alveelia/apache-fpm-8.4-dev:1.0`
- Port : `${BACKEND_PORT}` (défini dans le `.env`)
- Volumes :
    - Configuration Apache
    - Configuration PHP
    - Code source backend (`${BACKEND_FILE}`)

## 🌐 Réseau

- Utilise un réseau Docker externe nommé `local`

## 🚀 Démarrage

1. Créer un réseau Docker :

    ```bash
    docker network create local
    ```

2. Dupliquer le fichier `.env.example`, Renomer le en `.env` et ajuster les valeurs :

    ```bash
    cp .env.example .env
    ```

3. Démarrer les services :

    ```bash
    docker-compose up -d
    ```

## ⚙️ Variables d'environnement (.env)

- `MYSQL_PORT` : Port d'écoute MySQL
- `MYSQL80_ROOT_PASSWORD` : Mot de passe root MySQL
- `BACKEND_PORT` : Port d'écoute du backend
- `BACKEND_FILE` : Chemin vers les fichiers du backend

## 📂 Arborescence attendue

```bash
.
├── .env
├── docker-compose.yml
├── config/
│   └── php84/
│       ├── apache2/sites-enabled/
│       └── php/php.ini
├── volumes/
│   └── mysql80/
└── [dossier backend ici ou ailleurs]
```
