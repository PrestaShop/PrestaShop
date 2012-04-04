/* PHP:module_blockwishlist_multishop(); */;

ALTER TABLE `PREFIX_supplier` DROP `id_address`;

UPDATE `PREFIX_meta` SET `page` = 'contact' WHERE `page` = 'contact-form';