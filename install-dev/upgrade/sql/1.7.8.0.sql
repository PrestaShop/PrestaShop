SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionPresentCart', 'Cart Presenter', 'This hook is called before a cart is presented', '1'),
  (NULL, 'actionPresentOrder', 'Order footer', 'This hook is called before an order is presented', '1'),
  (NULL, 'actionPresentOrderReturn', 'Order Return Presenter', 'This hook is called before an order return is presented', '1'),
  (NULL, 'actionPresentProduct', 'Product Presenter', 'This hook is called before a product is presented', '1'),
  (NULL, 'displayBanner', 'Display Banner', 'Use this hook for banners on top of every pages', '1'),
  (NULL, 'actionModuleUninstallBefore', 'Module uninstall before', 'This hook is called before module uninstall process', '1'),
  (NULL, 'actionModuleUninstallAfter', 'Module uninstall after', 'This hook is called at the end of module uninstall process', '1'),
  (NULL, 'actionCheckoutRender', 'Checkout process render', 'This hook is called when checkout process is constructed', '1'),
  (NULL, 'actionPresentProductListing', 'Product Listing Presenter', 'This hook is called before a product listing is presented', '1')
  (NULL, 'actionGetProductPropertiesAfterUnitPrice', 'Product Properties', 'This hook is called after defining the properties of a product', '1')
;
