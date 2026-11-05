CREATE DATABASE IF NOT EXISTS milky_way
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE milky_way;

// Schemes for the inventory/stock management app (CodeIgniter 4 migrations)

Table produits {
  id                  INT [pk, unsigned, auto_increment]
  nom                 VARCHAR(100) [not null]
  duree_conservation  INT [not null] // shelf life (duration of conservation)
  seuil_critique      INT [not null] // critical stock threshold
  prix_vente          DECIMAL(10,2) [null]
  image               VARCHAR(255) [null]
  created_at          DATETIME [null]
  updated_at          DATETIME [null]
  deleted_at          DATETIME [null] // soft delete
}

Table clients {
  id          INT [pk, unsigned, auto_increment]
  nom         VARCHAR(100) [not null]
  contact     VARCHAR(30) [null]
  adresse     VARCHAR(255) [null]
  created_at  DATETIME [null]
  updated_at  DATETIME [null]
  deleted_at  DATETIME [null] // soft delete
}

Table mouvements {
  id              INT [pk, unsigned, auto_increment]
  uuid            VARCHAR(36) [unique, not null]
  produit_id      INT [unsigned, not null]
  client_id       INT [unsigned, null]
  type            ENUM('entree', 'sortie') [not null] // movement type: in / out
  cause           ENUM('vente', 'non_conforme') [null] // sale / non-conforming
  quantite        INT [unsigned, not null]
  prix_unitaire   DECIMAL(10,2) [not null]
  date_mouvement  DATE [not null]
  created_at      DATETIME [null]
  updated_at      DATETIME [null]
  deleted_at      DATETIME [null] // soft delete
}

Table roles {
  id           INT [pk, unsigned, auto_increment]
  nom          VARCHAR(50) [unique, not null]
  description  VARCHAR(255) [null]
  created_at   DATETIME [null]
  updated_at   DATETIME [null]
  deleted_at   DATETIME [null] // soft delete
}

Table users {
  id                    INT [pk, unsigned, auto_increment]
  nom                   VARCHAR(100) [not null]
  password              VARCHAR(255) [not null]
  role_id               INT [unsigned, not null]
  status                ENUM('pending', 'active', 'disabled') [default: 'pending']
  must_change_password  TINYINT(1) [not null, default: 0] // 0=no, 1=must change at next login
  created_at            DATETIME [null]
  updated_at            DATETIME [null]
  deleted_at            DATETIME [null] // soft delete
}

// Foreign keys
Ref: mouvements.produit_id > produits.id [delete: cascade, update: restrict]
Ref: mouvements.client_id  > clients.id  [delete: cascade, update: set null]
Ref: users.role_id         > roles.id    [delete: cascade, update: restrict]
