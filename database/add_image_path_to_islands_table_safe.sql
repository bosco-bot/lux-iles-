-- =====================================================
-- AJOUT COLONNE: image_path à la table islands (VERSION SÉCURISÉE)
-- Migration: 2025_12_07_184448_add_image_path_to_islands_table.php
-- =====================================================
-- Cette version vérifie si la colonne existe avant de l'ajouter
-- Compatible MySQL/MariaDB

-- Vérifier et ajouter la colonne si elle n'existe pas
SET @dbname = DATABASE();
SET @tablename = 'islands';
SET @columnname = 'image_path';
SET @preparedStatement = (SELECT IF(
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
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;








