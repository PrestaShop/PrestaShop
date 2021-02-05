SET SESSION sql_mode='';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_supply_order_receipt_history`
  CHANGE `employee_lastname` `employee_lastname` VARCHAR(255) DEFAULT '';

ALTER TABLE `PREFIX_product`
  CHANGE `reference` `reference` varchar(64) DEFAULT NULL;

ALTER TABLE `PREFIX_order_detail`
  CHANGE `product_supplier_reference` `product_supplier_reference` varchar(64) DEFAULT NULL;

-- Update default links in quick access
UPDATE `PREFIX_quick_access` SET `link` = "index.php/improve/modules/manage"
  WHERE link = "index.php/module/manage";
UPDATE `PREFIX_quick_access` SET `link` = "index.php/sell/catalog/products/new"
  WHERE link = "index.php/product/new";

/* PHP:ps_1751_update_module_sf_tab(); */;
