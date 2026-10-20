-- 0_ksf_marketing_contacts
-- Marketing contact list.
--
-- WHY THIS TABLE IS MODULE-LOCAL:
--   A marketing contact is a lead captured by a form, a drip campaign or an
--   advertisement - FA core has no such entity, and it is deliberately NOT an
--   FA person (0_crm_persons): a captured lead is not a customer contact.
--   The table therefore stays in the 0_ksf_marketing_* family.
--
-- The FA install engine replaces the literal 0_ with TB_PREF.

CREATE TABLE IF NOT EXISTS `0_ksf_marketing_contacts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(40) DEFAULT NULL,
    `company` VARCHAR(255) DEFAULT NULL,
    `source` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_source` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
