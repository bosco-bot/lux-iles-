-- Migration pour ajouter les colonnes adults, children, infants à la table reservations
-- Cette migration permet de stocker la décomposition détaillée des voyageurs

ALTER TABLE `reservations` 
ADD COLUMN `adults` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Nombre d''adultes' AFTER `number_of_guests`,
ADD COLUMN `children` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Nombre d''enfants' AFTER `adults`,
ADD COLUMN `infants` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Nombre de bébés' AFTER `children`;

-- Mettre à jour les valeurs existantes : considérer number_of_guests comme adults
UPDATE `reservations` 
SET `adults` = `number_of_guests`, 
    `children` = 0, 
    `infants` = 0
WHERE `adults` = 0;








