-- ---------------------------------------------------------------------------
-- Khan Travel B2B partner registration
-- Adds `users.previous_contact` for the "Have you already had contact with one
-- of our employees?" answer. No existing column holds this information.
--
-- Safe for the existing system:
--   * additive, nullable, no default value → existing rows and Laravel code are unaffected
--   * no existing column is renamed, changed or removed
--
-- Stored values: none | sales_team | business_development | support_team | other
-- ---------------------------------------------------------------------------

ALTER TABLE `users`
    ADD COLUMN `previous_contact` VARCHAR(50) NULL DEFAULT NULL AFTER `company_name`;

-- Rollback:
-- ALTER TABLE `users` DROP COLUMN `previous_contact`;
