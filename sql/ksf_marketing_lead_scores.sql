-- 0_ksf_marketing_lead_scores
-- Lead score per marketing contact.
--
-- WHY THIS TABLE IS MODULE-LOCAL:
--   A lead score is marketing-domain state (points, lifecycle stage). FA core
--   has no such entity. The table therefore stays in the
--   0_ksf_marketing_* family.
--
-- The FA install engine replaces the literal 0_ with TB_PREF.

CREATE TABLE IF NOT EXISTS `0_ksf_marketing_lead_scores` (
    `contact_id` INT(11) NOT NULL,
    `score` INT(11) NOT NULL DEFAULT 0,
    `stage` VARCHAR(40) NOT NULL DEFAULT 'lead',
    `points_breakdown` TEXT DEFAULT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
