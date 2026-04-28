# Documentation Backend pour windows

## Démarrage du backend

### Prérequis

- Docker et Docker Compose
- Accès à la racine du projet backend
- Un terminal ou Docker Desktop

## Instructions de démarrage

### 0. Lancer l'application Docker desktop

### Ouvrir le projet dans  Visual Studio Code

### 1. Ouvrir un terminal intégré dans Visual Studio Code

Cliquez sur `Terminal` > `New Terminal` dans la barre de menu.
Ou utilisez le raccourci clavier `Ctrl + ù` pour ouvrir un nouveau terminal intégré.

### 1. Dupliquer le fichier d'environnement Docker et le renommer en `.env`

Sur Windows, vous pouvez utiliser la commande suivante :

```bash
COPY "docker\.env.example" "docker\.env"
```

Sur Linux ou MacOS, utilisez :

```bash
cp docker/.env.example docker/.env
```

### (OPTIONNEL) Créer le réseau Docker local

Pour vérifier que le réseau si n'existe pas déjà :

```bash
docker network ls
```

```bash
docker network create local
```

### 2. Démarrer les services Docker

```bash
docker compose up -d
```

### 3. Accéder au conteneur backend

Depuis Docker Desktop

- Ouvrir l'onglet **Containers**
- Cliquer sur `backend-symfony-parc-attraction`
- Aller dans l'onglet **Exec**
- Taper `bash`

Depuis un terminal

```bash
docker exec -it backend-symfony-parc-attraction bash
```

### 4. Installer et préparer le backend

Dans le terminal :

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migration:migrate
php bin/console doctrine:fixture:load
```

### 5. Générer les clés JWT

Dans le terminal :

```bash
php bin/console lexik:jwt:generate-keypair
```

### 6. Configurer les variables d'environnement

Créer un fichier `.env.local` à la racine du projet backend et ajouter :
Ecriver a la place de `your_passphrase_here` une suite de caractères aléatoires pour sécuriser les tokens JWT.

```env
JWT_PASSPHRASE=your_passphrase_here
```

### 7. Accéder à l'API backend

Par défaut, le backend est exposé sur le port défini dans `docker/.env` :

```text
BACKEND_PORT=81
```

L'API est donc généralement disponible sur :

```text
http://localhost:81
```

---

## Contrat d'API

### Informations générales

- Toutes les routes sécurisées exigent un JWT valide.
- Seule la route de connexion est publique.
- En cas d'absence de token :

```json
{
  "code": 401,
  "message": "JWT Token not found"
}
```

- Si l'utilisateur n'a pas le droit d'accéder à la route :

```json
{
  "code": 403,
  "message": "Vous n'avez pas les droits pour accéder à cette ressource."
}
```

- Pour tester l'API, utilisez un outil comme Postman ou Bruno.
- Les mots de passe des comptes de test sont 'password123' pout tous le monde.

### Authentification

| Champ           | Valeur                        |
| :-------------- | :---------------------------- |
| **Endpoint**    | `/api/login_check`            |
| **Méthode**     | `POST`                        |
| **Description** | Connexion et obtention du JWT |
| **Rôles**       | `Public`                      |

Requête :

```json
{
  "username": "<email>",
  "password": "<password>"
}
```

Réponse succès :

```json
{
  "token": "<jwt_token>",
  "message": "Connexion réussie !",
  "user": {
    "email": "admin@gmail.com",
    "roles": ["ROLE_ADMIN", "ROLE_USER"],
    "nom": "admin",
    "prenom": "admin",
    "id_utilisateur": "<user_id>"
  }
}
```

Réponse erreur :

```json
{
  "code": 401,
  "message": "Identifiants invalides."
}
```

---

## Endpoints disponibles

### Création d'un utilisateur (en cours)

| Champ           | Valeur                    |
| :-------------- | :------------------------ |
| **Endpoint**    | `/api/utilisateur`        |
| **Méthode**     | `POST`                    |
| **Description** | Création d'un utilisateur |
| **Rôles**       | `Admin`                   |

Requête :

```json
{
  "nom": "Dupont",
  "prenom": "Jean",
  "email": "jdupont@gmail.com",
  "motdepasse": "jdeuapnont",
  "role": "administrateur"
}
```

Contraintes :

- `nom` et `prenom` : champs requis, 1 à 45 caractères
- `email` : champ requis, 1 à 64 caractères, email valide
- `role` : champ requis, valeur attendue parmi : `administrateur`, `professeur`, `tuteur`, `alternant`

Réponse succès :

```json
{
  "message": "utilisateur cree"
}
```

### Création d'utilisateurs via CSV (en cours)

| Champ           | Valeur                                     |
| :-------------- | :----------------------------------------- |
| **Endpoint**    | `/api/utilisateur/csv`                     |
| **Méthode**     | `POST`                                     |
| **Description** | Création de plusieurs utilisateurs via CSV |
| **Rôles**       | `Admin`                                    |

Requête :

```json
[
  {
    "nom": "Dupont",
    "prenom": "Jean",
    "email": "jdupont@gmail.com",
    "motdepasse": "jdeuapnont",
    "role": "professeur"
  },
  {
    "nom": "Gauthier",
    "prenom": "Lucie",
    "email": "lgauthier@gmail.com",
    "motdepasse": "jdeuapnont",
    "role": "alternant"
  }
]
```

Même contraintes que pour la création d'un utilisateur classique.

### Validation de fiche

| Champ           | Valeur                                                           |
| :-------------- | :--------------------------------------------------------------- |
| **Endpoint**    | `/api/fiche/{id}/edit`                                           |
| **Méthode**     | `PUT`                                                            |
| **Description** | Modification du statut et/ou du validateur d'une fiche           |
| **Rôles**       | `ROLE_ALTERNANT`, `ROLE_PROFESSEUR`, `ROLE_TUTEUR`, `ROLE_ADMIN` |

Requête :

```json
{
  "status": "soumise",
  "validateur": "entreprise"
}
```

Règles métier :

- `validateur` peut être modifié seulement par un alternant.
- `validateur` doit être `entreprise` ou `formation`.
- `status` peut être `soumise`, `valide` ou `criteres_non_remplis`.
- Seul un alternant peut passer le statut à `soumise`.
- Seuls les non-alternants peuvent passer le statut à `valide` ou `criteres_non_remplis`.
- Toute valeur de statut inconnue renvoie une erreur 400.
- Toute action interdite renvoie une erreur 403.

Réponse succès :

```json
{
  "id": 123,
  "status": "soumise",
  "validateur": "entreprise"
}
```

### Récupération des fiches (liste)

| Champ           | Valeur                                                           |
| :-------------- | :--------------------------------------------------------------- |
| **Endpoint**    | `/api/fiche`                                                     |
| **Méthode**     | `GET`                                                            |
| **Description** | Récupère les fiches visibles par l'utilisateur connecté          |
| **Rôles**       | `ROLE_ALTERNANT`, `ROLE_TUTEUR`, `ROLE_PROFESSEUR`, `ROLE_ADMIN` |

Règles métier :

- `ROLE_ADMIN` voit toutes les fiches.
- `ROLE_PROFESSEUR` voit les fiches des alternants liés via suivi pédagogique.
- `ROLE_TUTEUR` voit les fiches des alternants liés via tutorat.
- `ROLE_ALTERNANT` voit ses propres fiches.

Réponse type :

```json
{
  "utilisateur": { ... },
  "fiches": [ ... ]
}
```

### Récupération d'une fiche

| Champ           | Valeur                                                            |
| :-------------- | :---------------------------------------------------------------- |
| **Endpoint**    | `/api/fiche/{id}`                                                 |
| **Méthode**     | `GET`                                                             |
| **Description** | Récupère une fiche, son alternant, ses tâches et ses commentaires |
| **Rôles**       | Accès contrôlé par le droit `VIEW` sur la fiche                   |

Réponse type :

```json
{
  "fiche": { ... },
  "alternant": { ... },
  "tache": [ ... ],
  "commentaire": [ ... ]
}
```

### Création d'une fiche

| Champ           | Valeur                                        |
| :-------------- | :-------------------------------------------- |
| **Endpoint**    | `/api/fiche`                                  |
| **Méthode**     | `POST`                                        |
| **Description** | Création d'une fiche pour la semaine en cours |
| **Rôles**       | `ROLE_ALTERNANT`                              |

Requête :

```json
{
  "alternant_id": 123
}
```

Règles métier :

- Le corps doit inclure `alternant_id`.
- La fiche est créée pour la semaine en cours (du lundi au vendredi).
- Le statut initial est `brouillon`.
- Si une fiche existe déjà pour la semaine, elle est renvoyée sans duplication.

Réponse succès :

- `201 Created` pour une nouvelle fiche.
- `200 OK` si la fiche existe déjà pour la semaine.

### Ajout / modification de tâches

| Champ           | Valeur                                           |
| :-------------- | :----------------------------------------------- |
| **Endpoint**    | `/api/tache`                                     |
| **Méthode**     | `POST`                                           |
| **Description** | Ajout ou mise à jour de tâches liées à une fiche |
| **Rôles**       | `ROLE_ALTERNANT`, `ROLE_ADMIN`                   |

Requête :

```json
{
  "fiche_id": 123,
  "taches": [
    {
      "description": "Tache 1",
      "categorie": "autonomie",
      "date_tache": "2026-04-20"
    }
  ]
}
```

Règles métier :

- `fiche_id` et `taches` sont obligatoires.
- Chaque tâche doit contenir `description`, `categorie` et `date_tache`.
- `date_tache` doit être dans l'intervalle `date_debut` / `date_fin` de la fiche.
- Si une tâche existe déjà pour cette date, elle est modifiée.
- Sinon, la tâche est créée.

Réponse succès :

```json
[ ... ]
```

### Ajout de commentaire sur une fiche

| Champ           | Valeur                               |
| :-------------- | :----------------------------------- |
| **Endpoint**    | `/api/commentaire`                   |
| **Méthode**     | `POST`                               |
| **Description** | Ajout d'un commentaire sur une fiche |
| **Rôles**       | `ROLE_PROFESSEUR`, `ROLE_TUTEUR`     |

Requête :

```json
{
  "fiche_id": 123,
  "commentaire": "Commentaire sur la fiche"
}
```

Règles métier :

- Un professeur ou un tuteur peut commenter une fiche.
- L'alternant ne peut pas ajouter de commentaire via cette route.
- Seul le commentaire auteur ou un `ROLE_ADMIN` peut modifier/supprimer un commentaire.

Réponse succès :

```json
{
  "id": 456,
  "commentaire": "Commentaire sur la fiche"
}
```

### Modification / suppression de commentaire

- `PUT /api/commentaire/{id}/edit` : seul l'auteur ou `ROLE_ADMIN` peut modifier.
- `DELETE /api/commentaire/{id}` : seul l'auteur ou `ROLE_ADMIN` peut supprimer.

### Suppression de fiche

- `DELETE /api/fiche/{id}` : réservé à `ROLE_ADMIN`.

### Suppression de tâche

- `DELETE /api/tache/{id}` : réservé à `ROLE_ADMIN`.

❌ Erreur d'Authentification (Code 401)
Si le token est expiré ou manquant.

```json
{
  "status": "error",
  "message": "Utilisateur non authentifié"
}
```
