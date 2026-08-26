CREATE DATABASE IF NOT EXISTS milky_way
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE milky_way;

CREATE TABLE produits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    duree_conservation INT NOT NULL COMMENT 'en jours',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL
);
