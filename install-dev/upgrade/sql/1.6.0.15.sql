ALTER TABLE `PREFIX_product` ADD `pwyw_price` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'whether Pay-What-You-Want is enabled or not' AFTER `wholesale_price`;
ALTER TABLE `PREFIX_product_shop` ADD `pwyw_price` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'whether Pay-What-You-Want is enabled or not' AFTER `wholesale_price`;
ALTER TABLE `PREFIX_cart_product` ADD `pwyw_price` decimal(20,6) NULL DEFAULT NULL COMMENT 'The price chosen by the customer for Pay-What-You-Want enabled products' AFTER `quantity`;
ALTER TABLE `PREFIX_order_detail` ADD `product_is_pwyw_price` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'whether Pay-What-You-Want was enabled for this product' AFTER `product_price`;
