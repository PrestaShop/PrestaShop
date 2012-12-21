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
UPDATE `PREFIX_configuration` SET value = 'RSS' WHERE name = 'blocksocial_rss';
UPDATE `PREFIX_configuration` SET value = 'My Company' WHERE name = 'blockcontactinfos_company';
UPDATE `PREFIX_configuration` SET value = '42 avenue des Champs Elysées\n75000 Paris\nFrance' WHERE name = 'blockcontactinfos_address';
UPDATE `PREFIX_configuration` SET value = '+33 (0)1.23.45.67.89' WHERE name = 'blockcontactinfos_phone';
UPDATE `PREFIX_configuration` SET value = 'sales@yourcompany.com' WHERE name = 'blockcontactinfos_email';
UPDATE `PREFIX_configuration` SET value = '+33 (0)1.23.45.67.89' WHERE name = 'blockcontact_telnumber';
UPDATE `PREFIX_configuration` SET value = 'sales@yourcompany.com' WHERE name = 'blockcontact_email';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'SUPPLIER_DISPLAY_TEXT';
UPDATE `PREFIX_configuration` SET value = '5' WHERE name = 'SUPPLIER_DISPLAY_TEXT_NB';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'SUPPLIER_DISPLAY_FORM';
UPDATE `PREFIX_configuration` SET value = '1' WHERE name = 'BLOCK_CATEG_NBR_COLUMN_FOOTER';
UPDATE `PREFIX_configuration` SET value = '' WHERE name = 'UPGRADER_BACKUPDB_FILENAME';
UPDATE `PREFIX_configuration` SET value = '' WHERE name = 'UPGRADER_BACKUPFILES_FILENAME';
UPDATE `PREFIX_configuration` SET value = '5' WHERE name = 'blockreinsurance_nbblocks';

UPDATE `PREFIX_hook_module` SET position = 1
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayPayment') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'cheque')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayPaymentReturn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'cheque')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHome') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'homeslider')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'actionAuthentication') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsdata')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'actionShopDataDuplication') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'homeslider')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocklanguages')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'actionCustomerAccountAdd') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsdata')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayCustomerAccount') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'favoriteproducts')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsvisits')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsGraphEngine') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'graphvisifire')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsGridEngine') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'gridhtml')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumnProduct') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksharefb')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'actionSearch') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statssearch')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'actionCategoryAdd') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcategories')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'actionCategoryUpdate') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcategories')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'actionCategoryDelete') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcategories')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'actionAdminMetaSave') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcategories')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayMyAccountBlock') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'favoriteproducts')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayFooter') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockreinsurance');

UPDATE `PREFIX_hook_module` SET position = 2
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcurrencies')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayFooter') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcategories')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statssales')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsGraphEngine') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'graphxmlswfcharts')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumnProduct') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'favoriteproducts')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayPayment') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'bankwire')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayPaymentReturn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'bankwire')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayRightColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocknewproducts')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocktags')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHome') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'homefeatured')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockpaymentlogo')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminOrder') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsorigin');

UPDATE `PREFIX_hook_module` SET position = 3
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayPayment') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'moneybookers')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayPaymentReturn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'moneybookers')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcategories')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockpermanentlinks')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockpermanentlinks')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayFooter') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockmyaccount')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsGraphEngine') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'graphgooglechart')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsregistrations');

UPDATE `PREFIX_hook_module` SET position = 4
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayRightColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockspecials')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockviewed')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockviewed')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksearch')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayFooter') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcms')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statspersonalinfos')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsGraphEngine') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'graphartichow');

UPDATE `PREFIX_hook_module` SET position = 5
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayRightColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcms')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksupplier')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockmanufacturer')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcart')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksocial')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockuserinfo')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statslive')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayFooter') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksocial');

UPDATE `PREFIX_hook_module` SET position = 6
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayRightColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockstore')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcms')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockmyaccount')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocktopmenu')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsequipment')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayFooter') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcontactinfos');

UPDATE `PREFIX_hook_module` SET position = 7
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayRightColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcontact')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockadvertising')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcart')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'sekeywords')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayFooter') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksharefb')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayFooter') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsdata')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statscatalog')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = '') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = '');

UPDATE `PREFIX_hook_module` SET position = 8
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockpaymentlogo')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayTop') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'pagesnotfound')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsbestcustomers');

UPDATE `PREFIX_hook_module` SET position = 9
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocknewsletter')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcategories')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsorigin');

UPDATE `PREFIX_hook_module` SET position = 10
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockspecials')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'pagesnotfound');

UPDATE `PREFIX_hook_module` SET position = 11
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcurrencies')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'sekeywords');

UPDATE `PREFIX_hook_module` SET position = 12
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocknewproducts')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsproduct');

UPDATE `PREFIX_hook_module` SET position = 13
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockuserinfo')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsbestproducts');

UPDATE `PREFIX_hook_module` SET position = 14
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocklanguages')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'displayAdminStatsModules');

UPDATE `PREFIX_hook_module` SET position = 15
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockmanufacturer')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsbestcategories');

UPDATE `PREFIX_hook_module` SET position = 16
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcms')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsbestsuppliers');

UPDATE `PREFIX_hook_module` SET position = 17
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockadvertising')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statscarrier');

UPDATE `PREFIX_hook_module` SET position = 18
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocktags')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsnewsletter');

UPDATE `PREFIX_hook_module` SET position = 19
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockstore')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statssearch');

UPDATE `PREFIX_hook_module` SET position = 20
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksearch')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statscheckup');

UPDATE `PREFIX_hook_module` SET position = 21
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcontactinfos')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsstock');

UPDATE `PREFIX_hook_module` SET position = 22
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocktopmenu')
	OR
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayAdminStatsModules') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'statsforecast');

UPDATE `PREFIX_hook_module` SET position = 23
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'favoriteproducts');

UPDATE `PREFIX_hook_module` SET position = 24
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'homefeatured');

UPDATE `PREFIX_hook_module` SET position = 25
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocknewsletter');

UPDATE `PREFIX_hook_module` SET position = 26
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayRightColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockcontact');

UPDATE `PREFIX_hook_module` SET position = 27
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blocksupplier');

UPDATE `PREFIX_hook_module` SET position = 28
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayHeader') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'feeder');

DELETE FROM `PREFIX_hook_module` 
WHERE
	id_hook = (SELECT id_hook FROM `PREFIX_hook` WHERE name = 'displayLeftColumn') AND id_module = (SELECT id_module FROM `PREFIX_module` WHERE name = 'blockmyaccount')

