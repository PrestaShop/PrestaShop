SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

<<<<<<< HEAD
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
    ('PS_MAIL_DKIM_ENABLE', '0', NOW(), NOW()),
    ('PS_MAIL_DKIM_DOMAIN', '', NOW(), NOW()),
    ('PS_MAIL_DKIM_SELECTOR', '', NOW(), NOW()),
    ('PS_MAIL_DKIM_KEY', '', NOW(), NOW())
;
=======
INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionAdminSecurityControllerPostProcessBefore', 'On post-process in Admin Security Controller', 'This hook is called on Admin Security Controller post-process before processing any form', '1'),
  (NULL, 'actionAdminSecurityControllerPostProcessGeneralBefore', 'On post-process in Admin Security Controller', 'This hook is called on Admin Security Controller post-process before processing the General form', '1')
;


ALTER TABLE `PREFIX_employee_session` ADD `date_upd` DATETIME NOT NULL AFTER `token`;
ALTER TABLE `PREFIX_employee_session` ADD `date_add` DATETIME NOT NULL AFTER `date_upd`;
ALTER TABLE `PREFIX_customer_session` ADD `date_upd` DATETIME NOT NULL AFTER `token`;
ALTER TABLE `PREFIX_customer_session` ADD `date_add` DATETIME NOT NULL AFTER `date_upd`;

/* PHP:ps_1780_update_tabs(); */;
>>>>>>> Use 1.7.9.0.sql instead of 1.7.8.0.sql and add missing comments
