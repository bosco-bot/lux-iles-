-- =====================================================
-- SCRIPT: Marquer les migrations existantes comme exécutées
-- =====================================================
-- Ce script insère les migrations dans la table `migrations`
-- pour indiquer qu'elles ont déjà été exécutées
-- Utilisez ce script si vos tables existent déjà (via dump SQL)
-- mais que la table `migrations` n'enregistre pas ces migrations
--
-- IMPORTANT: Exécutez ce script AVANT de lancer `php artisan migrate`
-- Cela évitera que Laravel essaie de recréer des tables existantes

-- Vérifier d'abord si les migrations existent déjà
-- Si elles existent déjà, ce script ne les ajoutera pas en double (grâce aux noms uniques)

-- Migration 1: Users table (base Laravel)
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1);

-- Migration 2: Cache table
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000001_create_cache_table', 1);

-- Migration 3: Jobs table
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000002_create_jobs_table', 1);

-- Migration 4: Update users table for LUXÎLES
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_11_29_231052_update_users_table_for_lux_iles', 2);

-- Migration 5: Add photo_url to users
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_11_30_021101_add_photo_url_to_users_table', 3);

-- Migration 6: Create password_reset_tokens
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_11_30_024023_create_password_reset_tokens_table', 3);

-- Migration 7: Create message_attachments
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_04_124626_create_message_attachments_table', 4);

-- Migration 8: Create cancellation_policies
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_04_162101_create_cancellation_policies_table', 5);

-- Migration 9: Create settings_history
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_04_170138_create_settings_history_table', 6);

-- Migration 10: Create villa_ical_configs
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_04_185813_create_villa_ical_configs_table', 7);

-- Migration 11: Create seasons
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_04_222622_create_seasons_table', 8);

-- Migration 12: Create villa_seasonal_prices
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_04_222629_create_villa_seasonal_prices_table', 8);

-- Migration 13: Create favorites
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_05_130810_create_favorites_table', 9);

-- Migration 14: Add image_url to islands
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_07_181651_add_image_url_to_islands_table', 10);

-- Migration 15: Add image_path to islands
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_07_184448_add_image_path_to_islands_table', 10);

-- Migration 16: Add vat_amount to reservations
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_14_102827_add_vat_amount_to_reservations_table', 11);

-- Migration 17: Create notifications
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_29_154159_create_notifications_table', 12);

-- Note: Les migrations qui créent des tables principales (villas, reservations, etc.)
-- sont probablement déjà dans la table migrations si elles ont été créées via schema.sql
-- Ce script ne les inclut pas pour éviter les doublons








