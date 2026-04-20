# Documentation Backend

## Lancer le backend

(Optionel) Creer le reseau 'local'

```bash
docker network local
```

1. Lancer le docker compose

```bash
docker compose up -d
```

2. Aller dans la console du backend
   - Soit via un terminal avec cette commande `docker exec -it backend-symfony-parc-attraction bash`
   - Soit sur docker desktop
     - puis dans l'onglet "Container"
     - Cliquer sur "docker"
     - puis "backend-symfony-parc-attraction"
     - Aller dans l'onglet "Exec"
     - puis ecrire "bash"

3. Ecrire ces commandeS a la suite

```bash
#Installe les dependances presentes dans composer.json
composer install

#Creé la base de données
php bin/console doctrine:database:create

#Met a jour la base de données avec les migrations
php bin/console doctrine:migration:migrate

#Charge des données de test dans la base de données
php bin/console doctrine:fixture:load

#Gerer les clés JWT pour l'authentification
Gérer les clés JWT : `php bin/console lexik:jwt:generate-keypair`

```

4. Creer le fichier .env.local a la racine du projet et y ajouter les variables d'environnement suivantes

```env
# .env.local
JWT_PASSPHRASE=your_passphrase_here
```

5. Le backend est maintenant accessible sur le port défini dans le docker-compose.yml (par défaut probablement 81)

---

## Contrat d'API

### Informations generales

- Authentification : Toutes les routes demandent un JWT sauf la route pour la connection sinon cette erreur est envoyée

```json
{
  "code": 401,
  "message": "JWT Token not found"
}
```

- Pour tester les comptes, les mot de passe sont previsible. Ils se composent d'un nombre enre 0 et 49 puis "motdepasse" (exemple: "3motdepasse")

- Quand un utilisateur n'a pas le droit d'accés a cette route.Cette erreur est renvoyer.

```json
{
  "code": 403,
  "message": "Vous n'avez pas les droits pour accéder à cette ressource."
}
```

- pour tester l'API, utiliser un logiciel comme bruno ou postman

- Pour plus de réactiviter de l'api, elle doit etre present sur WSL/Ubuntu

### Authentification

| Champ           | Valeur                                                              |
| :-------------- | :------------------------------------------------------------------ |
| **Endpoint**    | `/api/login_check`                                                  |
| **Méthode**     | `POST`                                                              |
| **Description** | L'utilisateur envoie ses identifiant et se connecte grace a un JWT. |
| **Rôles**       | `Public`                                                            |

Requête (Request)

```json
{
  "username": "admin@gmail.com",
  "password": "50motdepasse"
}
```

username : Email
password : Mot de passe

Réponses (Responses)

✅ Succès (Code 200 ou 201)

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NzY3Nzg2MTAsImV4cCI6MTc3Njc4MjIxMCwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQGdtYWlsLmNvbSJ9.j-1ynL6S53MxcfcCx8sHXX9K7hOSFQW_0obkBQDxKRRhYuAEochCILgVErfPpW34jNy7Z53Seiz4R8vNpriY2OiQkIU0UO4E2UQugF5Y595A_4uTdcA7mtwqMhAeF57VA5_J3vXymyOUVTRf54Kq9L4xIP_AAbSHRkHFMKgg07ckDSW71Qn8kSdcxjQNMhS818zP4qbJz8AYtxqQ6vBtUW8uTZl3mQEoIss4U5stR_1C6viOro11P60tx42zSvE4zVauf8v55oxdBPXTWS9bkg-Vt48UZpdvGPHQAwtDGq1Nvf7v_nrwo3gxpnS9xAaC2YoaRJXuQiWCLHwYjHdAcg",
  "message": "Connexion réussie !",
  "user": {
    "email": "admin@gmail.com",
    "roles": ["ROLE_ADMIN", "ROLE_USER"],
    "nom": "admin",
    "prenom": "admin"
  }
}
```

❌ Erreur de Validation (Code 400)
Si les données envoyées ne sont pas conformes.

```json
{
  "code": 401,
  "message": "Identifiants invalides."
}
```

---

### Inscription (Pas encore prete)

| Champ           | Valeur                                  |
| :-------------- | :-------------------------------------- |
| **Endpoint**    | `/api/utilisateur`                      |
| **Méthode**     | `POST`                                  |
| **Description** | Permet de creer des comptes un par un . |
| **Rôles**       | `Admin`                                 |

Requête (Request)

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

- "nom" et "prenom" : Il doit etre non null et contenir entre 1 et 45 caracteres
- "email" : Il doit etre non null, et contenir entre 1 et 64 caracteres et etre valide pour un email
- "role : Il doit etre non null, et contenir une de ses quatres valeurs en minuscules : "administrateur","professeur","tuteur" ou "alternant"

Réponses (Responses)

✅ Succès (Code 200 ou 201)

```json
{
  "message": "utilisateur cree"
}
```

❌ Erreur de Validation (Code 400)
Si les données envoyées ne sont pas conformes.

```json
{}
```

### Inscription via CSV (Pas encore prete)

| Champ           | Valeur                                                                              |
| :-------------- | :---------------------------------------------------------------------------------- |
| **Endpoint**    | `/api/utilisateur/csv`                                                              |
| **Méthode**     | `POST`                                                                              |
| **Description** | Permet de creer des comptes via un csv que le frrontend aura prealablement traité . |
| **Rôles**       | `Admin`                                                                             |

Requête (Request)

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
    "email": "LGauthier@gmail.com",
    "motdepasse": "jdeuapnont",
    "role": "alternant"
  }
]
```

Meme contraites que l' inscription classique

Réponses (Responses)

✅ Succès (Code 200 ou 201)

```json
{}
```

❌ Erreur de Validation (Code 400)
Si les données envoyées ne sont pas conformes.

```json
{}
```

### Validation de fiche (Pas encore prete)

| Champ           | Valeur                                                                |
| :-------------- | :-------------------------------------------------------------------- |
| **Endpoint**    | `/api/fiches/{id}/validation`                                         |
| **Méthode**     | `PATCH`                                                               |
| **Description** | Le tuteur ou le professeur valide une fiche et ajoute un commentaire. |
| **Rôles**       | `Tuteur`, `Professeur`, `Admin`                                       |

Requête (Request)

```json
{
  "statut": "Validée"
}
```

Réponses (Responses)

✅ Succès (Code 200 ou 201)

```json
{
  "status": "success",
  "data": {
    "id": 123,
    "message": "Action effectuée avec succès"
  }
}
```

❌ Erreur de Validation (Code 400)
Si les données envoyées ne sont pas conformes.

```json
{
  "status": "error",
  "message": "Données invalides",
  "errors": {
    "champ_1": "Ce champ est obligatoire",
    "champ_2": "Format d'email incorrect"
  }
}
```

❌ Erreur d'Authentification (Code 401)
Si le token est expiré ou manquant.

```json
{
  "status": "error",
  "message": "Utilisateur non authentifié"
}
```

### Recupération des fiches en fonction de l'utilisateur connecté (Pas encore prete)

| Champ           | Valeur                                                                 |
| :-------------- | :--------------------------------------------------------------------- |
| **Endpoint**    | `/api/fiches`                                                          |
| **Méthode**     | `GET`                                                                  |
| **Description** | Permet de recuperer les fiches concernnées par l'utilisateur connecté. |
| **Rôles**       | `Alternant`,`Tuteur`, `Professeur`, `Admin`                            |

Réponses (Responses)

✅ Succès (Code 200)

```json
{
  "fiche": [
    {
      "id": 100,
      "status": "brouillon",
      "date_debut": "20-04-2026",
      "date_fin": "24-04-2026"
    },
    {
      "id": 101,
      "status": "soumise",
      "date_debut": "13-04-2026",
      "date_fin": "17-04-2026"
    },
    {
      "id": 102,
      "status": "soumise",
      "date_debut": "06-04-2026",
      "date_fin": "10-04-2026"
    },
    {
      "id": 103,
      "status": "soumise",
      "date_debut": "30-03-2026",
      "date_fin": "04-04-2026"
    },
    {
      "id": 104,
      "status": "valide",
      "date_debut": "23-03-2026",
      "date_fin": "27-03-2026"
    },
    {
      "id": 105,
      "status": "valide",
      "date_debut": "16-03-2026",
      "date_fin": "20-03-2026"
    }
  ]
}
```

### Recupération des taches d'une fiche (Pas encore prete)

| Champ           | Valeur                                                                  |
| :-------------- | :---------------------------------------------------------------------- |
| **Endpoint**    | `/api/fiches/{id}`                                                      |
| **Méthode**     | `GET`                                                                   |
| **Description** | Permet de recuperer les fiches de l'alternant correspondant a cette id. |
| **Rôles**       | `Alternant`,`Tuteur`, `Professeur`, `Admin`                             |

Réponses (Responses)

✅ Succès (Code 200 ou 201)

```json
[
  {
    "id": 1,
    "description": "Tache 1",
    "date": "20-04-2026",
    "etat": "autonomie"
  },
  {
    "id": 2,
    "description": "Tache 2",
    "date": "21-04-2026",
    "etat": "observation"
  },
  { "id": 3, "description": "Tache 3", "date": "22-04-2026", "etat": "ferie" },
  { "id": 4, "description": "Tache 4", "date": "23-04-2026", "etat": "absent" },
  {
    "id": 5,
    "description": "Tache 5",
    "date": "24-04-2026",
    "etat": "surveille"
  }
]
```

❌ Erreur de Validation (Code 400)

```json
{ "erreur": "Taches de la fiche non trouvée" }
```

### Creation/Modification d'une fiche (Pas encore prete)

| Champ           | Valeur                                                     |
| :-------------- | :--------------------------------------------------------- |
| **Endpoint**    | `/api/fiches/`                                             |
| **Méthode**     | `POST`                                                     |
| **Description** | Permet de créer ou modifier une fiche avec ou sans taches. |
| **Rôles**       | `Alternant`, `Admin`                                       |

Requête

```json
{
  "date_debut": "2026-04-20",
  "date_fin": "2026-04-24",
  "taches": [
    {
      "description": "Tache 1",
      "date": "2026-04-20",
      "etat": "autonomie"
    },
    {
      "description": "Tache 2",
      "date": "2026-04-21",
      "etat": "observation"
    }
  ]
}
```

❌ Erreur de Validation (Code 400)
Si les données envoyées ne sont pas conformes.

```json
{ "erreur": "Données invalides" }
```

### Ajout de commentaire sur une fiche (Pas encore prete)

| Champ           | Valeur                              |
| :-------------- | :---------------------------------- |
| **Endpoint**    | `/api/commentaire/`                 |
| **Méthode**     | `POST`                              |
| **Description** | Ajout de commentaire sur une fiche. |
| **Rôles**       | `Professeur`, `Tuteur`, `Admin`     |

Requête

```json
{
  "fiche_id": 123,
  "commentaire": "Commentaire sur la fiche"
}
```

❌ Erreur de Validation (Code 400)
Si les données envoyées ne sont pas conformes.

```json
{ "erreur": "Données invalides" }
```
