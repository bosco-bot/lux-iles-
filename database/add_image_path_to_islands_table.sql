-- =====================================================
-- AJOUT COLONNE: image_path à la table islands
-- Migration: 2025_12_07_184448_add_image_path_to_islands_table.php
-- =====================================================
-- Cette colonne permet de stocker le chemin local des images des îles
-- en complément de image_url (URL externe)
-- 
-- IMPORTANT: Si la colonne existe déjà, cette requête échouera.
-- Vérifiez d'abord avec: SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = 'votre_base' AND TABLE_NAME = 'islands' AND COLUMN_NAME = 'image_path';
-- 
-- Si la colonne n'existe pas, exécutez la commande ci-dessous :

ALTER TABLE `islands` 
ADD COLUMN `image_path` VARCHAR(500) NULL COMMENT 'Chemin local de l''image de l''île' 
AFTER `image_url`;

