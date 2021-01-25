SET SESSION sql_mode='';
SET NAMES 'utf8';

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
    (NULL, 'displayAdminGridTableBefore', 'Display before Grid table', 'This hook adds new blocks before Grid component table.', '1'),
    (NULL, 'displayAdminGridTableAfter', 'Display after Grid table', 'This hook adds new blocks after Grid component table.', '1')
