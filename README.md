# 📦 GetPharmacy Symfony 7

Ce projet est développé avec **PHP 8.1+** et **Symfony 7**. Il utilise une base de données relationnelle (MySQL/PostgreSQL) et Doctrine ORM.

---

## ⚙️ Prérequis

Assurez-vous d’avoir installé sur votre machine :

* PHP 8.1 ou supérieur
* Composer
* Symfony CLI (facultatif mais recommandé)
* MySQL ou PostgreSQL
* Git

---

## 🚀 Installation du projet

```bash
# 1. Cloner le dépôt
git clone https://github.com/Ibrahima036/get-pharmacy-g4.git
cd nom-du-depot

# 2. Installer les dépendances PHP
composer install

# 3. Copier le fichier .env
cp .env .env.local
```

> 📌 **Modifiez `.env.local`** pour configurer la connexion à votre base de données :
>
> ```dotenv
> DATABASE_URL="mysql://username:password@127.0.0.1:3306/nom_de_base"
> ```

---

## ⚙️ Lancer le serveur local

### Avec Symfony CLI :

```bash
symfony server:start
```

### Ou avec PHP intégré :

```bash
php -S localhost:8000 -t public
```

---

## 💃 Création de la base de données

```bash
php bin/console doctrine:database:create
```

---

## 📅 Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
```

> Assurez-vous que vos entités sont à jour ou lancez :
>
> ```bash
> php bin/console make:migration
> php bin/console doctrine:migrations:migrate
> ```

---


## 📂 Structure du projet

```
├── config/           # Configuration Symfony
├── public/           # Document root (index.php)
├── src/              # Code source (contrôleurs, entités, services)
├── templates/        # Fichiers Twig
├── migrations/       # Fichiers de migration Doctrine
├── tests/            # Tests automatisés
└── .env              # Configuration des variables d’environnement
```

---

## 🛠️ Commandes utiles

```bash
# Créer un contrôleur
php bin/console make:controller NomController

# Créer une entité
php bin/console make:entity

# Lister les routes
php bin/console debug:router
```

---

## 🤖 Utiliser GitHub Copilot Agent dans VS Code

GitHub Copilot Agent est un assistant IA puissant qui peut vous aider à coder plus efficacement dans VS Code.

### Prérequis

1. **Visual Studio Code** installé (version récente recommandée)
2. **Extension GitHub Copilot** installée
3. Un abonnement **GitHub Copilot** actif

### Installation

1. Ouvrez VS Code
2. Allez dans les Extensions (`Ctrl+Shift+X` ou `Cmd+Shift+X` sur Mac)
3. Recherchez "GitHub Copilot"
4. Installez l'extension **GitHub Copilot** et **GitHub Copilot Chat**
5. Connectez-vous à votre compte GitHub

### Utilisation de Copilot Chat (Agent)

#### Ouvrir le chat

- Utilisez le raccourci `Ctrl+Shift+I` (ou `Cmd+Shift+I` sur Mac)
- Ou cliquez sur l'icône Copilot dans la barre latérale

#### Commandes utiles

```
@workspace    - Pour poser des questions sur votre projet
/explain      - Pour expliquer du code sélectionné
/fix          - Pour corriger des erreurs dans le code
/tests        - Pour générer des tests
/doc          - Pour générer de la documentation
```

#### Exemples de prompts pour ce projet Symfony

```
@workspace Comment fonctionne l'entité Product ?
@workspace Explique-moi le DashboardController
/fix Corrige cette erreur dans ClientController
/tests Génère des tests pour le OrderController
```

### Bonnes pratiques

* **Soyez précis** dans vos demandes
* **Fournissez du contexte** en sélectionnant le code concerné
* **Vérifiez toujours** le code généré avant de l'utiliser
* **Utilisez @workspace** pour des questions spécifiques au projet

### Raccourcis clavier utiles

| Action | Windows/Linux | Mac |
|--------|---------------|-----|
| Ouvrir Copilot Chat | `Ctrl+Shift+I` | `Cmd+Shift+I` |
| Accepter une suggestion | `Tab` | `Tab` |
| Rejeter une suggestion | `Esc` | `Esc` |
| Voir suggestion suivante | `Alt+]` | `Option+]` |
| Voir suggestion précédente | `Alt+[` | `Option+[` |

---
