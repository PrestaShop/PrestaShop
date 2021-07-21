SET SESSION sql_mode='';
SET NAMES 'utf8';

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
    (NULL, 'displayAdminGridTableBefore', 'Display before Grid table', 'This hook adds new blocks before Grid component table.', '1'),
    (NULL, 'displayAdminGridTableAfter', 'Display after Grid table', 'This hook adds new blocks after Grid component table.', '1')
;

UPDATE `PREFIX_hook_module` AS hm
    INNER JOIN `PREFIX_hook` AS hfrom ON hm.id_hook = hfrom.id_hook AND hfrom.name = 'displayAdminListBefore'
    INNER JOIN `PREFIX_hook` AS hto ON hto.name = 'displayAdminGridTableBefore'
    SET hm.id_hook = hto.id_hook;
DELETE FROM `PREFIX_hook` WHERE name = 'displayAdminListBefore';

UPDATE `PREFIX_hook_module` AS hm
    INNER JOIN `PREFIX_hook` AS hfrom ON hm.id_hook = hfrom.id_hook AND hfrom.name = 'displayAdminListAfter'
    INNER JOIN `PREFIX_hook` AS hto ON hto.name = 'displayAdminGridTableAfter'
    SET hm.id_hook = hto.id_hook;
DELETE FROM `PREFIX_hook` WHERE name = 'displayAdminListAfter';


INSERT IGNORE INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES
     ('displayAdminGridTableBefore', 'displayAdminListBefore'),
     ('displayAdminGridTableAfter', 'displayAdminListAfter')
;
