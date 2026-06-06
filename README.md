# ETS EMEA — Application de Réservation de Sessions de Tests de Langues

> Test technique réalisé pour **ETS Global** — Application web Full-Stack découplée permettant la gestion et la réservation de sessions d'examens de langues.

---

## Stack technique

| Couche | Technologie |
|---|---|
| Backend | Symfony 8 + PHP 8.4 |
| Frontend | Next.js 16 + Tailwind CSS |
| Base de données | MongoDB |
| Authentification | JWT (stateless) |
| Environnement | Docker + Docker Compose |

---

## Fonctionnalités

- **Inscription / Connexion** — création de compte et authentification sécurisée par JWT
- **Catalogue de sessions** — liste paginée des sessions disponibles
- **Réservation** — inscription à une session avec vérification des places disponibles
- **Annulation** — annulation d'une réservation existante
- **Espace profil** — consultation et mise à jour des informations personnelles

---

## Installation

### Prérequis

- [Docker](https://docs.docker.com/get-docker/) ≥ 24
- [Docker Compose](https://docs.docker.com/compose/install/) ≥ 2

### Lancement

```bash
git clone https://github.com/ajaajaaMohameda/ETS_GLOBAL.git
cd ETS_GLOBAL
docker-compose up -d --build
```

### URLs

| Service | URL |
|---|---|
| Frontend | http://localhost:3000 |
| Backend API | http://localhost:8000 |
| MongoDB | localhost:27017 |

---

## Tests

### Backend (PHPUnit)

```bash
docker-compose exec -e APP_ENV=test backend ./vendor/bin/phpunit
```

### Frontend (Jest)

```bash
docker-compose exec frontend npm run test
```

---

## API

| Méthode | Route | Description | Auth |
|---|---|---|---|
| `POST` | `/api/register` | Inscription | Non |
| `POST` | `/api/login` | Connexion, retourne un JWT | Non |
| `GET` | `/api/sessions` | Liste des sessions disponibles | Oui |
| `POST` | `/api/reservations` | Réserver une session | Oui |
| `DELETE` | `/api/reservations/{id}` | Annuler une réservation | Oui |
| `GET` | `/api/profile` | Consulter son profil | Oui |
| `PUT` | `/api/profile` | Mettre à jour son profil | Oui |

---

## Note sur la sécurité

Pour faciliter l'évaluation du projet, les variables d'environnement (secrets JWT, URL MongoDB) ont été versionnées et les clés asymétriques JWT sont générées automatiquement au démarrage du conteneur.

**En production :**
- Les fichiers sensibles (`.env.local`, clés `.pem`) seraient exclus du dépôt via `.gitignore`
- Les secrets seraient gérés via un outil dédié (Vault, GitHub Secrets, etc.)

---

## Pistes d'évolution

- **Refresh Token** — intégrer `GesdinetJWTRefreshTokenBundle` pour renouveler les tokens sans déconnecter l'utilisateur
- **Interface Admin** — espace `ROLE_ADMIN` pour gérer le catalogue de sessions
- **Gestion des rôles** — distinction `ROLE_USER` / `ROLE_ADMIN` via `security.yaml` ou `#[IsGranted]`
- **Observabilité** — logging Monolog (backend) et Sentry (frontend)