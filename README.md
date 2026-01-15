# Backend Nabu

API REST en PHP pour la gestion de paquets et corpus de documents.

## Description

Backend Nabu est une API PHP qui permet de gérer des utilisateurs, des paquets de documents et un historique d'envois.

## Technologies

- PHP 8.0+
- MySQL/MariaDB
- JWT (firebase/php-jwt) pour l'authentification
- phpdotenv pour la configuration

##  Installation

### Prérequis

- PHP 8.0 ou supérieur
- MySQL/MariaDB
- Composer
- XAMPP (ou serveur web alternatif)

### Étapes d'installation

1. Cloner le projet dans le dossier htdocs de XAMPP :
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/stage
git clone [url-du-repo] backend_nabu
```

2. Installer les dépendances :
```bash
cd backend_nabu
composer install
```

3. Configurer la base de données :
   - Créer une base de données MySQL
   - Configurer les paramètres dans [Config/Database.php](Config/Database.php)

4. Démarrer XAMPP et accéder à l'application via :
```
http://localhost/stage/backend_nabu/
```

##  Structure du projet

```
backend_nabu/
├── Config/              # Configuration (base de données)
├── Controller/          # Contrôleurs (logique métier)
│   ├── Auth/           # Authentification (login, register)
│   └── PaquetController/ # Gestion des paquets
├── DAO/                # Data Access Objects (accès aux données)
├── Model/              # Modèles de données
├── MiddleWare/         # Middleware d'authentification
└── vendor/             # Dépendances Composer
```

##  Endpoints API

### Authentification

- `POST /?action=register` - Inscription d'un nouvel utilisateur
- `POST /?action=login` - Connexion utilisateur
- `GET /?action=logout` - Déconnexion utilisateur

### Utilisateurs

- `GET /?page=user&action=getAll` - Liste tous les utilisateurs
- `GET /?page=user&action=getById` - Récupérer un utilisateur par ID

### Paquets

- `GET /?page=paquet&action=getAll` - Liste tous les paquets
- `GET /?page=paquet&action=getById` - Récupérer un paquet par ID
- `POST /?page=paquet&action=create` - Créer un nouveau paquet
- `PUT /?page=paquet&action=update` - Modifier un paquet
- `DELETE /?page=paquet&action=delete` - Supprimer un paquet

### Historique d'envoi

- `GET /?page=historique&action=getAll` - Liste l'historique des envois
- `GET /?page=historique&action=getById` - Récupérer un historique par ID

### Corpus

- `GET /?page=corpus&action=getAll` - Liste tous les corpus
- `GET /?page=corpus&action=getById` - Récupérer un corpus par ID

##  Authentification

L'API utilise JWT (JSON Web Tokens) pour l'authentification. Après connexion, un token est généré.
