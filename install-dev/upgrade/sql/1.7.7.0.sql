SET SESSION sql_mode='';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_search_word` MODIFY `word` VARCHAR(30) NOT NULL;

/* Update wrong hook names */
UPDATE `PREFIX_hook` SET `name` = 'actionAdministrationPageSave' WHERE `name` = 'actionAdministrationPageFormSave';
UPDATE `PREFIX_hook` SET `name` = 'actionMaintenancePageSave' WHERE `name` = 'actionMaintenancePageFormSave';
UPDATE `PREFIX_hook` SET `name` = 'actionPerformancePageSave' WHERE `name` = 'actionPerformancePageFormSave';
