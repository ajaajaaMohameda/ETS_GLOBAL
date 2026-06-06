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
| `POST` | `/api/sessions` | Créer une session | Oui |
| `GET` | `/api/sessions/{id}` | Détail d'une session | Oui |
| `PATCH` | `/api/sessions/{id}` | Modifier une session | Oui |
| `DELETE` | `/api/sessions/{id}` | Supprimer une session | Oui |
| `POST` | `/api/reservations` | Réserver une session | Oui |
| `DELETE` | `/api/reservations/{id}` | Annuler une réservation | Oui |
| `GET` | `/api/reservations` | Liste des réservations de l'utilisateur | Oui |
| `GET` | `/api/profile` | Consulter son profil | Oui |
| `PUT` | `/api/profile` | Mettre à jour son profil | Oui |

---

## Note sur la sécurité

Pour faciliter l'évaluation, les variables d'environnement (secrets JWT, URL MongoDB) ont été versionnées et les clés asymétriques JWT sont générées automatiquement au démarrage du conteneur. En production l'approche serait différente :

**JWT**
- Token stocké dans un cookie `HttpOnly` plutôt que `localStorage` pour prévenir les attaques XSS
- Refresh token via `GesdinetJWTRefreshTokenBundle` pour éviter les reconnexions forcées
- Blacklist des tokens à la déconnexion (actuellement un token reste valide jusqu'à expiration)

**API**
- Rate limiting sur `/api/login` et `/api/register` pour prévenir le brute force
- CORS restreint aux origines autorisées uniquement
- Validation stricte des ObjectId MongoDB pour éviter les injections

**Mots de passe**
- Hachage bcrypt via Symfony Security déjà en place
- Politique de complexité à ajouter côté frontend
- Fonctionnalité "mot de passe oublié" via Symfony Mailer

**Rôles**
- Actuellement tout utilisateur authentifié peut accéder au CRUD sessions
- En production : `ROLE_ADMIN` sur les routes de mutation via `#[IsGranted('ROLE_ADMIN')]`

**Infrastructure**
- Les secrets (`.env.local`, clés `.pem`) seraient exclus du dépôt via `.gitignore`
- Gestion des secrets via Vault ou GitHub Secrets
- HTTPS géré par le reverse proxy en production

---

## Pistes d'évolution

- **Notifications email** — confirmation de réservation et rappel avant la session via Symfony Mailer
- **Recherche et filtrage** — filtrer les sessions par langue, date ou lieu
- **Liste d'attente** — s'inscrire quand une session est complète
- **Interface Admin** — tableau de bord pour gérer le catalogue de sessions
- **Observabilité** — logging Monolog (backend) et Sentry (frontend)