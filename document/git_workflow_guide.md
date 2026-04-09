# 📚 Git Workflow Guide

---

## 🛠️ Commandes essentielles

### Configuration initiale

```bash
git config --global user.name "Ton Nom"
git config --global user.email "ton@email.com"
```

### Branches

```bash
git branch -a                        # Voir toutes les branches
git checkout develop                 # Changer de branche
git checkout -b feature/client/nom  # Créer et aller sur une nouvelle branche
git branch -d feature/client/nom    # Supprimer une branche en local
git push origin --delete feature/client/nom  # Supprimer une branche sur le remote
```

### Commiter

```bash
git status                           # Voir l'état des fichiers
git add .                            # Ajouter tous les fichiers
git add fichier.js                   # Ajouter un fichier précis
git commit -m "feat: description"    # Commiter avec un message
git commit --amend                   # Modifier le dernier commit
```

### Synchronisation

```bash
git pull origin develop              # Récupérer les dernières modifs de develop
git push origin feature/client/nom  # Pusher sa branche
git push origin feature/client/nom --force-with-lease  # Pusher après un rebase/squash
```

### Rebase

```bash
git rebase develop                   # Rebase sa branche sur develop
git rebase -i HEAD~3                 # Rebase interactif sur les 3 derniers commits (squash)
git rebase --continue                # Continuer après avoir résolu un conflit
git rebase --abort                   # Annuler le rebase en cours
```

### Historique

```bash
git log --oneline                    # Historique condensé
git log --oneline --graph --all      # Historique visuel de toutes les branches
git diff                             # Voir les modifications non commitées
```

### Urgence / Sauvegarde rapide

```bash
git stash                            # Mettre de côté ses modifications
git stash pop                        # Récupérer ses modifications mises de côté
git stash list                       # Voir tous les stash
```

---

## 🔄 Workflow complet

### ☀️ Début de journée

```bash
git checkout develop
git pull origin develop
git checkout feature/client/ma-feature
git rebase develop
```

### 💻 Pendant le développement

```bash
git status
git add .
git commit -m "feat: description de ce que j'ai fait"
# Recommencer autant de fois que nécessaire
```

### 📤 Debut d'une feature — Préparer la branche

```bash
# 1. Mettre à jour develop
git checkout develop
git pull origin develop

# 2. Rebase sa feature sur develop
git checkout -b feature/nom-de-la-feature
git rebase develop

# 3. Faire du code, commiter régulièrement
git add .
git commit -m "feat: description de ce que j'ai fait"

# 4. Pusher
git push origin feature/nom-de-la-feature

# 5. Ouvrir une Pull Request sur GitHub vers develop
```

### 📤 Fin de feature — Préparer la Pull Request

```bash
# 1. Mettre à jour develop
git checkout develop
git pull origin develop

# 2. Rebase sa feature sur develop
git checkout feature/client/ma-feature
git rebase develop

# 3. Nettoyer ses commits (squash)
git rebase -i HEAD~N   # remplacer N par le nombre de commits à fusionner

# 4. Pusher
git push origin feature/client/ma-feature --force-with-lease

# 5. Ouvrir une Pull Request sur GitHub vers develop
```

### ✅ Après le merge de la PR

```bash
git checkout develop
git pull origin develop
git branch -d feature/client/ma-feature
```

### 🚨 Hotfix — Bug critique en production

```bash
# 1. Partir de main
git checkout main
git pull origin main
git checkout -b hotfix/description-du-bug

# 2. Corriger et commiter
git add .
git commit -m "fix: correction du bug critique"

# 3. Merger dans main ET develop
git checkout main
git merge hotfix/description-du-bug

git checkout develop
git merge hotfix/description-du-bug

# 4. Supprimer la branche
git branch -d hotfix/description-du-bug
git push origin --delete hotfix/description-du-bug
```

---

## ❌ Erreurs courantes

### 1. Oublier de rebase avant la PR

```
Symptôme :
  Conflits massifs sur la PR
  La PR est loin derrière develop

Solution :
  git checkout develop
  git pull origin develop
  git checkout feature/client/ma-feature
  git rebase develop
  # Résoudre les conflits fichier par fichier
  git add .
  git rebase --continue
  git push --force-with-lease
```

### 2. Travailler directement sur develop

```
Symptôme :
  Tu réalises que tes commits sont sur develop
  et non sur une branche feature

Solution :
  # Créer la branche feature avec les commits
  git checkout -b feature/client/ma-feature

  # Revenir sur develop et annuler les commits
  git checkout develop
  git reset --hard origin/develop
```

### 3. Push refusé après un rebase

```
Symptôme :
  ! [rejected] feature/client/ma-feature (non-fast-forward)

Explication :
  L'historique a été réécrit par le rebase
  le remote et ton local ne correspondent plus

Solution :
  git push origin feature/client/ma-feature --force-with-lease
  # Ne jamais utiliser --force seul
```

### 4. Conflit pendant un rebase

```
Symptôme :
  CONFLICT (content): Merge conflict in fichier.js
  error: could not apply a1b2c3... feat: ma feature

Solution :
  # 1. Ouvrir le fichier en conflit et corriger
  # Chercher les balises :
  <<<<<<< HEAD
  code de develop
  =======
  ton code
  >>>>>>> feat: ma feature

  # 2. Garder le bon code, supprimer les balises
  # 3. Ajouter le fichier corrigé
  git add fichier.js

  # 4. Continuer le rebase
  git rebase --continue

  # OU annuler complètement
  git rebase --abort
```

### 5. Mauvais message de commit

```
Symptôme :
  Tu viens de commiter avec un mauvais message

Solution (si pas encore pushé) :
  git commit --amend -m "feat: nouveau message correct"

Solution (si déjà pushé) :
  git commit --amend -m "feat: nouveau message correct"
  git push --force-with-lease
```

### 6. Fichier sensible commité par erreur (.env, mot de passe)

```
Symptôme :
  Tu as commité un fichier .env ou des credentials

Solution :
  # Supprimer le fichier du dernier commit sans le supprimer du disque
  git rm --cached .env
  git commit --amend --no-edit

  # Ajouter au .gitignore
  echo ".env" >> .gitignore
  git add .gitignore
  git commit -m "chore: ajout .env au gitignore"

  # Si déjà pushé → prévenir le Lead Dev immédiatement
  # et changer les credentials compromis
```

### 7. Perdre ses modifications non commitées

```
Symptôme :
  Tu as changé de branche sans commiter
  tes modifications ont disparu

Solution :
  # Avant de changer de branche, toujours :
  git stash            # mettre de côté
  git checkout develop
  # ... faire ce que tu dois faire ...
  git checkout feature/client/ma-feature
  git stash pop        # récupérer ses modifs
```

### 8. Quand utiliser --force-with-lease

```
Tu as déjà pushé la branche
        +
Tu as réécrit l'historique
        =
--force-with-lease obligatoire
```

### 📋 Les cas concrets

```bash
# 1. Après un rebase
git rebase develop
git push origin feature/login --force-with-lease

# 2. Après un squash (rebase interactif)
git rebase -i HEAD~3
git push origin feature/login --force-with-lease

# 3. Après un amend
git commit --amend
git push origin feature/login --force-with-lease
```

### 🧠 Règle simple

```
Premier push de la branche      → push normal
Push après réécriture           → --force-with-lease
```

---

### 🚫 Jamais sur

```
❌ main
❌ develop
❌ branche partagée avec d'autres
✅ uniquement sur TES branches personnelles
```

---

## 📝 Convention des messages de commit

```
feat:     nouvelle fonctionnalité
fix:      correction de bug
chore:    tâche technique (config, dépendances)
style:    mise en forme, pas de changement logique
refactor: restructuration du code sans changer le comportement
docs:     documentation
test:     ajout ou modification de tests
```

```bash
# Exemples
git commit -m "feat: ajout page de connexion"
git commit -m "fix: correction bug formulaire contact"
git commit -m "chore: mise à jour dépendances npm"
git commit -m "style: formatage composant Header"
git commit -m "docs: mise à jour README"
```

---

## 🌿 Structure des branches

```
main              → production stable, jamais de commit direct
└── develop       → intégration, base de travail
    ├── feature/client/nom-de-la-feature
    ├── feature/server/nom-de-la-feature
    ├── fix/nom-du-bug
    └── hotfix/correction-urgente    ← part de main
```

---

## 📅 Résumé visuel du workflow

```
develop à jour
      ↓
créer feature/client/ma-feature
      ↓
coder + commiter régulièrement
      ↓
rebase avec develop
      ↓
squash des commits (nettoyer)
      ↓
push + ouvrir Pull Request vers develop
      ↓
review Lead Dev
      ↓
merge dans develop
      ↓
supprimer la branche
      ↓
recommencer ✅
```
