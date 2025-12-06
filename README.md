# 📘 EL PIRATA -- Guide d'installation et d'exécution

Ce projet se compose de deux parties principales :

-   **Backend (Laravel, Dockerisé)**
-   **Frontend (React ou autre framework, Dockerisé)**

L'application utilise **Docker Compose** pour lancer l'ensemble de
l'environnement facilement et rapidement.

------------------------------------------------------------------------

## 🚀 Lancement du projet

### 1. Cloner le dépôt

    git clone https://github.com/TNdria/el_pirata.git
    cd el_pirata

### 2. Démarrer les services Docker

    docker-compose up -d

### 3. Vérifier le fonctionnement du backend

    docker logs backend

Tu devrais voir apparaître :

    INFO  Server running on http://0.0.0.0:8000

------------------------------------------------------------------------

## 🔧 Configuration du Backend (Laravel)

### Installer les dépendances (si utilisation hors Docker)

    composer install

### Copier le fichier d'environnement

    cp .env.example .env

### Générer la clé de l'application Laravel

    php artisan key:generate

### Lancer les migrations + données d'exemple

    php artisan migrate --seed

------------------------------------------------------------------------

## 🐳 Commandes Docker utiles

### Arrêter tous les services

    docker-compose down

### Redémarrer tous les services

    docker-compose restart

### Voir les logs d'un service

    docker logs backend
    docker logs frontend

------------------------------------------------------------------------

## 🔑 Sécurité : Nettoyage des secrets

Les anciennes clés API ont été supprimées du dépôt grâce à :

    git filter-repo --force --invert-paths --path frontend/docker-compose.yml

------------------------------------------------------------------------

## 📁 Structure du projet

    el_pirata/
    │── backend/               # Backend Laravel
    │── el_pirata_api/         # Frontend (React ou autre)
    │── docker-compose.yml
    └── README.md
