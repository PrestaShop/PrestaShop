SET SESSION sql_mode='';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_supply_order_receipt_history`
  CHANGE `employee_lastname` `employee_lastname` VARCHAR(255) DEFAULT '';

ALTER TABLE `PREFIX_product`
  CHANGE `reference` `reference` varchar(64) DEFAULT NULL;
