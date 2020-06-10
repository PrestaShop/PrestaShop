SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionPresentCart', 'Cart Presenter', 'This hook is called before a cart is presented', '1'),
  (NULL, 'actionPresentOrder', 'Order footer', 'This hook is called before an order is presented', '1'),
  (NULL, 'actionPresentOrderReturn', 'Order Return Presenter', 'This hook is called before an order return is presented', '1'),
  (NULL, 'actionPresentProduct', 'Product Presenter', 'This hook is called before a product is presented', '1'),
  (NULL, 'displayBanner', 'Display Banner', 'Use this hook for banners on top of every pages', '1'),
  (NULL, 'actionModuleUninstallBefore', 'Module uninstall before', 'This hook is called before module uninstall process', '1'),
  (NULL, 'actionModuleUninstallAfter', 'Module uninstall after', 'This hook is called at the end of module uninstall process', '1')
;

INSERT IGNORE INTO `PREFIX_authorization_role` (`slug`) VALUES
  ('ROLE_MOD_TAB_DEFAULT_CREATE'),
  ('ROLE_MOD_TAB_DEFAULT_READ'),
  ('ROLE_MOD_TAB_DEFAULT_UPDATE'),
  ('ROLE_MOD_TAB_DEFAULT_DELETE');

INSERT IGNORE INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('1', (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminTabs' LIMIT 1), '1', '1', '1', '1');

INSERT IGNORE INTO `PREFIX_tab_lang` (`id_lang`, `id_tab`, `name`) VALUES
	(1, (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminTabs'), 'Menus');
