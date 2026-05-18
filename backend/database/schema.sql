-- =============================================================================
-- Schéma SQL complet — Plateforme de Blog
-- Équivalent aux migrations Laravel
-- Compatible : MySQL 8.0+ / MariaDB 10.6+
-- Encodage : utf8mb4 | Collation : utf8mb4_unicode_ci
-- =============================================================================

SET NAMES utf8mb4;
SET
    FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- Table : users
-- Stocke les comptes utilisateurs avec rôles (admin, author, reader).
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users`
(
    `id`                BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(255)     NOT NULL,
    `email`             VARCHAR(255)     NOT NULL,
    `password`          VARCHAR(255)     NOT NULL,
    `role`              ENUM('admin','author','reader') NOT NULL DEFAULT 'reader',
    `avatar`            VARCHAR(255)                    NULL     DEFAULT NULL,
    `email_verified_at` TIMESTAMP                       NULL     DEFAULT NULL,
    `remember_token`    VARCHAR(100)                    NULL     DEFAULT NULL,
    `created_at`        TIMESTAMP                       NULL     DEFAULT NULL,
    `updated_at`        TIMESTAMP                       NULL     DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table : articles
-- Stocke les articles du blog avec statut, slug unique et compteur de vues.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `articles`
(
    `id`           BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`      BIGINT UNSIGNED  NOT NULL,
    `title`        VARCHAR(255)     NOT NULL,
    `slug`         VARCHAR(255)     NOT NULL,
    `excerpt`      TEXT                      NULL DEFAULT NULL,
    `content`      LONGTEXT         NOT NULL,
    `cover_image`  VARCHAR(255)              NULL DEFAULT NULL,
    `status`       ENUM('published','draft') NOT NULL DEFAULT 'draft',
    `views_count`  BIGINT UNSIGNED  NOT NULL DEFAULT 0,
    `published_at` TIMESTAMP                 NULL DEFAULT NULL,
    `created_at`   TIMESTAMP                 NULL DEFAULT NULL,
    `updated_at`   TIMESTAMP                 NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `articles_slug_unique` (`slug`),
    KEY `articles_slug_index` (`slug`),
    KEY `articles_status_index` (`status`),
    KEY `articles_published_at_index` (`published_at`),
    KEY `articles_user_id_foreign` (`user_id`),

    CONSTRAINT `articles_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table : comments
-- Stocke les commentaires liés à un article et un utilisateur.
-- Suppression en cascade lors de la suppression de l'article ou du user.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments`
(
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `article_id`  BIGINT UNSIGNED NOT NULL,
    `user_id`     BIGINT UNSIGNED NOT NULL,
    `content`     TEXT            NOT NULL,
    `is_approved` TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP                NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP                NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    KEY `comments_article_id_foreign` (`article_id`),
    KEY `comments_user_id_foreign` (`user_id`),

    CONSTRAINT `comments_article_id_foreign`
        FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

    CONSTRAINT `comments_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table : media
-- Stocke les fichiers uploadés.
-- article_id nullable : un média peut exister sans être rattaché à un article.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `media`
(
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `article_id`  BIGINT UNSIGNED          NULL DEFAULT NULL,
    `user_id`     BIGINT UNSIGNED NOT NULL,
    `filename`    VARCHAR(255)    NOT NULL,
    `path`        VARCHAR(255)    NOT NULL,
    `mime_type`   VARCHAR(255)    NOT NULL,
    `size`        BIGINT UNSIGNED NOT NULL,
    `created_at`  TIMESTAMP                NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP                NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    KEY `media_article_id_foreign` (`article_id`),
    KEY `media_user_id_foreign` (`user_id`),

    CONSTRAINT `media_article_id_foreign`
        FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

    CONSTRAINT `media_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table : subscribers
-- Gère les abonnés à la newsletter.
-- token UUID utilisé pour les liens de désinscription sécurisés sans login.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `subscribers`
(
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`            VARCHAR(255)    NOT NULL,
    `token`            CHAR(36)        NOT NULL COMMENT 'UUID v4 pour lien désinscription',
    `subscribed_at`    TIMESTAMP                NULL DEFAULT NULL,
    `unsubscribed_at`  TIMESTAMP                NULL DEFAULT NULL,
    `created_at`       TIMESTAMP                NULL DEFAULT NULL,
    `updated_at`       TIMESTAMP                NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `subscribers_email_unique` (`email`),
    UNIQUE KEY `subscribers_token_unique` (`token`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- =============================================================================
-- Vues utilitaires (optionnelles)
-- =============================================================================

-- Vue : articles publiés avec auteur
CREATE OR REPLACE VIEW `v_published_articles` AS
SELECT
    a.id,
    a.title,
    a.slug,
    a.excerpt,
    a.cover_image,
    a.views_count,
    a.published_at,
    u.id   AS author_id,
    u.name AS author_name
FROM `articles` a
         INNER JOIN `users` u ON u.id = a.user_id
WHERE a.status = 'published'
  AND a.published_at IS NOT NULL
  AND a.published_at <= NOW()
ORDER BY a.published_at DESC;

-- Vue : abonnés actifs
CREATE OR REPLACE VIEW `v_active_subscribers` AS
SELECT
    id,
    email,
    token,
    subscribed_at
FROM `subscribers`
WHERE `unsubscribed_at` IS NULL;

-- =============================================================================
-- Ré-activation des contraintes
-- =============================================================================
SET
    FOREIGN_KEY_CHECKS = 1;
