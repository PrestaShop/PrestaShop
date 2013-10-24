ALTER TABLE `ps_orders` CHANGE `reference` `reference` VARCHAR( 12 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `ps_order_payment` CHANGE `order_reference` `order_reference` VARCHAR( 12 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
