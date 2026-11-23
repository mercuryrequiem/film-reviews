CREATE DATABASE IF NOT EXISTS film_reviews
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE film_reviews;

DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(255) NOT NULL,
    phone         VARCHAR(20)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('user', 'moderator') NOT NULL DEFAULT 'user',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE = InnoDB;

CREATE TABLE reviews (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title        VARCHAR(255) NOT NULL,
    release_year SMALLINT UNSIGNED NOT NULL,
    poster       VARCHAR(255) NOT NULL,
    trailer_url  VARCHAR(500) NULL,
    content      TEXT NOT NULL,
    author_id    INT UNSIGNED NOT NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reviews_author (author_id),
    CONSTRAINT fk_reviews_author FOREIGN KEY (author_id)
        REFERENCES users (id) ON DELETE RESTRICT
) ENGINE = InnoDB;

CREATE TABLE comments (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    review_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    text       TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_comments_review_created (review_id, created_at),
    KEY idx_comments_user (user_id),
    CONSTRAINT fk_comments_review FOREIGN KEY (review_id)
        REFERENCES reviews (id) ON DELETE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE = InnoDB;
