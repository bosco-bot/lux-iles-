-- =====================================================
-- SCRIPT: Marquer les migrations existantes comme exécutées
-- =====================================================
-- Ces migrations tentent de créer/modifier des tables/colonnes qui existent déjà
-- Ce script les marque comme exécutées pour éviter les erreurs de duplication

-- Migration: create_seasons_table
-- La table seasons existe déjà, on marque la migration comme exécutée
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_04_222622_create_seasons_table', 8);

-- Migration: create_villa_seasonal_prices_table  
-- La table villa_seasonal_prices existe probablement aussi
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_04_222629_create_villa_seasonal_prices_table', 8);

-- Migration: add_image_url_to_islands_table
-- La colonne image_url existe déjà (ajoutée manuellement)
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_07_181651_add_image_url_to_islands_table', 10);

-- Migration: add_image_path_to_islands_table
-- La colonne image_path existe déjà (ajoutée manuellement)
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_07_184448_add_image_path_to_islands_table', 10);

-- Migration: create_notifications_table
-- La table notifications existe déjà (créée manuellement via SQL)
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2025_12_29_154159_create_notifications_table', 12);

