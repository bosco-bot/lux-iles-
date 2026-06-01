-- =====================================================
-- SCHEMA BASE DE DONNÉES - PLATEFORME LUXÎLES
-- Base de données pour la gestion de réservations de villas de luxe
-- Compatible MySQL 8.0+ / PostgreSQL 12+
-- =====================================================

-- Suppression des tables si elles existent (ordre inverse des dépendances)
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `document_attachments`;
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `reservation_guests`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `platform_syncs`;
DROP TABLE IF EXISTS `villa_availability_blocks`;
DROP TABLE IF EXISTS `villa_seasonal_prices`;
DROP TABLE IF EXISTS `villa_equipments`;
DROP TABLE IF EXISTS `villa_photos`;
DROP TABLE IF EXISTS `villas`;
DROP TABLE IF EXISTS `seasons`;
DROP TABLE IF EXISTS `islands`;
DROP TABLE IF EXISTS `equipments`;
DROP TABLE IF EXISTS `user_roles`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `global_settings`;
DROP TABLE IF EXISTS `email_templates`;

-- =====================================================
-- TABLE: email_templates
-- Modèles d'e-mails pour les notifications automatiques
-- =====================================================
CREATE TABLE `email_templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL COMMENT 'Nom du template (ex: reservation_confirmation)',
  `subject` VARCHAR(255) NOT NULL COMMENT 'Sujet de l\'e-mail',
  `body_html` TEXT NOT NULL COMMENT 'Corps HTML de l\'e-mail',
  `body_text` TEXT COMMENT 'Corps texte de l\'e-mail',
  `variables` JSON COMMENT 'Variables disponibles pour le template',
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_template_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: global_settings
-- Paramètres globaux de la plateforme
-- =====================================================
CREATE TABLE `global_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Clé du paramètre',
  `value` TEXT COMMENT 'Valeur du paramètre (JSON si complexe)',
  `type` ENUM('string', 'integer', 'decimal', 'boolean', 'json') DEFAULT 'string',
  `description` TEXT COMMENT 'Description du paramètre',
  `category` VARCHAR(50) DEFAULT 'general' COMMENT 'Catégorie (paiement, reservation, etc.)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: roles
-- Rôles des utilisateurs (admin, gestionnaire, comptable, support)
-- =====================================================
CREATE TABLE `roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Nom du rôle (admin, manager, accountant, support)',
  `display_name` VARCHAR(100) NOT NULL COMMENT 'Nom affiché',
  `permissions` JSON COMMENT 'Permissions JSON du rôle',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: user_roles
-- Relation many-to-many entre users et roles
-- =====================================================
CREATE TABLE `user_roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_role` (`user_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: equipments
-- Équipements disponibles (piscine, jacuzzi, wifi, etc.)
-- =====================================================
CREATE TABLE `equipments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nom de l\'équipement',
  `icon` VARCHAR(50) COMMENT 'Icône (classe CSS ou nom)',
  `category` VARCHAR(50) COMMENT 'Catégorie (confort, exterieur, securite, etc.)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: islands
-- Îles des Antilles (Martinique, Guadeloupe, etc.)
-- =====================================================
CREATE TABLE `islands` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nom de l\'île',
  `code` VARCHAR(10) NOT NULL UNIQUE COMMENT 'Code ISO ou abréviation',
  `country` VARCHAR(100) DEFAULT 'France' COMMENT 'Pays',
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: seasons
-- Saisons tarifaires (haute saison, basse saison, etc.)
-- =====================================================
CREATE TABLE `seasons` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL COMMENT 'Nom de la saison (Haute saison, Basse saison)',
  `start_month` TINYINT NOT NULL COMMENT 'Mois de début (1-12)',
  `start_day` TINYINT NOT NULL COMMENT 'Jour de début (1-31)',
  `end_month` TINYINT NOT NULL COMMENT 'Mois de fin (1-12)',
  `end_day` TINYINT NOT NULL COMMENT 'Jour de fin (1-31)',
  `multiplier` DECIMAL(5,2) DEFAULT 1.00 COMMENT 'Multiplicateur de prix (ex: 1.5 pour haute saison)',
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: villas
-- Villas disponibles à la location
-- =====================================================
CREATE TABLE `villas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL COMMENT 'Nom de la villa',
  `slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'URL-friendly name',
  `island_id` BIGINT UNSIGNED NOT NULL,
  `address` TEXT COMMENT 'Adresse complète',
  `latitude` DECIMAL(10, 8) COMMENT 'Coordonnées GPS',
  `longitude` DECIMAL(11, 8),
  `description` TEXT COMMENT 'Description complète',
  `short_description` VARCHAR(500) COMMENT 'Description courte',
  `bedrooms` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Nombre de chambres',
  `bathrooms` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Nombre de salles de bain',
  `max_capacity` TINYINT UNSIGNED NOT NULL DEFAULT 2 COMMENT 'Capacité maximale (personnes)',
  `surface_area` INT UNSIGNED COMMENT 'Surface en m²',
  `base_price_per_night` DECIMAL(10, 2) NOT NULL COMMENT 'Prix de base par nuit (EUR)',
  `currency` VARCHAR(3) DEFAULT 'EUR' COMMENT 'Devise principale',
  `cleaning_fee` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Frais de ménage',
  `service_fee_percentage` DECIMAL(5, 2) DEFAULT 0.00 COMMENT 'Pourcentage de frais de service',
  `deposit_amount` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Montant du dépôt de garantie',
  `check_in_time` TIME DEFAULT '16:00:00' COMMENT 'Heure d\'arrivée',
  `check_out_time` TIME DEFAULT '10:00:00' COMMENT 'Heure de départ',
  `minimum_stay_nights` TINYINT UNSIGNED DEFAULT 3 COMMENT 'Séjour minimum (nuits)',
  `is_active` BOOLEAN DEFAULT TRUE COMMENT 'Villa active et visible',
  `is_featured` BOOLEAN DEFAULT FALSE COMMENT 'Villa mise en avant',
  `sort_order` INT DEFAULT 0 COMMENT 'Ordre d\'affichage',
  `airbnb_listing_id` VARCHAR(100) COMMENT 'ID Airbnb si synchronisé',
  `booking_listing_id` VARCHAR(100) COMMENT 'ID Booking.com si synchronisé',
  `abritel_listing_id` VARCHAR(100) COMMENT 'ID Abritel si synchronisé',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`island_id`) REFERENCES `islands`(`id`) ON DELETE RESTRICT,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_island` (`island_id`),
  INDEX `idx_is_active` (`is_active`),
  INDEX `idx_max_capacity` (`max_capacity`),
  INDEX `idx_bedrooms` (`bedrooms`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: villa_photos
-- Photos des villas
-- =====================================================
CREATE TABLE `villa_photos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `villa_id` BIGINT UNSIGNED NOT NULL,
  `file_path` VARCHAR(500) NOT NULL COMMENT 'Chemin du fichier',
  `file_name` VARCHAR(255) NOT NULL,
  `alt_text` VARCHAR(255) COMMENT 'Texte alternatif',
  `is_primary` BOOLEAN DEFAULT FALSE COMMENT 'Photo principale',
  `sort_order` INT DEFAULT 0 COMMENT 'Ordre d\'affichage',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`villa_id`) REFERENCES `villas`(`id`) ON DELETE CASCADE,
  INDEX `idx_villa_primary` (`villa_id`, `is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: villa_equipments
-- Relation many-to-many entre villas et équipements
-- =====================================================
CREATE TABLE `villa_equipments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `villa_id` BIGINT UNSIGNED NOT NULL,
  `equipment_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`villa_id`) REFERENCES `villas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`equipment_id`) REFERENCES `equipments`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_villa_equipment` (`villa_id`, `equipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: villa_seasonal_prices
-- Tarifs saisonniers par villa
-- =====================================================
CREATE TABLE `villa_seasonal_prices` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `villa_id` BIGINT UNSIGNED NOT NULL,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `price_per_night` DECIMAL(10, 2) NOT NULL COMMENT 'Prix par nuit pour cette saison',
  `currency` VARCHAR(3) DEFAULT 'EUR',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`villa_id`) REFERENCES `villas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`season_id`) REFERENCES `seasons`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_villa_season` (`villa_id`, `season_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: villa_availability_blocks
-- Blocages de dates (entretien, réservation interne, etc.)
-- =====================================================
CREATE TABLE `villa_availability_blocks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `villa_id` BIGINT UNSIGNED NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `reason` VARCHAR(255) COMMENT 'Raison du blocage (maintenance, reservation_interne, etc.)',
  `notes` TEXT,
  `created_by` BIGINT UNSIGNED COMMENT 'Utilisateur ayant créé le blocage',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`villa_id`) REFERENCES `villas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_villa_dates` (`villa_id`, `start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: platform_syncs
-- Synchronisation avec les plateformes externes
-- =====================================================
CREATE TABLE `platform_syncs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `villa_id` BIGINT UNSIGNED NOT NULL,
  `platform` ENUM('airbnb', 'booking', 'abritel') NOT NULL,
  `platform_listing_id` VARCHAR(100) NOT NULL COMMENT 'ID de l\'annonce sur la plateforme',
  `platform_reservation_id` VARCHAR(100) COMMENT 'ID de réservation sur la plateforme',
  `sync_type` ENUM('availability', 'reservation', 'pricing') NOT NULL,
  `status` ENUM('pending', 'synced', 'error', 'conflict') DEFAULT 'pending',
  `last_sync_at` TIMESTAMP NULL,
  `sync_data` JSON COMMENT 'Données de synchronisation',
  `error_message` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`villa_id`) REFERENCES `villas`(`id`) ON DELETE CASCADE,
  INDEX `idx_villa_platform` (`villa_id`, `platform`),
  INDEX `idx_platform_listing` (`platform`, `platform_listing_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: reservations
-- Réservations (directes et synchronisées)
-- =====================================================
CREATE TABLE `reservations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `reservation_number` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Numéro unique de réservation',
  `villa_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED COMMENT 'Client (peut être NULL si réservation externe)',
  `guest_first_name` VARCHAR(100) NOT NULL COMMENT 'Prénom du client',
  `guest_last_name` VARCHAR(100) NOT NULL COMMENT 'Nom du client',
  `guest_email` VARCHAR(255) NOT NULL,
  `guest_phone` VARCHAR(20),
  `guest_address` TEXT,
  `check_in_date` DATE NOT NULL,
  `check_out_date` DATE NOT NULL,
  `number_of_nights` INT UNSIGNED NOT NULL,
  `number_of_guests` TINYINT UNSIGNED NOT NULL,
  `base_price` DECIMAL(10, 2) NOT NULL COMMENT 'Prix de base (nuits)',
  `cleaning_fee` DECIMAL(10, 2) DEFAULT 0.00,
  `service_fee` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Frais de service',
  `vat_amount` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'TVA sur les services additionnels',
  `tourist_tax` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Taxe de séjour',
  `total_price` DECIMAL(10, 2) NOT NULL COMMENT 'Prix total',
  `currency` VARCHAR(3) DEFAULT 'EUR',
  `deposit_percentage` TINYINT UNSIGNED DEFAULT 30 COMMENT 'Pourcentage d\'arrhes (30-50%)',
  `deposit_amount` DECIMAL(10, 2) NOT NULL COMMENT 'Montant des arrhes',
  `balance_amount` DECIMAL(10, 2) NOT NULL COMMENT 'Montant du solde',
  `deposit_guarantee` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Dépôt de garantie',
  `status` ENUM('pending', 'confirmed', 'deposit_paid', 'fully_paid', 'cancelled', 'completed', 'refunded') DEFAULT 'pending',
  `source` ENUM('direct', 'airbnb', 'booking', 'abritel', 'manual') DEFAULT 'direct' COMMENT 'Origine de la réservation',
  `platform_reservation_id` VARCHAR(100) COMMENT 'ID sur la plateforme externe',
  `platform_sync_id` BIGINT UNSIGNED COMMENT 'Lien vers la synchronisation',
  `special_requests` TEXT COMMENT 'Demandes spéciales du client',
  `admin_notes` TEXT COMMENT 'Notes internes administrateur',
  `cancellation_reason` TEXT COMMENT 'Raison d\'annulation',
  `cancelled_at` TIMESTAMP NULL,
  `cancelled_by` BIGINT UNSIGNED COMMENT 'Utilisateur ayant annulé',
  `created_by` BIGINT UNSIGNED COMMENT 'Utilisateur ayant créé la réservation',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`villa_id`) REFERENCES `villas`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`platform_sync_id`) REFERENCES `platform_syncs`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`cancelled_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_reservation_number` (`reservation_number`),
  INDEX `idx_villa_dates` (`villa_id`, `check_in_date`, `check_out_date`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_source` (`source`),
  INDEX `idx_check_in` (`check_in_date`),
  INDEX `idx_check_out` (`check_out_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: reservation_guests
-- Invités supplémentaires (pour les réservations)
-- =====================================================
CREATE TABLE `reservation_guests` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `reservation_id` BIGINT UNSIGNED NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `birth_date` DATE,
  `nationality` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: payments
-- Paiements (arrhes, soldes, remboursements)
-- =====================================================
CREATE TABLE `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `reservation_id` BIGINT UNSIGNED NOT NULL,
  `payment_number` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Numéro unique de paiement',
  `type` ENUM('deposit', 'balance', 'deposit_guarantee', 'refund', 'adjustment') NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `currency` VARCHAR(3) DEFAULT 'EUR',
  `status` ENUM('pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',
  `payment_method` ENUM('stripe', 'bank_transfer', 'other') DEFAULT 'stripe',
  `stripe_payment_intent_id` VARCHAR(255) COMMENT 'ID Stripe PaymentIntent',
  `stripe_charge_id` VARCHAR(255) COMMENT 'ID Stripe Charge',
  `transaction_id` VARCHAR(255) COMMENT 'ID transaction externe',
  `due_date` DATE COMMENT 'Date d\'échéance du paiement',
  `paid_at` TIMESTAMP NULL COMMENT 'Date de paiement effectif',
  `failure_reason` TEXT COMMENT 'Raison d\'échec si applicable',
  `metadata` JSON COMMENT 'Métadonnées supplémentaires',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE RESTRICT,
  INDEX `idx_reservation` (`reservation_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_payment_number` (`payment_number`),
  INDEX `idx_stripe_intent` (`stripe_payment_intent_id`),
  INDEX `idx_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: documents
-- Documents générés (contrats, factures, reçus)
-- =====================================================
CREATE TABLE `documents` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `reservation_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('contract', 'invoice', 'receipt', 'deposit_receipt', 'balance_receipt', 'cancellation') NOT NULL,
  `document_number` VARCHAR(50) NOT NULL COMMENT 'Numéro du document',
  `file_path` VARCHAR(500) NOT NULL COMMENT 'Chemin du fichier PDF',
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` BIGINT UNSIGNED COMMENT 'Taille en octets',
  `mime_type` VARCHAR(100) DEFAULT 'application/pdf',
  `is_signed` BOOLEAN DEFAULT FALSE COMMENT 'Document signé',
  `signed_at` TIMESTAMP NULL,
  `signed_by` BIGINT UNSIGNED COMMENT 'Utilisateur ayant signé',
  `generated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`signed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_reservation` (`reservation_id`),
  INDEX `idx_type` (`type`),
  INDEX `idx_document_number` (`document_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: document_attachments
-- Pièces jointes aux documents (signatures, etc.)
-- =====================================================
CREATE TABLE `document_attachments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `document_id` BIGINT UNSIGNED NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(100) COMMENT 'Type (signature, annexe, etc.)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`document_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: messages
-- Messagerie entre clients et administrateurs
-- =====================================================
CREATE TABLE `messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `reservation_id` BIGINT UNSIGNED COMMENT 'Réservation liée (optionnel)',
  `sender_id` BIGINT UNSIGNED NOT NULL COMMENT 'Expéditeur',
  `recipient_id` BIGINT UNSIGNED COMMENT 'Destinataire (NULL si message général)',
  `subject` VARCHAR(255),
  `body` TEXT NOT NULL,
  `is_read` BOOLEAN DEFAULT FALSE,
  `read_at` TIMESTAMP NULL,
  `is_admin_message` BOOLEAN DEFAULT FALSE COMMENT 'Message envoyé par un admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recipient_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_reservation` (`reservation_id`),
  INDEX `idx_sender` (`sender_id`),
  INDEX `idx_recipient` (`recipient_id`),
  INDEX `idx_is_read` (`is_read`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DONNÉES INITIALES
-- =====================================================

-- Rôles par défaut
INSERT INTO `roles` (`name`, `display_name`, `permissions`) VALUES
('admin', 'Administrateur', '{"all": true}'),
('manager', 'Gestionnaire', '{"reservations": true, "villas": true, "messages": true}'),
('accountant', 'Comptable', '{"payments": true, "documents": true, "reports": true}'),
('support', 'Support Client', '{"messages": true, "reservations": {"view": true, "update": true}}');

-- Îles des Antilles
INSERT INTO `islands` (`name`, `code`, `country`) VALUES
('Martinique', 'MQ', 'France'),
('Guadeloupe', 'GP', 'France'),
('Saint-Barthélemy', 'BL', 'France'),
('Saint-Martin', 'MF', 'France'),
('Les Saintes', 'TF', 'France');

-- Saisons par défaut
INSERT INTO `seasons` (`name`, `start_month`, `start_day`, `end_month`, `end_day`, `multiplier`) VALUES
('Basse saison', 1, 1, 4, 30, 1.00),
('Haute saison', 5, 1, 8, 31, 1.50),
('Moyenne saison', 9, 1, 12, 31, 1.25);

-- Équipements de base
INSERT INTO `equipments` (`name`, `icon`, `category`) VALUES
('Piscine', 'pool', 'exterieur'),
('Jacuzzi', 'jacuzzi', 'exterieur'),
('WiFi', 'wifi', 'confort'),
('Climatisation', 'ac', 'confort'),
('Parking', 'parking', 'confort'),
('Lave-linge', 'washing-machine', 'confort'),
('Lave-vaisselle', 'dishwasher', 'confort'),
('Télévision', 'tv', 'confort'),
('Cuisine équipée', 'kitchen', 'confort'),
('Plage privée', 'beach', 'exterieur'),
('Vue mer', 'sea-view', 'exterieur'),
('Alarme', 'alarm', 'securite'),
('Coffre-fort', 'safe', 'securite');

-- Paramètres globaux par défaut
INSERT INTO `global_settings` (`key`, `value`, `type`, `description`, `category`) VALUES
('stripe_public_key', '', 'string', 'Clé publique Stripe', 'paiement'),
('stripe_secret_key', '', 'string', 'Clé secrète Stripe', 'paiement'),
('stripe_webhook_secret', '', 'string', 'Secret webhook Stripe', 'paiement'),
('default_currency', 'EUR', 'string', 'Devise par défaut', 'paiement'),
('supported_currencies', '["EUR", "USD"]', 'json', 'Devises supportées', 'paiement'),
('tourist_tax_per_person_per_night', '1.50', 'decimal', 'Taxe de séjour par personne et par nuit (EUR)', 'reservation'),
('service_fee_percentage', '5.00', 'decimal', 'Pourcentage de frais de service', 'reservation'),
('deposit_percentage_min', '30', 'integer', 'Pourcentage minimum d\'arrhes', 'reservation'),
('deposit_percentage_max', '50', 'integer', 'Pourcentage maximum d\'arrhes', 'reservation'),
('balance_due_days_before_checkin', '30', 'integer', 'Jours avant l\'arrivée pour le paiement du solde', 'reservation'),
('deposit_guarantee_days_before_checkin', '7', 'integer', 'Jours avant l\'arrivée pour le dépôt de garantie', 'reservation'),
('cancellation_policy_days', '30', 'integer', 'Délai d\'annulation (jours avant arrivée)', 'reservation'),
('company_name', 'LUXÎLES', 'string', 'Nom de l\'entreprise', 'general'),
('company_address', '', 'string', 'Adresse de l\'entreprise', 'general'),
('company_phone', '', 'string', 'Téléphone de l\'entreprise', 'general'),
('company_email', 'contact@luxîles.fr', 'string', 'Email de l\'entreprise', 'general'),
('company_siret', '', 'string', 'SIRET', 'general'),
('company_vat', '', 'string', 'Numéro TVA', 'general');

-- Templates d'e-mails par défaut
INSERT INTO `email_templates` (`name`, `subject`, `body_html`, `variables`) VALUES
('reservation_confirmation', 'Confirmation de votre réservation - {{reservation_number}}', '<p>Bonjour {{guest_name}},</p><p>Votre réservation {{reservation_number}} a été confirmée.</p>', '["reservation_number", "guest_name", "villa_name", "check_in_date", "check_out_date"]'),
('payment_reminder', 'Rappel de paiement - Réservation {{reservation_number}}', '<p>Bonjour {{guest_name}},</p><p>Un paiement est dû pour votre réservation {{reservation_number}}.</p>', '["reservation_number", "guest_name", "amount", "due_date"]'),
('check_in_reminder', 'Rappel - Arrivée prévue le {{check_in_date}}', '<p>Bonjour {{guest_name}},</p><p>Votre arrivée est prévue le {{check_in_date}}.</p>', '["guest_name", "check_in_date", "villa_name", "villa_address"]'),
('check_out_reminder', 'Rappel - Départ prévu le {{check_out_date}}', '<p>Bonjour {{guest_name}},</p><p>Votre départ est prévu le {{check_out_date}}.</p>', '["guest_name", "check_out_date"]'),
('deposit_guarantee_reminder', 'Rappel - Dépôt de garantie à effectuer', '<p>Bonjour {{guest_name}},</p><p>Le dépôt de garantie doit être effectué avant votre arrivée.</p>', '["guest_name", "amount", "due_date"]');










