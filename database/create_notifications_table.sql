-- =====================================================
-- TABLE: notifications
-- Table Laravel standard pour les notifications utilisateurs
-- Cette table est créée par la migration: 2025_12_29_154159_create_notifications_table.php
-- =====================================================
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` CHAR(36) NOT NULL PRIMARY KEY COMMENT 'UUID de la notification',
  `type` VARCHAR(255) NOT NULL COMMENT 'Type de notification (nom de classe)',
  `notifiable_type` VARCHAR(255) NOT NULL COMMENT 'Type du modèle notifiable (ex: App\\Models\\User)',
  `notifiable_id` BIGINT UNSIGNED NOT NULL COMMENT 'ID du modèle notifiable',
  `data` TEXT NOT NULL COMMENT 'Données JSON de la notification',
  `read_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Date de lecture',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;








