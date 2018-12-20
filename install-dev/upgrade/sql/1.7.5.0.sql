SET SESSION sql_mode='';
SET NAMES 'utf8';

/* PHP:add_supplier_manufacturer_routes(); */;

/* PHP:ps_1750_update_module_tabs(); */;

ALTER TABLE `PREFIX_cms_lang`
  ADD `head_seo_title` varchar(255) DEFAULT NULL AFTER `meta_title`,
  CHANGE `meta_title` `meta_title` VARCHAR(255) NOT NULL,
  CHANGE `meta_description` `meta_description` VARCHAR(512) DEFAULT NULL;

ALTER TABLE `PREFIX_stock_available`
  ADD `location` VARCHAR(255) NOT NULL DEFAULT '' AFTER `out_of_stock`;

ALTER TABLE `PREFIX_store`
  CHANGE `email` `email` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `PREFIX_contact`
  CHANGE `email` `email` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_contact_lang`
  CHANGE `name` `name` varchar(255) NOT NULL;

ALTER TABLE `PREFIX_category_lang`
  CHANGE `meta_title` `meta_title` VARCHAR(255) DEFAULT NULL,
  CHANGE `meta_description` `meta_description` VARCHAR(512) DEFAULT NULL;

ALTER TABLE `PREFIX_cms_category_lang`
  CHANGE `meta_title` `meta_title` VARCHAR(255) DEFAULT NULL,
  CHANGE `meta_description` `meta_description` VARCHAR(512) DEFAULT NULL;

ALTER TABLE `PREFIX_customer`
  CHANGE `company` `company` VARCHAR(255),
  CHANGE `email` `email` VARCHAR(255) NOT NULL,
  CHANGE `passwd` `passwd` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_manufacturer_lang`
  CHANGE `meta_title` `meta_title` VARCHAR(255) NOT NULL,
  CHANGE `meta_description` `meta_description` VARCHAR(512) DEFAULT NULL;

ALTER TABLE `PREFIX_employee`
  CHANGE `firstname` `firstname` VARCHAR(255) NOT NULL,
  CHANGE `email` `email` VARCHAR(255) NOT NULL,
  CHANGE `passwd` `passwd` VARCHAR(255) NOT NULL,
  CHANGE `lastname` `lastname` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_referrer`
  CHANGE `passwd` `passwd` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `PREFIX_supply_order_history`
  CHANGE `employee_lastname` `employee_lastname` VARCHAR(255) DEFAULT '',
  CHANGE `employee_firstname` `employee_firstname` VARCHAR(255) DEFAULT '';

ALTER TABLE `PREFIX_supply_order_receipt_history`
  CHANGE `employee_firstname` `employee_firstname` VARCHAR(255) DEFAULT '';

ALTER TABLE `PREFIX_supplier_lang`
  CHANGE `meta_description` `meta_description` VARCHAR(512) DEFAULT NULL,
  CHANGE `meta_title` `meta_title` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `PREFIX_order_detail`
  CHANGE `product_reference` `product_reference` varchar(64) DEFAULT NULL;

ALTER TABLE `PREFIX_product`
  CHANGE `supplier_reference` `supplier_reference` varchar(64) DEFAULT NULL;

ALTER TABLE `PREFIX_product_attribute`
  CHANGE `reference` `reference` varchar(64) DEFAULT NULL,
  CHANGE `supplier_reference` `supplier_reference` varchar(64) DEFAULT NULL;

ALTER TABLE `PREFIX_warehouse`
  CHANGE `reference` `reference` varchar(64) DEFAULT NULL;

ALTER TABLE `PREFIX_stock`
  CHANGE `reference` `reference` varchar(64) DEFAULT NULL;

ALTER TABLE `PREFIX_supply_order_detail`
  CHANGE `reference` `reference` varchar(64) NOT NULL,
  CHANGE `supplier_reference` `supplier_reference` varchar(64) NOT NULL;

ALTER TABLE `PREFIX_product_supplier`
  CHANGE `product_supplier_reference` `product_supplier_reference` varchar(64) DEFAULT NULL;

ALTER TABLE `PREFIX_product_lang`
  CHANGE `meta_description` `meta_description` varchar(512) DEFAULT NULL,
  CHANGE `meta_keywords` `meta_keywords` varchar(255) DEFAULT NULL;

ALTER TABLE `PREFIX_customer_thread`
  CHANGE `email` `email` varchar(255) NOT NULL;
