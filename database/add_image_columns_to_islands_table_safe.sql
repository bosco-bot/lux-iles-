-- =====================================================
-- AJOUT COLONNES: image_url et image_path à la table islands (VERSION SÉCURISÉE)
-- Migrations: 
--   - 2025_12_07_181651_add_image_url_to_islands_table.php
--   - 2025_12_07_184448_add_image_path_to_islands_table.php
-- =====================================================
-- Ce script ajoute les deux colonnes si elles n'existent pas
-- Compatible MySQL/MariaDB

SET @dbname = DATABASE();
SET @tablename = 'islands';

-- Étape 1: Ajouter image_url si elle n'existe pas (après description)
SET @columnname = 'image_url';
SET @preparedStatement1 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT 1', -- La colonne existe, on ne fait rien
  CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @columnname, '` VARCHAR(500) NULL COMMENT ''URL de l''''image de l''''île'' AFTER `description`;')
));
PREPARE alterIfNotExists1 FROM @preparedStatement1;
EXECUTE alterIfNotExists1;
DEALLOCATE PREPARE alterIfNotExists1;

-- Étape 2: Ajouter image_path si elle n'existe pas (après image_url)
SET @columnname = 'image_path';
SET @preparedStatement2 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT 1', -- La colonne existe, on ne fait rien
  CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @columnname, '` VARCHAR(500) NULL COMMENT ''Chemin local de l''''image de l''''île'' AFTER `image_url`;')
));
PREPARE alterIfNotExists2 FROM @preparedStatement2;
EXECUTE alterIfNotExists2;
DEALLOCATE PREPARE alterIfNotExists2;








