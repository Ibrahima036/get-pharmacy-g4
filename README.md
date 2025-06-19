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
