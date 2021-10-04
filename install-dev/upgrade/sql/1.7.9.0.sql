SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
    ('PS_MAIL_DKIM_ENABLE', '0', NOW(), NOW()),
    ('PS_MAIL_DKIM_DOMAIN', '', NOW(), NOW()),
    ('PS_MAIL_DKIM_SELECTOR', '', NOW(), NOW()),
    ('PS_MAIL_DKIM_KEY', '', NOW(), NOW())
;
INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionValidateOrderAfter', 'New Order', 'This hook is called after validating an order by core', '1');

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionAdminOrdersTrackingNumberUpdate', 'After setting the tracking number for the order', 'This hook allows you to execute code after the unique tracking number for the order was added', '1');

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionCustomerLogoutBefore', 'Before customer logout', 'This hook allows you to execute code befor customer logout', '1');

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionCustomerLogoutAfter', 'After customer logout', 'This hook allows you to execute code after customer logout', '1');
