-- =====================================================
-- TABLE: global_settings
-- Paramètres globaux de la plateforme
-- =====================================================
CREATE TABLE IF NOT EXISTS `global_settings` (
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
-- DONNÉES PAR DÉFAUT - Informations légales de l'entreprise
-- =====================================================
INSERT INTO `global_settings` (`key`, `value`, `type`, `description`, `category`) VALUES
('company_name', 'BLUE SECRET', 'string', 'Nom de l\'entreprise', 'general'),
('company_address', '4 LOT DOMAINE DU GRAND BLEU, PALAIS STE MARGUERITE, 97160 LE MOULE', 'string', 'Adresse de l\'entreprise', 'general'),
('company_phone', '+33 7 66 33 41 98', 'string', 'Téléphone de l\'entreprise', 'general'),
('company_email', 'contact.luxiles@gmail.com', 'string', 'Email de l\'entreprise', 'general'),
('company_siret', '85262415400013', 'string', 'SIRET', 'general'),
('company_vat', 'FR31852624154', 'string', 'Numéro TVA intracommunautaire', 'general')
ON DUPLICATE KEY UPDATE 
  `value` = VALUES(`value`),
  `updated_at` = CURRENT_TIMESTAMP;

-- =====================================================
-- Paramètres SMTP pour l'envoi d'emails
-- =====================================================
INSERT INTO `global_settings` (`key`, `value`, `type`, `description`, `category`) VALUES
('email_smtp_host', 'smtp.gmail.com', 'string', 'Serveur SMTP', 'email'),
('email_smtp_port', '587', 'integer', 'Port SMTP', 'email'),
('email_smtp_username', '', 'string', 'Nom d\'utilisateur SMTP', 'email'),
('email_smtp_password', '', 'string', 'Mot de passe SMTP', 'email'),
('email_smtp_encryption', 'tls', 'string', 'Chiffrement SMTP (tls ou ssl)', 'email'),
('email_from_address', 'contact.luxiles@gmail.com', 'string', 'Adresse email expéditeur', 'email'),
('email_from_name', 'LUXÎLES', 'string', 'Nom de l\'expéditeur', 'email')
ON DUPLICATE KEY UPDATE 
  `value` = VALUES(`value`),
  `updated_at` = CURRENT_TIMESTAMP;

-- =====================================================
-- Autres paramètres globaux (optionnels, à adapter selon vos besoins)
-- =====================================================
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
('cancellation_policy_days', '30', 'integer', 'Délai d\'annulation (jours avant arrivée)', 'reservation')
ON DUPLICATE KEY UPDATE 
  `value` = VALUES(`value`),
  `updated_at` = CURRENT_TIMESTAMP;

