SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionAdminSecurityControllerPostProcessBefore', 'On post-process in Admin Security Controller', 'This hook is called on Admin Security Controller post-process before processing any form', '1'),
  (NULL, 'actionAdminSecurityControllerPostProcessGeneralBefore', 'On post-process in Admin Security Controller', 'This hook is called on Admin Security Controller post-process before processing the General form', '1')
;


ALTER TABLE `PREFIX_employee_session` ADD `date_upd` DATETIME NOT NULL AFTER `token`;
ALTER TABLE `PREFIX_employee_session` ADD `date_add` DATETIME NOT NULL AFTER `date_upd`;
ALTER TABLE `PREFIX_customer_session` ADD `date_upd` DATETIME NOT NULL AFTER `token`;
ALTER TABLE `PREFIX_customer_session` ADD `date_add` DATETIME NOT NULL AFTER `date_upd`;

/* PHP:ps_1780_update_tabs(); */;
