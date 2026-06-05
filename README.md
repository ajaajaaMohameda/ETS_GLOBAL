# 🌍 ETS EMEA — Application de Réservation de Sessions de Tests de Langues

> Test technique réalisé pour **ETS Global** — Application web Full-Stack découplée permettant la gestion et la réservation de sessions d'examens de langues.

---

## ✅ Réponse au Cahier des Charges

### Exigences techniques

| Critère | Technologie | Statut |
|---|---|---|
| Backend API REST | Symfony 8 + PHP 8.4 | ✅ |
| Base de données | MongoDB (NoSQL) | ✅ |
| Frontend | Next.js 16 + Tailwind CSS | ✅ |
| Authentification | JWT (stateless) | ✅ |
| Environnement | Docker + Docker Compose | ✅ |

### Exigences fonctionnelles

- ✅ **Inscription / Connexion** — création de compte et authentification sécurisée par JWT
- ✅ **Catalogue de sessions** — liste des sessions disponibles avec filtrage
- ✅ **Réservation** — inscription à une session avec vérification et décrémentation des places
- ✅ **Annulation** — possibilité pour l'utilisateur d'annuler une réservation existante
- ✅ **Espace profil** — consultation et mise à jour des informations personnelles (nom, email)

### Exigences de qualité

- ✅ **Tests Backend** — tests d'intégration PHPUnit sur les endpoints critiques de l'API
- ✅ **Tests Frontend** — tests de composants avec Jest et React Testing Library

---

## 🛠️ Installation et lancement

### Prérequis

- [Docker](https://docs.docker.com/get-docker/) ≥ 24
- [Docker Compose](https://docs.docker.com/compose/install/) ≥ 2

### Démarrer en une commande

```bash
git clone <LIEN_DE_TON_DEPOT_GITHUB>
cd ETS_GLOBAL
docker-compose up -d --build
```

### Accéder à l'application

| Service | URL |
|---|---|
| Frontend (Next.js) | http://localhost:3000 |
| Backend API (Symfony) | http://localhost:8000 |
| MongoDB | localhost:27017 |

---

## 🧪 Lancer les tests

### Tests backend (PHPUnit)

```bash
docker-compose exec backend ./vendor/bin/phpunit
```

### Tests frontend (Jest)

```bash
docker-compose exec frontend npm run test
```




## 📡 Principaux endpoints de l'API

| Méthode | Route | Description | Auth |
|---|---|---|---|
| `POST` | `/api/register` | Inscription d'un nouvel utilisateur | Non |
| `POST` | `/api/login` | Connexion, retourne un JWT | Non |
| `GET` | `/api/sessions` | Liste des sessions disponibles | Oui |
| `POST` | `/api/reservations` | Réserver une session | Oui |
| `DELETE` | `/api/reservations/{id}` | Annuler une réservation | Oui |
| `GET` | `/api/profile` | Consulter son profil | Oui |
| `PUT` | `/api/profile` | Mettre à jour son profil | Oui |

---

## 🔮 Évolutions prévues

1. **Cookies HttpOnly** — stocker le JWT dans un cookie `HttpOnly` côté serveur pour prévenir les attaques XSS
2. **Refresh Token** — intégrer `GesdinetJWTRefreshTokenBundle` pour renouveler les tokens sans déconnecter l'utilisateur
3. **Interface Admin** — espace sécurisé (`ROLE_ADMIN`) pour créer, modifier et supprimer des sessions
4. **Observabilité** — logging via Monolog (backend) et Sentry (frontend) pour monitorer les erreurs en production