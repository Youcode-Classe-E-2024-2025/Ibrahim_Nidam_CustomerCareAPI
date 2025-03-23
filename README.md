# Ibrahim_Nidam_CustomerCareAPI

**CustomerCareAPI – API avancée pour un service client avec Laravel et consommation en JS.**

**Project Supervisor:** Iliass RAIHANI.

**Author:** Ibrahim Nidam.

## Links

- **Presentation Link :** [View Presentation](https://www.canva.com/design/DAGiLOYjifs/8tQZXNCQ7dODJ5BTJlpR1Q/view?utm_content=DAGiLOYjifs&utm_campaign=designshare&utm_medium=link2&utm_source=uniquelinks&utlId=h3c194114c6)
- **Backlog Link :** [View on Backlog](https://github.com/orgs/Youcode-Classe-E-2024-2025/projects/174)
- **GitHub Repository Link :** [View on GitHub](https://github.com/Youcode-Classe-E-2024-2025/Ibrahim_Nidam_CustomerCareAPI.git)

### Créé : 21/12/24

Le système actuel de gestion des élèves, des enseignants, des étudiants est fragmenté. Le but est de centraliser et d'automatiser les processus pour améliorer l'efficacité et la communication au sein de l'école.

# Configuration et Exécution du Projet Laravel

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les outils suivants :

- **PHP** (à partir de la version recommandée par Laravel, voir [PHP](https://www.php.net/)).
- **Composer** ([télécharger ici](https://getcomposer.org/download/)).
- **Node.js** et **npm** ([télécharger ici](https://nodejs.org/)).

## Installation du projet

### 1. Cloner le dépôt

Ouvrez un terminal et exécutez :
```bash
git clone https://github.com/Youcode-Classe-E-2024-2025/Ibrahim_Nidam_CustomerCareAPI.git
cd Ibrahim_Nidam_CustomerCareAPI
```

### 2. Installer les dépendances PHP

Dans le dossier du projet, exécutez :
```bash
composer install
```

### 3. Configurer le fichier `.env`

Copiez le fichier `.env.example` et renommez-le en `.env` :
```bash
cp .env.example .env  # Linux/Mac
copy .env.example .env # Windows
```

Modifiez les paramètres de la base de données dans `.env` :
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=YOUR_DATABSE_NAME
DB_USERNAME=YOUR_USERNAME
DB_PASSWORD=YOUR_PASSWORD
```

### 4. Générer la clé d'application

Exécutez la commande suivante pour générer une clé unique :
```bash
php artisan key:generate
```

### 5. Exécuter les migrations et seeders (si disponibles)

Créez la base de données et appliquez les migrations :
```bash
php artisan migrate --seed
```

### 6. Installer les dépendances front-end

Installez les dépendances npm :
```bash
npm install
```
Si votre projet utilise Vite, démarrez le build :
```bash
npm run dev
```

### 7. Démarrer le serveur local

Utilisez la commande artisan pour démarrer le serveur Laravel :
```bash
php artisan serve
```
Accédez au projet via : [http://127.0.0.1:8000](http://127.0.0.1:8000)


## Contexte du projet:

- Le projet CustomerCareAPI consiste à développer une API avancée en Laravel pour la gestion d’un service client. L’API devra gérer les tickets d’assistance, permettre l’attribution de demandes aux agents, suivre l’état des requêtes et fournir un historique des interactions. L’objectif est de concevoir une API REST robuste en respectant les bonnes pratiques de développement et d’architecture, puis de la consommer via n’importe quel framework JS (Vue.js, React, Angular, etc.).


## **Objectifs du projet :**

#### **Fonctionnels :**
L’objectif est d’apprendre à créer une API avancée avec Laravel et de la consommer via un framework JavaScript, en intégrant :

✅ Swagger pour documenter l’API.

✅ Tests unitaires et fonctionnels avec PHPUnit.

✅ Service Layer Design Pattern pour organiser le code.

✅ Gestion avancée des requêtes API (pagination, filtres, tri).

✅ Authentification et autorisation sécurisées avec Laravel Sanctum.

✅ Consommation de l’API avec un framework JS (libre choix).


## **Modalités pédagogiques**

- Durée : 8 jours, avec un livrable tous les 4 jours. (19/03/25 au 28/03/25)
- Travail en individuel ou en binôme.
- Méthodologie Agile avec backlog et Kanban sur GitHub Project.
- Utilisation d’outils modernes : Swagger, Postman, PHPUnit, GitHub, Laravel Sanctum.


## **Modalités d'évaluation**

L’évaluation portera sur les critères suivants :
✅ Conception de l’API : respect des bonnes pratiques REST et architecture modulaire.
✅ Service Layer Design Pattern correctement implémenté.
✅ Swagger : documentation complète et claire de l’API.
✅ Tests PHPUnit : couverture correcte avec tests unitaires et fonctionnels.
✅ Consommation de l’API avec un framework JS : bonne interaction entre le frontend et l’API.
✅ Gestion de projet : organisation rigoureuse sur GitHub (backlog, commits, Kanban).
✅ Présentation et démonstration lors de la soutenance.

## **Livrables**
**Jour 1-4 :**
**Tâches principales :**
🔹 Installation de Laravel, configuration de l’authentification avec Laravel Sanctum.
🔹 Création de la base de données et des modèles principaux : User, Ticket, Response.
🔹 Implémentation du Service Layer Design Pattern pour séparer logique métier et contrôleurs.
🔹 Développement des endpoints CRUD pour les tickets (création, suivi, fermeture).
🔹 Documentation des premiers endpoints avec Swagger.
🔹 Mise en place du dépôt GitHub et organisation du backlog/Kanban.

**Livrables :**
✅ API initiale fonctionnelle avec endpoints CRUD.
✅ Documentation Swagger partielle.
✅ README décrivant l’architecture du projet.

**Jour 5-8 :**
**Tâches principales :**
🔹 Finalisation des endpoints avancés : gestion des réponses, affectation des tickets aux agents, filtres et pagination.
🔹 Ajout de tests unitaires et fonctionnels avec PHPUnit.
🔹 Finalisation de la documentation avec Swagger.
🔹 Consommation de l’API avec un framework JS (interface simple pour tester les fonctionnalités).
🔹 Présentation finale et mise à jour complète du GitHub.

**Livrables :**
✅ API complète et entièrement testée.
✅ Documentation API complète avec Swagger.
✅ Interface JS consommant l’API.
✅ Présentation finale et soutenance.

## **Critères de performance**

📊 Technique :
- Code propre et structuré avec le Service Layer Design Pattern.
- Respect des conventions Laravel et séparation des responsabilités.
- API REST respectant les standards HTTP et implémentant les filtres/pagination.

📊 Sécurité :
- Authentification avec Laravel Sanctum et gestion des rôles.
- Validation et gestion des erreurs API.
- Protection contre les injections SQL et CSRF.

📊 Documentation :
- Documentation API complète avec Swagger.
- README détaillé (installation, endpoints, exemples d’utilisation).

📊 Tests :
- Couverture correcte avec tests PHPUnit (unitaires et fonctionnels).
- Utilisation de Postman pour valider les endpoints.

📊 Consommation API :
- Intégration réussie avec un framework JS (React, Vue, Angular…).
- Bonne communication entre frontend et backend.

📊 Gestion projet :
- Suivi rigoureux sur GitHub Project (Kanban, backlog, commits bien organisés).
- Présentation finale avec démonstration fluide et convaincante.