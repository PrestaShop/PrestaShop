SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionPresentCart', 'Cart Presenter', 'This hook is called before a cart is presented', '1'),
  (NULL, 'actionPresentOrder', 'Order footer', 'This hook is called before an order is presented', '1'),
  (NULL, 'actionPresentOrderReturn', 'Order Return Presenter', 'This hook is called before an order return is presented', '1'),
  (NULL, 'actionPresentProduct', 'Product Presenter', 'This hook is called before a product is presented', '1'),
  (NULL, 'displayBanner', 'Display Banner', 'Use this hook for banners on top of every pages', '1'),
  (NULL, 'actionModuleUninstallBefore', 'Module uninstall before', 'This hook is called before module uninstall process', '1'),
  (NULL, 'actionModuleUninstallAfter', 'Module uninstall after', 'This hook is called at the end of module uninstall process', '1'),
  (NULL, 'displayCartModalContent', 'Cart Presenter', 'This hook displays content in the middle of the window that appears after adding product to cart', '1'),
  (NULL, 'displayCartModalFooter', 'Cart Presenter', 'This hook displays content in the bottom of window that appears after adding product to cart', '1'),
  (NULL, 'displayHeaderCategory', 'Category header', 'This hook adds new blocks above the products listing in a category/search', '1'),
  (NULL, 'actionCheckoutRender', 'Checkout process render', 'This hook is called when checkout process is constructed', '1'),
  (NULL, 'actionPresentProductListing', 'Product Listing Presenter', 'This hook is called before a product listing is presented', '1'),
  (NULL, 'actionGetProductPropertiesAfterUnitPrice', 'Product Properties', 'This hook is called after defining the properties of a product', '1'),
  (NULL, 'actionProductSearchProviderRunQueryBefore', 'Runs an action before ProductSearchProviderInterface::RunQuery()', 'Required to modify an SQL query before executing it', '1'),
  (NULL, 'actionProductSearchProviderRunQueryAfter', 'Runs an action after ProductSearchProviderInterface::RunQuery()', 'Required to return a previous state of an SQL query or/and to change a result of the SQL query after executing it', '1'),
  (NULL, 'actionOverrideEmployeeImage', 'Override Employee Image', 'This hook is used to override the employee image', '1'),
  (NULL, 'actionFrontControllerSetVariables', 'Add variables in JavaScript object and Smarty templates', 'Add variables to javascript object that is available in Front Office. These are also available in smarty templates in modules.your_module_name.', '1'),
  (NULL, 'displayAdminGridTableBefore', 'Display before Grid table', 'This hook adds new blocks before Grid component table.', '1'),
  (NULL, 'displayAdminGridTableAfter', 'Display after Grid table', 'This hook adds new blocks after Grid component table.', '1'),
  (NULL, 'displayAdminOrderCreateExtraButtons', 'Add buttons on the create order page dropdown', 'Add buttons on the create order page dropdown', '1'),
  (NULL, 'actionFeatureFlagForm', 'Modify feature flag options form content', 'This hook allows to modify the Feature Flag page form FormBuilder', 1),
  (NULL, 'actionFeatureFlagSave', 'Modify feature flag options form saved data', 'This hook allows to modify the Feature Flag data being submitted through the form after it was saved', 1),
  (NULL, 'actionProductFormBuilderModifier', 'Modify product identifiable object form', 'This hook allows to modify product identifiable object form content by modifying form builder data or FormBuilder itself', '1'),
  (NULL, 'actionBeforeUpdateProductFormHandler', 'Modify product identifiable object data before updating it', 'This hook allows to modify product identifiable object form data before it was updated', '1'),
  (NULL, 'actionAfterUpdateProductFormHandler', 'Modify product identifiable object data after updating it', 'This hook allows to modify product identifiable object form data after it was updated', '1'),
  (NULL, 'actionBeforeCreateProductFormHandler', 'Modify product identifiable object data before creating it', 'This hook allows to modify product identifiable object form data before it was created', '1'),
  (NULL, 'actionAfterCreateProductFormHandler', 'Modify product identifiable object data after creating it', 'This hook allows to modify product identifiable object form data after it was created', '1'),
  (NULL,'actionCustomerAddressGridDefinitionModifier','Modify customer address grid definition','This hook allows to alter customer address grid columns, actions and filters','1'),
  (NULL,'actionCartRuleGridDefinitionModifier','Modify cart rule grid definition','This hook allows to alter cart rule grid columns, actions and filters','1'),
  (NULL,'actionOrderStatesGridDefinitionModifier','Modify order states grid definition','This hook allows to alter order states grid columns, actions and filters','1'),
  (NULL,'actionOrderReturnStatesGridDefinitionModifier','Modify order return states grid definition','This hook allows to alter order return states grid columns, actions and filters','1'),
  (NULL,'actionOutstandingGridDefinitionModifier','Modify outstanding grid definition','This hook allows to alter outstanding grid columns, actions and filters','1'),
  (NULL,'actionCarrierGridDefinitionModifier','Modify carrier grid definition','This hook allows to alter carrier grid columns, actions and filters','1'),
  (NULL,'actionZoneGridDefinitionModifier','Modify zone grid definition','This hook allows to alter zone grid columns, actions and filters','1'),
  (NULL,'actionCustomerDiscountGridQueryBuilderModifier','Modify customer discount grid query builder','This hook allows to alter Doctrine query builder for customer discount grid','1'),
  (NULL,'actionCustomerAddressGridQueryBuilderModifier','Modify customer address grid query builder','This hook allows to alter Doctrine query builder for customer address grid','1'),
  (NULL,'actionCartRuleGridQueryBuilderModifier','Modify cart rule grid query builder','This hook allows to alter Doctrine query builder for cart rule grid','1'),
  (NULL,'actionOrderStatesGridQueryBuilderModifier','Modify order states grid query builder','This hook allows to alter Doctrine query builder for order states grid','1'),
  (NULL,'actionOrderReturnStatesGridQueryBuilderModifier','Modify order return states grid query builder','This hook allows to alter Doctrine query builder for order return states grid','1'),
  (NULL,'actionOutstandingGridQueryBuilderModifier','Modify outstanding grid query builder','This hook allows to alter Doctrine query builder for outstanding grid','1'),
  (NULL,'actionCarrierGridQueryBuilderModifier','Modify carrier grid query builder','This hook allows to alter Doctrine query builder for carrier grid','1'),
  (NULL,'actionZoneGridQueryBuilderModifier','Modify zone grid query builder','This hook allows to alter Doctrine query builder for zone grid','1'),
  (NULL,'actionCustomerDiscountGridDataModifier','Modify customer discount grid data','This hook allows to modify customer discount grid data','1'),
  (NULL,'actionCustomerAddressGridDataModifier','Modify customer address grid data','This hook allows to modify customer address grid data','1'),
  (NULL,'actionCartRuleGridDataModifier','Modify cart rule grid data','This hook allows to modify cart rule grid data','1'),
  (NULL,'actionOrderStatesGridDataModifier','Modify order states grid data','This hook allows to modify order states grid data','1'),
  (NULL,'actionOrderReturnStatesGridDataModifier','Modify order return states grid data','This hook allows to modify order return states grid data','1'),
  (NULL,'actionOutstandingGridDataModifier','Modify outstanding grid data','This hook allows to modify outstanding grid data','1'),
  (NULL,'actionCarrierGridDataModifier','Modify carrier grid data','This hook allows to modify carrier grid data','1'),
  (NULL,'actionZoneGridDataModifier','Modify zone grid data','This hook allows to modify zone grid data','1'),
  (NULL,'actionCustomerDiscountGridFilterFormModifier','Modify customer discount grid filters','This hook allows to modify filters for customer discount grid','1'),
  (NULL,'actionCustomerAddressGridFilterFormModifier','Modify customer address grid filters','This hook allows to modify filters for customer address grid','1'),
  (NULL,'actionCartRuleGridFilterFormModifier','Modify cart rule grid filters','This hook allows to modify filters for cart rule grid','1'),
  (NULL,'actionOrderStatesGridFilterFormModifier','Modify order states grid filters','This hook allows to modify filters for order states grid','1'),
  (NULL,'actionOrderReturnStatesGridFilterFormModifier','Modify order return states grid filters','This hook allows to modify filters for order return states grid','1'),
  (NULL,'actionOutstandingGridFilterFormModifier','Modify outstanding grid filters','This hook allows to modify filters for outstanding grid','1'),
  (NULL,'actionCarrierGridFilterFormModifier','Modify carrier grid filters','This hook allows to modify filters for carrier grid','1'),
  (NULL,'actionZoneGridFilterFormModifier','Modify zone grid filters','This hook allows to modify filters for zone grid','1'),
  (NULL,'actionCustomerDiscountGridPresenterModifier','Modify customer discount grid template data','This hook allows to modify data which is about to be used in template for customer discount grid','1'),
  (NULL,'actionCustomerAddressGridPresenterModifier','Modify customer address grid template data','This hook allows to modify data which is about to be used in template for customer address grid','1'),
  (NULL,'actionCartRuleGridPresenterModifier','Modify cart rule grid template data','This hook allows to modify data which is about to be used in template for cart rule grid','1'),
  (NULL,'actionOrderStatesGridPresenterModifier','Modify order states grid template data','This hook allows to modify data which is about to be used in template for order states grid','1'),
  (NULL,'actionOrderReturnStatesGridPresenterModifier','Modify order return states grid template data','This hook allows to modify data which is about to be used in template for order return states grid','1'),
  (NULL,'actionOutstandingGridPresenterModifier','Modify outstanding grid template data','This hook allows to modify data which is about to be used in template for outstanding grid','1'),
  (NULL,'actionCarrierGridPresenterModifier','Modify carrier grid template data','This hook allows to modify data which is about to be used in template for carrier grid','1'),
  (NULL,'actionZoneGridPresenterModifier','Modify zone grid template data','This hook allows to modify data which is about to be used in template for zone grid','1'),
  (NULL,'actionCustomerDiscountGridDefinitionModifier','Modify customer discount grid definition','This hook allows to alter customer discount grid columns, actions and filters','1'),
  (NULL,'actionPerformancePageSmartyForm','Modify performance page smarty options form content','This hook allows to modify performance page smarty options form FormBuilder','1'),
  (NULL,'actionPerformancePageDebugModeForm','Modify performance page debug mode options form content','This hook allows to modify performance page debug mode options form FormBuilder','1'),
  (NULL,'actionPerformancePageOptionalFeaturesForm','Modify performance page optional features options form content','This hook allows to modify performance page optional features options form FormBuilder','1'),
  (NULL,'actionPerformancePageCombineCompressCacheForm','Modify performance page combine compress cache options form content','This hook allows to modify performance page combine compress cache options form FormBuilder','1'),
  (NULL,'actionPerformancePageMediaServersForm','Modify performance page media servers options form content','This hook allows to modify performance page media servers options form FormBuilder','1'),
  (NULL,'actionPerformancePagecachingForm','Modify performance pagecaching options form content','This hook allows to modify performance pagecaching options form FormBuilder','1'),
  (NULL,'actionAdministrationPageGeneralForm','Modify administration page general options form content','This hook allows to modify administration page general options form FormBuilder','1'),
  (NULL,'actionAdministrationPageUploadQuotaForm','Modify administration page upload quota options form content','This hook allows to modify administration page upload quota options form FormBuilder','1'),
  (NULL,'actionAdministrationPageNotificationsForm','Modify administration page notifications options form content','This hook allows to modify administration page notifications options form FormBuilder','1'),
  (NULL,'actionShippingPreferencesPageHandlingForm','Modify shipping preferences page handling options form content','This hook allows to modify shipping preferences page handling options form FormBuilder','1'),
  (NULL,'actionShippingPreferencesPageCarrierOptionsForm','Modify shipping preferences page carrier options options form content','This hook allows to modify shipping preferences page carrier options options form FormBuilder','1'),
  (NULL,'actionOrderPreferencesPageGeneralForm','Modify order preferences page general options form content','This hook allows to modify order preferences page general options form FormBuilder','1'),
  (NULL,'actionOrderPreferencesPageGiftOptionsForm','Modify order preferences page gift options options form content','This hook allows to modify order preferences page gift options options form FormBuilder','1'),
  (NULL,'actionProductPreferencesPageGeneralForm','Modify product preferences page general options form content','This hook allows to modify product preferences page general options form FormBuilder','1'),
  (NULL,'actionProductPreferencesPagePaginationForm','Modify product preferences page pagination options form content','This hook allows to modify product preferences page pagination options form FormBuilder','1'),
  (NULL,'actionProductPreferencesPagePageForm','Modify product preferences page page options form content','This hook allows to modify product preferences page page options form FormBuilder','1'),
  (NULL,'actionProductPreferencesPageStockForm','Modify product preferences page stock options form content','This hook allows to modify product preferences page stock options form FormBuilder','1'),
  (NULL,'actionGeolocationPageByAddressForm','Modify geolocation page by address options form content','This hook allows to modify geolocation page by address options form FormBuilder','1'),
  (NULL,'actionGeolocationPageWhitelistForm','Modify geolocation page whitelist options form content','This hook allows to modify geolocation page whitelist options form FormBuilder','1'),
  (NULL,'actionGeolocationPageOptionsForm','Modify geolocation page options options form content','This hook allows to modify geolocation page options options form FormBuilder','1'),
  (NULL,'actionLocalizationPageConfigurationForm','Modify localization page configuration options form content','This hook allows to modify localization page configuration options form FormBuilder','1'),
  (NULL,'actionLocalizationPageLocalUnitsForm','Modify localization page local units options form content','This hook allows to modify localization page local units options form FormBuilder','1'),
  (NULL,'actionLocalizationPageAdvancedForm','Modify localization page advanced options form content','This hook allows to modify localization page advanced options form FormBuilder','1'),
  (NULL,'actionFeatureFlagForm','Modify feature flag options form content','This hook allows to modify feature flag options form FormBuilder','1'),
  (NULL,'actionPerformancePageSmartySave','Modify performance page smarty options form saved data','This hook allows to modify data of performance page smarty options form after it was saved','1'),
  (NULL,'actionPerformancePageDebugModeSave','Modify performance page debug mode options form saved data','This hook allows to modify data of performance page debug mode options form after it was saved','1'),
  (NULL,'actionPerformancePageOptionalFeaturesSave','Modify performance page optional features options form saved data','This hook allows to modify data of performance page optional features options form after it was saved','1'),
  (NULL,'actionPerformancePageCombineCompressCacheSave','Modify performance page combine compress cache options form saved data','This hook allows to modify data of performance page combine compress cache options form after it was saved','1'),
  (NULL,'actionPerformancePageMediaServersSave','Modify performance page media servers options form saved data','This hook allows to modify data of performance page media servers options form after it was saved','1'),
  (NULL,'actionPerformancePagecachingSave','Modify performance pagecaching options form saved data','This hook allows to modify data of performance pagecaching options form after it was saved','1'),
  (NULL,'actionAdministrationPageGeneralSave','Modify administration page general options form saved data','This hook allows to modify data of administration page general options form after it was saved','1'),
  (NULL,'actionAdministrationPageUploadQuotaSave','Modify administration page upload quota options form saved data','This hook allows to modify data of administration page upload quota options form after it was saved','1'),
  (NULL,'actionAdministrationPageNotificationsSave','Modify administration page notifications options form saved data','This hook allows to modify data of administration page notifications options form after it was saved','1'),
  (NULL,'actionShippingPreferencesPageHandlingSave','Modify shipping preferences page handling options form saved data','This hook allows to modify data of shipping preferences page handling options form after it was saved','1'),
  (NULL,'actionShippingPreferencesPageCarrierOptionsSave','Modify shipping preferences page carrier options options form saved data','This hook allows to modify data of shipping preferences page carrier options options form after it was saved','1'),
  (NULL,'actionOrderPreferencesPageGeneralSave','Modify order preferences page general options form saved data','This hook allows to modify data of order preferences page general options form after it was saved','1'),
  (NULL,'actionOrderPreferencesPageGiftOptionsSave','Modify order preferences page gift options options form saved data','This hook allows to modify data of order preferences page gift options options form after it was saved','1'),
  (NULL,'actionProductPreferencesPageGeneralSave','Modify product preferences page general options form saved data','This hook allows to modify data of product preferences page general options form after it was saved','1'),
  (NULL,'actionProductPreferencesPagePaginationSave','Modify product preferences page pagination options form saved data','This hook allows to modify data of product preferences page pagination options form after it was saved','1'),
  (NULL,'actionProductPreferencesPagePageSave','Modify product preferences page page options form saved data','This hook allows to modify data of product preferences page page options form after it was saved','1'),
  (NULL,'actionProductPreferencesPageStockSave','Modify product preferences page stock options form saved data','This hook allows to modify data of product preferences page stock options form after it was saved','1'),
  (NULL,'actionGeolocationPageByAddressSave','Modify geolocation page by address options form saved data','This hook allows to modify data of geolocation page by address options form after it was saved','1'),
  (NULL,'actionGeolocationPageWhitelistSave','Modify geolocation page whitelist options form saved data','This hook allows to modify data of geolocation page whitelist options form after it was saved','1'),
  (NULL,'actionGeolocationPageOptionsSave','Modify geolocation page options options form saved data','This hook allows to modify data of geolocation page options options form after it was saved','1'),
  (NULL,'actionLocalizationPageConfigurationSave','Modify localization page configuration options form saved data','This hook allows to modify data of localization page configuration options form after it was saved','1'),
  (NULL,'actionLocalizationPageLocalUnitsSave','Modify localization page local units options form saved data','This hook allows to modify data of localization page local units options form after it was saved','1'),
  (NULL,'actionLocalizationPageAdvancedSave','Modify localization page advanced options form saved data','This hook allows to modify data of localization page advanced options form after it was saved','1'),
  (NULL,'actionFeatureFlagSave','Modify feature flag options form saved data','This hook allows to modify data of feature flag options form after it was saved','1'),
  (NULL,'actionOrderStateFormBuilderModifier','Modify order state identifiable object form','This hook allows to modify order state identifiable object forms content by modifying form builder data or FormBuilder itself','1'),
  (NULL,'actionOrderReturnStateFormBuilderModifier','Modify order return state identifiable object form','This hook allows to modify order return state identifiable object forms content by modifying form builder data or FormBuilder itself','1'),
  (NULL,'actionZoneFormBuilderModifier','Modify zone identifiable object form','This hook allows to modify zone identifiable object forms content by modifying form builder data or FormBuilder itself','1'),
  (NULL,'actionBeforeUpdateOrderStateFormHandler','Modify order state identifiable object data before updating it','This hook allows to modify order state identifiable object forms data before it was updated','1'),
  (NULL,'actionBeforeUpdateOrderReturnStateFormHandler','Modify order return state identifiable object data before updating it','This hook allows to modify order return state identifiable object forms data before it was updated','1'),
  (NULL,'actionBeforeUpdateZoneFormHandler','Modify zone identifiable object data before updating it','This hook allows to modify zone identifiable object forms data before it was updated','1'),
  (NULL,'actionAfterUpdateOrderStateFormHandler','Modify order state identifiable object data after updating it','This hook allows to modify order state identifiable object forms data after it was updated','1'),
  (NULL,'actionAfterUpdateOrderReturnStateFormHandler','Modify order return state identifiable object data after updating it','This hook allows to modify order return state identifiable object forms data after it was updated','1'),
  (NULL,'actionAfterUpdateZoneFormHandler','Modify zone identifiable object data after updating it','This hook allows to modify zone identifiable object forms data after it was updated','1'),
  (NULL,'actionBeforeCreateOrderStateFormHandler','Modify order state identifiable object data before creating it','This hook allows to modify order state identifiable object forms data before it was created','1'),
  (NULL,'actionBeforeCreateOrderReturnStateFormHandler','Modify order return state identifiable object data before creating it','This hook allows to modify order return state identifiable object forms data before it was created','1'),
  (NULL,'actionBeforeCreateZoneFormHandler','Modify zone identifiable object data before creating it','This hook allows to modify zone identifiable object forms data before it was created','1'),
  (NULL,'actionAfterCreateOrderStateFormHandler','Modify order state identifiable object data after creating it','This hook allows to modify order state identifiable object forms data after it was created','1'),
  (NULL,'actionAfterCreateOrderReturnStateFormHandler','Modify order return state identifiable object data after creating it','This hook allows to modify order return state identifiable object forms data after it was created','1'),
  (NULL,'actionAfterCreateZoneFormHandler','Modify zone identifiable object data after creating it','This hook allows to modify zone identifiable object forms data after it was created','1')
;

ALTER TABLE `PREFIX_employee` ADD `has_enabled_gravatar` TINYINT UNSIGNED DEFAULT 0 NOT NULL;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
    ('PS_COOKIE_SAMESITE', 'Lax', NOW(), NOW()),
    ('PS_SHOW_LABEL_OOS_LISTING_PAGES', '1', NOW(), NOW()),
    ('ADDONS_API_MODULE_CHANNEL', 'stable', NOW(), NOW())
;

ALTER TABLE `PREFIX_hook` ADD `active` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL AFTER `description`;

ALTER TABLE `PREFIX_orders` ADD COLUMN `note` TEXT AFTER `date_upd`;

ALTER TABLE `PREFIX_currency` CHANGE `numeric_iso_code` `numeric_iso_code` varchar(3) NULL DEFAULT NULL;

UPDATE `PREFIX_configuration` SET `value` = '4' WHERE `name` = 'PS_LOGS_BY_EMAIL' AND `value` = '5';
ALTER TABLE `PREFIX_log`
  ADD `id_shop` INT(10) NULL DEFAULT NULL after `object_id`,
  ADD `id_shop_group` INT(10) NULL DEFAULT NULL after `id_shop`,
  ADD `id_lang` INT(10) NULL DEFAULT NULL after `id_shop_group`,
  ADD `in_all_shops` TINYINT(1) unsigned NOT NULL DEFAULT '0'
;

ALTER TABLE `PREFIX_tab` ADD `wording` VARCHAR(255) DEFAULT NULL AFTER `icon`;
ALTER TABLE `PREFIX_tab` ADD `wording_domain` VARCHAR(255) DEFAULT NULL AFTER `wording`;

UPDATE `PREFIX_product` SET `location` = '' WHERE `location` IS NULL;
ALTER TABLE `PREFIX_product` MODIFY COLUMN `location` VARCHAR(255) NOT NULL DEFAULT '';
UPDATE `PREFIX_product_attribute` SET `location` = '' WHERE `location` IS NULL;
ALTER TABLE `PREFIX_product_attribute` MODIFY COLUMN `location` VARCHAR(255) NOT NULL DEFAULT '';

UPDATE `PREFIX_product` SET `redirect_type` = '404' WHERE `redirect_type` = '';
ALTER TABLE `PREFIX_product` MODIFY COLUMN `redirect_type` ENUM(
    '404', '301-product', '302-product', '301-category', '302-category'
) NOT NULL DEFAULT '404';

ALTER TABLE  `PREFIX_product` ADD `product_type` ENUM(
    'standard', 'pack', 'virtual', 'combinations'
) NOT NULL DEFAULT 'standard';

/* First set all products to standard type, then update them based on cached columns that identify the type */
UPDATE `PREFIX_product` SET `product_type` = "standard";
UPDATE `PREFIX_product` SET `product_type` = "combinations" WHERE `cache_default_attribute` != 0;
UPDATE `PREFIX_product` SET `product_type` = "pack" WHERE `cache_is_pack` = 1;
UPDATE `PREFIX_product` SET `product_type` = "virtual" WHERE `is_virtual` = 1;

/* PHP:ps_1780_add_feature_flag_tab(); */;

/* this table should be created by Doctrine but we need to perform INSERT and the 1.7.8.0.sql script is called
before Doctrine schema update */
/* consequently we create the table manually */
CREATE TABLE IF NOT EXISTS `PREFIX_feature_flag` (
  `id_feature_flag` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) COLLATE utf8mb4_general_ci NOT NULL,
  `state` TINYINT(1) NOT NULL DEFAULT '0',
  `label_wording` VARCHAR(191) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `label_domain` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `description_wording` VARCHAR(191) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `description_domain` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_feature_flag`),
  UNIQUE KEY `UNIQ_91700F175E237E06` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `PREFIX_feature_flag` (`name`, `state`, `label_wording`, `label_domain`, `description_wording`, `description_domain`)
VALUES
	('product_page_v2', 0, 'Experimental product page', 'Admin.Advparameters.Feature', 'This page benefits from increased performance and includes new features such as a new combination management system. Please note this is a work in progress and some features are not available yet.', 'Admin.Advparameters.Help');
