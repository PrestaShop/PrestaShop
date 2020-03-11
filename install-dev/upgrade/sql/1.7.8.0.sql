SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionPresentCart', 'Cart Presenter', 'This hook is called before a cart is presented', '1'),
  (NULL, 'actionPresentOrder', 'Order footer', 'This hook is called before an order is presented', '1'),
  (NULL, 'actionPresentOrderReturn', 'Order Return Presenter', 'This hook is called before an order return is presented', '1'),
  (NULL, 'actionPresentProduct', 'Product Presenter', 'This hook is called before a product is presented', '1')
  (NULL, 'displayBanner', 'Display Banner', 'Use this hook for banners on top of every pages', '1')
;

ALTER TABLE `PREFIX_tab` ADD `wording` VARCHAR(196) DEFAULT NULL AFTER `icon`;
ALTER TABLE `PREFIX_tab` ADD `wording_domain` VARCHAR(196) DEFAULT NULL AFTER `wording`;
