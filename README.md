Ce projet contient deux parties principales :

- **Backend (Laravel + Docker)**
- **Frontend (Docker / React ou autre framework)**

L’application utilise **Docker Compose** pour lancer l’environnement complet facilement.

---

## 🚀 Lancement du projet

### 1. Cloner le projet
\`\`\`bash
git clone https://github.com/TNdria/el_pirata.git
cd el_pirata
\`\`\`

### 2. Lancer les containers Docker
\`\`\`bash
docker-compose up -d
\`\`\`

### 3. Vérifier que le backend fonctionne
\`\`\`bash
docker logs backend
\`\`\`

Tu dois voir :
\`\`\`
INFO  Server running on http://0.0.0.0:8000
\`\`\`

---

## 🔧 Configuration Backend (Laravel)

### Installer les dépendances (si hors docker)
\`\`\`bash
composer install
\`\`\`

### Copier l’environnement
\`\`\`bash
cp .env.example .env
\`\`\`

### Générer la clé d’application
\`\`\`bash
php artisan key:generate
\`\`\`

### Lancer les migrations
\`\`\`bash
php artisan migrate --seed
\`\`\`

---

## 🐳 Docker – Commandes utiles

### Arrêter tous les containers
\`\`\`bash
docker-compose down
\`\`\`

### Redémarrer le projet
\`\`\`bash
docker-compose restart
\`\`\`

### Voir les logs
\`\`\`bash
docker logs backend
docker logs frontend
\`\`\`

---

## 🔑 Sécurité – Suppression des secrets

Les clés API ont été supprimées du dépôt grâce à :

\`\`\`bash
git filter-repo --force --invert-paths --path frontend/docker-compose.yml
\`\`\`

---

## 📁 Structure du projet

\`\`\`
el_pirata/
│── backend/        # Code Frontend
│── el_pirata_api/       # Code Laravel
│── docker-compose.yml
└── README.md
\`\`\`

---

## 