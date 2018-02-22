SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_detail` DROP KEY product_id, ADD KEY product_id (product_id, product_attribute_id);
