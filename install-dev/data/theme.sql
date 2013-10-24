SET NAMES 'utf8';

UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_CONDITIONS';
UPDATE `PREFIX_configuration` SET value = '10' WHERE name = 'PS_PRODUCTS_PER_PAGE';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_PRODUCTS_ORDER_WAY';
UPDATE `PREFIX_configuration` SET value = '4' WHERE name = 'PS_PRODUCTS_ORDER_BY';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_DISPLAY_QTIES';
UPDATE `PREFIX_configuration` SET value = '20' WHERE name = 'PS_NB_DAYS_NEW_PRODUCT';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_BLOCK_CART_AJAX';
UPDATE `PREFIX_configuration` SET value = '8388608' WHERE name = 'PS_PRODUCT_PICTURE_MAX_SIZE';
UPDATE `PREFIX_configuration` SET value = '64' WHERE name = 'PS_PRODUCT_PICTURE_WIDTH';
UPDATE `PREFIX_configuration` SET value = '64' WHERE name = 'PS_PRODUCT_PICTURE_HEIGHT';
UPDATE `PREFIX_configuration` SET value = '3' WHERE name = 'PS_SEARCH_MINWORDLEN';
UPDATE `PREFIX_configuration` SET value = '6' WHERE name = 'PS_SEARCH_WEIGHT_PNAME';
UPDATE `PREFIX_configuration` SET value = '10' WHERE name = 'PS_SEARCH_WEIGHT_REF';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_SEARCH_WEIGHT_SHORTDESC';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_SEARCH_WEIGHT_DESC';
UPDATE `PREFIX_configuration` SET value = '3' WHERE name = 'PS_SEARCH_WEIGHT_CNAME';
UPDATE `PREFIX_configuration` SET value = '3' WHERE name = 'PS_SEARCH_WEIGHT_MNAME';
UPDATE `PREFIX_configuration` SET value = '4' WHERE name = 'PS_SEARCH_WEIGHT_TAG';
UPDATE `PREFIX_configuration` SET value = '2' WHERE name = 'PS_SEARCH_WEIGHT_ATTRIBUTE';
UPDATE `PREFIX_configuration` SET value = '2' WHERE name = 'PS_SEARCH_WEIGHT_FEATURE';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_SEARCH_AJAX';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_DISPLAY_JQZOOM';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_BLOCK_BESTSELLERS_DISPLAY';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_BLOCK_NEWPRODUCTS_DISPLAY';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_BLOCK_SPECIALS_DISPLAY';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_TAX_DISPLAY';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_STORES_DISPLAY_CMS';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_STORES_DISPLAY_FOOTER';
UPDATE `PREFIX_configuration` SET value = '209' WHERE name = 'SHOP_LOGO_WIDTH';
UPDATE `PREFIX_configuration` SET value = '52' WHERE name = 'SHOP_LOGO_HEIGHT';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'PS_DISPLAY_SUPPLIERS';
UPDATE `PREFIX_configuration` SET value = '0' WHERE name = 'PS_LEGACY_IMAGES';
UPDATE `PREFIX_configuration` SET value = 'jpg' WHERE name = 'PS_IMAGE_QUALITY';
UPDATE `PREFIX_configuration` SET value = '7' WHERE name = 'PS_PNG_QUALITY';
UPDATE `PREFIX_configuration` SET value = '90' WHERE name = 'PS_JPEG_QUALITY';
UPDATE `PREFIX_configuration` SET value = '2' WHERE name = 'PRODUCTS_VIEWED_NBR';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'BLOCK_CATEG_DHTML';
UPDATE `PREFIX_configuration` SET value = '4' WHERE name = 'BLOCK_CATEG_MAX_DEPTH';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'MANUFACTURER_DISPLAY_FORM';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'MANUFACTURER_DISPLAY_TEXT';
UPDATE `PREFIX_configuration` SET value = '5' WHERE name = 'MANUFACTURER_DISPLAY_TEXT_NB';
UPDATE `PREFIX_configuration` SET value = '5' WHERE name = 'NEW_PRODUCTS_NBR';
UPDATE `PREFIX_configuration` SET value = '10' WHERE name = 'BLOCKTAGS_NBR';
UPDATE `PREFIX_configuration` SET value = '0_3|0_4' WHERE name = 'FOOTER_CMS';
UPDATE `PREFIX_configuration` SET value = '0_3|0_4' WHERE name = 'FOOTER_BLOCK_ACTIVATION';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'FOOTER_POWEREDBY';
UPDATE `PREFIX_configuration` SET value = 'http://www.prestashop.com' WHERE name = 'BLOCKADVERT_LINK';
UPDATE `PREFIX_configuration` SET value = 'store.jpg' WHERE name = 'BLOCKSTORE_IMG';
UPDATE `PREFIX_configuration` SET value = 'jpg' WHERE name = 'BLOCKADVERT_IMG_EXT';
UPDATE `PREFIX_configuration` SET value = 'CAT2,CAT3,CAT4' WHERE name = 'MOD_BLOCKTOPMENU_ITEMS';
UPDATE `PREFIX_configuration` SET value = '' WHERE name = 'MOD_BLOCKTOPMENU_SEARCH';
UPDATE `PREFIX_configuration` SET value = 'http://www.facebook.com/prestashop' WHERE name = 'blocksocial_facebook';
UPDATE `PREFIX_configuration` SET value = 'http://www.twitter.com/prestashop' WHERE name = 'blocksocial_twitter';
UPDATE `PREFIX_configuration` SET value = 'http://www.prestashop.com/blog/en/feed/' WHERE name = 'blocksocial_rss';
UPDATE `PREFIX_configuration` SET value = 'My Company' WHERE name = 'blockcontactinfos_company';
UPDATE `PREFIX_configuration` SET value = '42 avenue des Champs Elysées\n75000 Paris\nFrance' WHERE name = 'blockcontactinfos_address';
UPDATE `PREFIX_configuration` SET value = '0123-456-789' WHERE name = 'blockcontactinfos_phone';
UPDATE `PREFIX_configuration` SET value = 'sales@yourcompany.com' WHERE name = 'blockcontactinfos_email';
UPDATE `PREFIX_configuration` SET value = '0123-456-789' WHERE name = 'blockcontact_telnumber';
UPDATE `PREFIX_configuration` SET value = 'sales@yourcompany.com' WHERE name = 'blockcontact_email';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'SUPPLIER_DISPLAY_TEXT';
UPDATE `PREFIX_configuration` SET value = '5' WHERE name = 'SUPPLIER_DISPLAY_TEXT_NB';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'SUPPLIER_DISPLAY_FORM';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'BLOCK_CATEG_NBR_COLUMN_FOOTER';
UPDATE `PREFIX_configuration` SET value = '' WHERE name = 'UPGRADER_BACKUPDB_FILENAME';
UPDATE `PREFIX_configuration` SET value = '' WHERE name = 'UPGRADER_BACKUPFILES_FILENAME';

/* No right column */
DELETE FROM `PREFIX_hook_module` WHERE id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayRightColumn');

/* displayTop */
SET @id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop');
UPDATE `PREFIX_hook_module` SET position = 1
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksearch')
AND id_hook = @id_hook;

UPDATE `PREFIX_hook_module` SET position = 2
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockuserinfo')
AND id_hook = @id_hook;

UPDATE `PREFIX_hook_module` SET position = 3
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocktopmenu')
AND id_hook = @id_hook;

UPDATE `PREFIX_hook_module` SET position = 4
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcart')
AND id_hook = @id_hook;

/* displayHomeTab && displayHomeTabContent */
SET @id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHomeTab');
SET @id_hook2 = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHomeTabContent');
UPDATE `PREFIX_hook_module` SET position = 1
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocknewproducts')
AND id_hook IN (@id_hook, @id_hook2);

UPDATE `PREFIX_hook_module` SET position = 2
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'homefeatured')
AND id_hook IN (@id_hook, @id_hook2);

UPDATE `PREFIX_hook_module` SET position = 3
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockbestsellers')
AND id_hook IN (@id_hook, @id_hook2);

/* displayFooter */
SET @id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayFooter');
UPDATE `PREFIX_hook_module` SET position = 1
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocknewsletter')
AND id_hook = @id_hook;

UPDATE `PREFIX_hook_module` SET position = 2
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksocial')
AND id_hook = @id_hook;

UPDATE `PREFIX_hook_module` SET position = 3
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcategories')
AND id_hook = @id_hook;

UPDATE `PREFIX_hook_module` SET position = 4
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcms')
AND id_hook = @id_hook;

UPDATE `PREFIX_hook_module` SET position = 5
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockmyaccountfooter')
AND id_hook = @id_hook;

UPDATE `PREFIX_hook_module` SET position = 6
WHERE id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcontactinfos')
AND id_hook = @id_hook;

/* Exceptions for pages without left column */
SET @id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn');
INSERT INTO `PREFIX_hook_module_exceptions` (`id_shop`, `id_module`, `id_hook`, `file_name`) (
	SELECT 1, id_module, id_hook, pages.page
	FROM `PREFIX_hook_module`
	JOIN (
		SELECT 'index' as page
		UNION SELECT 'auth' as page
		UNION SELECT 'cart' as page
		UNION SELECT 'order' as page
		UNION SELECT 'orderopc' as page
		UNION SELECT 'sitemap' as page
		UNION SELECT 'stores' as page
		UNION SELECT 'cms' as page
		UNION SELECT 'contact' as page
		UNION SELECT 'myaccount' as page
		UNION SELECT 'identity' as page
		UNION SELECT 'address' as page
		UNION SELECT 'addresses' as page
		UNION SELECT 'pagenotfound' as page
		UNION SELECT 'password' as page
		UNION SELECT 'orderfollow' as page
		UNION SELECT 'orderslip' as page
		UNION SELECT 'discount' as page
		UNION SELECT 'product' as page
	) pages
	WHERE id_hook = @id_hook
);


