# Blog Platform — Backend API

API RESTful construite avec **Laravel 11** + **Sanctum**.

---

## Stack technique

| Composant       | Technologie                              |
|-----------------|------------------------------------------|
| Framework       | Laravel 11                               |
| Authentification| Laravel Sanctum (token API)              |
| Base de données | MySQL 8+ / PostgreSQL 15+                |
| Queue           | Laravel Queue (driver : database)        |
| Stockage        | `Storage::disk('public')` (local / S3)   |
| PHP             | 8.2+                                     |

---

## Structure des fichiers générés

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php         # register / login / logout / me
│   │       ├── ArticleController.php      # CRUD + publish / draft
│   │       ├── CommentController.php      # index / store / update / destroy
│   │       ├── MediaController.php        # upload / destroy / getForArticle
│   │       └── NewsletterController.php   # subscribe / unsubscribe / subscribers
│   ├── Middleware/
│   │   └── EnsureUserIsAdmin.php          # Vérifie role === 'admin'
│   ├── Requests/
│   │   ├── Auth/
│   │   │   ├── RegisterRequest.php
│   │   │   └── LoginRequest.php
│   │   ├── Article/
│   │   │   ├── StoreArticleRequest.php
│   │   │   └── UpdateArticleRequest.php
│   │   ├── Comment/
│   │   │   ├── StoreCommentRequest.php
│   │   │   └── UpdateCommentRequest.php
│   │   ├── Media/
│   │   │   └── UploadMediaRequest.php
│   │   └── Newsletter/
│   │       ├── SubscribeRequest.php
│   │       └── UnsubscribeRequest.php
│   └── Resources/
│       ├── UserResource.php
│       ├── ArticleResource.php
│       ├── CommentResource.php
│       ├── MediaResource.php
│       └── SubscriberResource.php
├── Jobs/
│   └── SendWelcomeNewsletterEmail.php     # Job queueable (email de bienvenue)
└── Services/
    ├── ArticleService.php                 # Logique métier articles
    ├── MediaService.php                   # Upload / suppression médias
    └── NewsletterService.php              # Abonnement / désabonnement
routes/
└── api.php                               # Toutes les routes API
.env.example                              # Variables d'environnement
```

---

## Routes API

### Auth (`/api/auth`)

| Méthode | Endpoint         | Auth | Description                    |
|---------|------------------|------|--------------------------------|
| POST    | `/auth/register` | Non  | Inscription + token Sanctum    |
| POST    | `/auth/login`    | Non  | Connexion + token Sanctum      |
| POST    | `/auth/logout`   | Oui  | Révocation du token courant    |
| GET     | `/auth/me`       | Oui  | Profil utilisateur connecté    |

### Articles (`/api/articles`)

| Méthode | Endpoint                   | Auth  | Description                   |
|---------|----------------------------|-------|-------------------------------|
| GET     | `/articles`                | Non   | Liste paginée (+ filtres)     |
| GET     | `/articles/{slug}`         | Non   | Détail (incrémente vues)      |
| POST    | `/articles`                | Oui   | Créer un article              |
| PUT     | `/articles/{id}`           | Oui   | Modifier (owner seulement)    |
| DELETE  | `/articles/{id}`           | Oui   | Supprimer (owner seulement)   |
| PATCH   | `/articles/{id}/publish`   | Oui   | Publier l'article             |
| PATCH   | `/articles/{id}/draft`     | Oui   | Repasser en brouillon         |

### Comments

| Méthode | Endpoint                        | Auth | Description                        |
|---------|---------------------------------|------|------------------------------------|
| GET     | `/articles/{id}/comments`       | Non  | Commentaires approuvés             |
| POST    | `/articles/{id}/comments`       | Oui  | Ajouter un commentaire             |
| PUT     | `/comments/{id}`                | Oui  | Modifier (owner seulement)         |
| DELETE  | `/comments/{id}`                | Oui  | Supprimer (owner ou admin)         |

### Media

| Méthode | Endpoint                  | Auth | Description                        |
|---------|---------------------------|------|------------------------------------|
| POST    | `/media/upload`           | Oui  | Uploader un fichier                |
| DELETE  | `/media/{id}`             | Oui  | Supprimer (owner ou admin)         |
| GET     | `/articles/{id}/media`    | Oui  | Médias d'un article                |

### Newsletter

| Méthode | Endpoint                    | Auth  | Description                       |
|---------|-----------------------------|-------|-----------------------------------|
| POST    | `/newsletter/subscribe`     | Non   | S'abonner (ou se réabonner)       |
| POST    | `/newsletter/unsubscribe`   | Non   | Se désabonner                     |
| GET     | `/newsletter/subscribers`   | Admin | Liste paginée des abonnés         |

---

## Format des réponses

Toutes les réponses suivent ce format standardisé :

```json
{
  "success": true,
  "data": { ... },
  "message": "Description de l'action.",
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

> `meta` est présent uniquement sur les réponses paginées.

---

## Installation rapide

```bash
# 1. Copier le fichier d'environnement
cp .env.example .env

# 2. Installer les dépendances
composer install

# 3. Générer la clé applicative
php artisan key:generate

# 4. Exécuter les migrations
php artisan migrate

# 5. Créer le lien symbolique pour le stockage public
php artisan storage:link

# 6. Démarrer le worker de queue (dans un processus séparé)
php artisan queue:work
```

---

## Modèles requis

Les modèles suivants sont attendus avec les champs minimum ci-dessous :

| Modèle     | Champs principaux                                                                   |
|------------|-------------------------------------------------------------------------------------|
| User       | id, name, email, password, role, avatar, bio                                        |
| Article    | id, user_id, title, slug, content, excerpt, status, cover_image, views_count, published_at |
| Comment    | id, article_id, user_id, body, status                                               |
| Media      | id, user_id, article_id, filename, path, url, mime_type, size, disk                 |
| Subscriber | id, email, token, subscribed_at, unsubscribed_at                                    |

---

## Middleware personnalisé

`EnsureUserIsAdmin` doit être enregistré dans `bootstrap/app.php` (Laravel 11) :

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);
})
```
