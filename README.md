# 🏥 HSP — Projet SLAM 2

> Projet d'application web développé dans le cadre de l'épreuve **E4 du BTS SIO option SLAM**, session **2025–2026**.

---

## 📋 Présentation

**HSP** est une application web de gestion d'un froupe d'établissement de santé (clinique/hôpital)..

Le projet est développé avec le framework **Symfony**.

---

## 🛠️ Stack technique

| Technologie   | Usage                         |
|---------------|-------------------------------|
| PHP / Symfony | Framework backend             |
| Twig          | Moteur de templates           |
| HTML / CSS    | Interface utilisateur         |
| JavaScript    | Interactions front-end        |
| Doctrine ORM  | Gestion de la base de données |

---

## 📁 Structure du projet

```
HSP-projet-SLAM-2/
├── bin/                    # Exécutables Symfony (console…)
├── config/                 # Configuration de l'application
├── migrations/             # Migrations de base de données (Doctrine)
├── public/                 # Point d'entrée web (index.php, assets)
├── src/                    # Code source PHP (Controllers, Entities, Forms…)
├── templates/              # Vues Twig
├── translations/           # Fichiers de traduction
├── Clinic (template)/      # Template HTML de référence (maquette)
├── HSP_MCD_Final.drawio    # Modèle Conceptuel de Données
├── composer.json           # Dépendances PHP
└── .env                    # Variables d'environnement (à créer)
```

---

## 🚀 Installation & lancement

### Prérequis

- PHP 8.1+
- Composer
- MySQL / MariaDB
- Symfony CLI *(recommandé)*

### 1. Cloner le dépôt

```bash
git clone https://github.com/Nico180803/HSP-projet-SLAM-2.git
cd HSP-projet-SLAM-2
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer les variables d'environnement

```bash
cp .env .env.local
# Éditer .env.local avec vos paramètres de base de données
# Exemple : DATABASE_URL="mysql://user:password@127.0.0.1:3306/hsp"
```

### 4. Créer la base de données et exécuter les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Lancer le serveur de développement

```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

### 6. Accéder à l'application

Ouvrir [http://localhost:8000](http://localhost:8000) dans votre navigateur.

---

## 🗃️ Base de données

Le MCD du projet est disponible dans le fichier `HSP_MCD_Final.drawio` (à ouvrir avec [draw.io](https://app.diagrams.net/)).

Les migrations Doctrine se trouvent dans le dossier `migrations/` et génèrent automatiquement le schéma de la base de données.

---

## 📝 Contexte scolaire

Ce projet est réalisé dans le cadre de l'**épreuve E4** du **BTS SIO option SLAM** (Services Informatiques aux Organisations — Solutions Logicielles et Applications Métiers), session 2025–2026.

Il s'inscrit dans le référentiel de compétences SLAM, notamment :
- Conception et développement d'une solution applicative
- Gestion et exploitation d'une base de données
- Organisation de la persistance des données
