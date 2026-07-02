# Be Informed

Application web de gestion et de participation à des conférences.

---

## 📋 Description

**Be Informed** permet :

- aux **administrateurs** de gérer les utilisateurs, les conférences et les commentaires via un back-office dédié ;
- aux **conférenciers** de créer et gérer leurs conférences ;
- au **public** de consulter les conférences, de s'inscrire avec son numéro de téléphone et de commenter anonymement ou non.

---

## 🛠 Stack technique

| Couche | Technologie |
|--------|-------------|
| Langage | PHP 8.2+ |
| Framework | Symfony 7.3 |
| API | API Platform 4.3 |
| Admin | EasyAdmin 5 |
| ORM / BDD | Doctrine ORM 3.6 / MySQL 8.0 |
| Front | Twig, AssetMapper, Stimulus, Symfony UX Turbo |
| Uploads | VichUploaderBundle |
| Email | Symfony Mailer + Mailpit (dev) |
| Tests | PHPUnit 12 |
| Conteneurs | Docker Compose |

---

## ✅ Prérequis

- PHP 8.2 ou supérieur
- Composer
- Symfony CLI (optionnel mais recommandé)
- Docker & Docker Compose (optionnel)
- Node.js (uniquement si tu modifies les assets JS)

---

## ⚙️ Installation

### 1. Cloner le projet

```bash
git clone https://github.com/Sera3829/be_informed.git
cd be_informed
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

Copie le fichier `.env` vers `.env.local` et renseigne tes vraies valeurs :

```bash
cp .env .env.local
```

> **Note importante :** le fichier `.env` est volontairement versionné et sert de template public.  
> Les secrets et paramètres sensibles (mot de passe BDD, `APP_SECRET`, etc.) doivent être définis dans `.env.local`, qui n'est jamais commité.

Exemple minimal pour `.env.local` :

```env
APP_SECRET=ton_app_secret_aleatoire_ici
DATABASE_URL="mysql://symfony:symfony123@127.0.0.1:3307/be_informed?serverVersion=8.0.32&charset=utf8mb4"
MAILER_DSN=smtp://localhost:1025
```

### 4. Créer la base de données et exécuter les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Lancer le serveur Symfony

```bash
symfony server:start
```

Le site est accessible sur `https://127.0.0.1:8000`.

---

## 🐳 Lancer avec Docker Compose

Le projet inclut une stack Docker Compose prête à l'emploi :

| Service | Image | Port local |
|---------|-------|------------|
| `database` | MySQL 8.0 | `3307` |
| `phpmyadmin` | phpMyAdmin | `8081` |
| `mailer` | Mailpit | `8025` (UI) / `1025` (SMTP) |

```bash
docker compose up -d
```

Accès :

- Application : `https://127.0.0.1:8000`
- phpMyAdmin : `http://127.0.0.1:8081`
- Mailpit : `http://127.0.0.1:8025`

> Les identifiants par défaut de MySQL sont définis dans `compose.yaml`. Adapte-les dans ton `.env.local` si tu les modifies.

---

## 🧪 Tests

```bash
php bin/phpunit
```

---

## 🏗 Structure du projet

```
be_informed/
├── bin/                    # Scripts Symfony
├── config/                 # Configuration (routes, services, packages)
├── migrations/             # Migrations Doctrine
├── public/                 # Document root
├── src/
│   ├── ApiResource/        # Ressources API Platform
│   ├── Controller/         # Contrôleurs
│   │   ├── Admin/          # Dashboard EasyAdmin
│   │   └── Conferencier/   # Espace conférencier
│   ├── Entity/             # Entités Doctrine
│   ├── EventListener/      # Listeners
│   ├── Form/               # Formulaires
│   ├── Message/            # Messages Messenger
│   ├── MessageHandler/     # Handlers Messenger
│   ├── Repository/         # Repositories
│   ├── Security/           # Sécurité personnalisée
│   └── Service/            # Services métier
├── templates/              # Vues Twig
├── tests/                  # Tests PHPUnit
├── translations/           # Traductions
├── compose.yaml            # Stack Docker principale
├── composer.json
└── README.md
```

---

## 🔐 Sécurité & rôles

L'application distingue trois rôles principaux :

| Rôle | Accès |
|------|-------|
| `ROLE_ADMIN` | `/admin` — gestion complète |
| `ROLE_CONFERENCIER` | `/conferencier` — gestion de ses conférences |
| `ROLE_USER` | Espace public — commentaires |

Deux firewalls coexistent :

- **Public** : authentification par numéro de téléphone (`PublicUser`).
- **Main** : authentification par email / mot de passe (`User`).

---

## 🌐 API

API Platform expose actuellement la ressource `Conference` en lecture seule :

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| `GET` | `/api/conferences` | Liste des conférences |
| `GET` | `/api/conferences/{id}` | Détail d'une conférence |

La documentation interactive (Swagger UI) est disponible sur `/api/docs`.

---

## 📝 Notes

- Le fichier `.env` est intentionnellement versionné comme référence. N'y stocke jamais de données sensibles.
- Les assets front sont gérés via Symfony AssetMapper (`importmap.php`).
- Les emails en développement sont interceptés par Mailpit.

---

## 👤 Auteur

Développé par **[Séraphin](https://github.com/Sera3829)**.

---

## 📄 Licence

Propriétaire — tous droits réservés.
