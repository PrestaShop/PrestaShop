SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_cart_rule` ADD KEY `date_from` (`date_from`), ADD KEY `date_to` (`date_to`);
