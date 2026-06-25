# Routes Index (generated 2026-04-25)
# ~812 routes across admin / admin-api / api
#
# Paths are relative to the routing file's prefix (see parent _*.yml for full prefix).
# Route name is the canonical identifier — use it with $this->generateUrl() or $router->generate().

## admin

### admin/configure/advanced_parameters/admin_api
```
GET           /                                         admin_api_index  [AdminAPIController::indexAction]
POST          /                                         admin_api_clients_process_configuration  [AdminAPIController::processConfigurationAction]
GET,POST,PATCH  /api-clients/create                       admin_api_clients_create  [AdminAPIController::createAction]
GET,POST,PATCH  /api-clients/{apiClientId}/edit           admin_api_clients_edit  [AdminAPIController::editAction]
POST          /api-clients/{apiClientId}/toggle-active  admin_api_clients_toggle_active  [AdminAPIController::toggleStatusAction]
POST          /api-clients/{apiClientId}/delete         admin_api_clients_delete  [AdminAPIController::deleteAction]
POST          /api-clients/{apiClientId}/regenerate-secret  admin_api_clients_regenerate_secret  [AdminAPIController::regenerateSecretAction]
```

### admin/configure/advanced_parameters/administration
```
GET           /                                         admin_administration  [AdministrationController::indexAction]
POST          /general                                  admin_administration_general_save  [AdministrationController::processGeneralFormAction]
POST          /upload-quota                             admin_administration_upload_quota_save  [AdministrationController::processUploadQuotaFormAction]
POST          /notifications                            admin_administration_notifications_save  [AdministrationController::processNotificationsFormAction]
```

### admin/configure/advanced_parameters/backup
```
GET           /                                         admin_backups_index  [BackupController::indexAction]
POST          /                                         admin_backups_save_options  [BackupController::saveOptionsAction]
POST          /new                                      admin_backups_create  [BackupController::createAction]
GET           /view/{downloadFileName}                  admin_backups_download_view  [BackupController::downloadViewAction]
GET           /download/{downloadFileName}              admin_backup_download  [BackupController::downloadContentAction]
DELETE,POST   /{deleteFileName}                         admin_backups_delete  [BackupController::deleteAction]
POST          /bulk-delete/                             admin_backups_bulk_delete  [BackupController::bulkDeleteAction]
```

### admin/configure/advanced_parameters/email
```
GET           /                                         admin_emails_index  [EmailController::indexAction]
POST          /                                         admin_emails_search  [CommonController::searchGridAction]
GET           /options                                  admin_emails_save_options_get  [EmailController::indexAction]
POST          /options                                  admin_emails_save_options  [EmailController::saveOptionsAction]
POST          /send-testing-email                       admin_emails_send_test  [EmailController::sendTestAction]
POST          /delete-bulk                              admin_emails_delete_bulk  [EmailController::deleteBulkAction]
POST          /delete-all                               admin_emails_delete_all  [EmailController::deleteAllAction]
POST          /delete/{mailId}                          admin_emails_delete  [EmailController::deleteAction]
```

### admin/configure/advanced_parameters/employee
```
GET           /                                         admin_employees_index  [EmployeeController::indexAction]
POST          /                                         admin_employees_search  [CommonController::searchGridAction]
POST          /save-options                             admin_employees_save_options  [EmployeeController::saveOptionsAction]
POST          /{employeeId}/toggle-status               admin_employees_toggle_status  [EmployeeController::toggleStatusAction]
POST          /bulk-enable-status                       admin_employees_bulk_enable_status  [EmployeeController::bulkStatusEnableAction]
POST          /bulk-disable-status                      admin_employees_bulk_disable_status  [EmployeeController::bulkStatusDisableAction]
POST          /{employeeId}/delete                      admin_employees_delete  [EmployeeController::deleteAction]
POST          /bulk-delete                              admin_employees_bulk_delete  [EmployeeController::bulkDeleteAction]
GET,POST      /new                                      admin_employees_create  [EmployeeController::createAction]
GET,POST      /{employeeId}/edit                        admin_employees_edit  [EmployeeController::editAction]
POST          /toggle-navigation                        admin_employees_toggle_navigation  [EmployeeController::toggleNavigationMenuAction]
POST          /change-form-language                     admin_employees_change_form_language  [EmployeeController::changeFormLanguageAction]
GET           /tabs                                     admin_employees_get_tabs  [EmployeeController::getAccessibleTabsAction]
GET           /password_generated                       admin_employees_get_password_generated  [EmployeeController::generatePasswordAction]
```

### admin/configure/advanced_parameters/feature_flags
```
GET,POST      /                                         admin_feature_flags_index  [FeatureFlagController::indexAction]
```

### admin/configure/advanced_parameters/import
```
GET,POST      /                                         admin_import  [ImportController::importAction]
POST          /data                                     admin_import_data_configuration_index  [ImportDataConfigurationController::indexAction]
GET           /data                                     admin_import_data_configuration_index_redirect  []
POST          /process                                  admin_import_process  [ImportController::processImportAction]
POST          /file/upload                              admin_import_file_upload  [ImportController::uploadAction]
GET           /file/delete                              admin_import_file_delete  [ImportController::deleteAction]
GET           /file/download                            admin_import_file_download  [ImportController::downloadAction]
GET           /sample/download/{sampleName}             admin_import_sample_download  [ImportController::downloadSampleAction]
POST          /match                                    admin_import_data_configuration_create  [ImportDataConfigurationController::createAction]
GET           /match                                    admin_import_data_configuration_get  [ImportDataConfigurationController::getAction]
DELETE        /match                                    admin_import_data_configuration_delete  [ImportDataConfigurationController::deleteAction]
GET           /fields                                   admin_import_get_available_fields  [ImportController::getAvailableEntityFieldsAction]
```

### admin/configure/advanced_parameters/logs
```
GET           /                                         admin_logs_index  [LogsController::indexAction]
POST          /settings                                 admin_logs_save_settings  [LogsController::saveSettingsAction]
POST          /database-settings                        admin_logs_save_database_settings  [LogsController::saveDatabaseSettingsAction]
POST          /delete-all                               admin_logs_delete_all  [LogsController::deleteAllAction]
POST          /                                         admin_logs_search  [LogsController::searchAction]
```

### admin/configure/advanced_parameters/performance
```
GET           /                                         admin_performance  [PerformanceController::indexAction]
POST          /smarty                                   admin_performance_smarty_save  [PerformanceController::processSmartyFormAction]
POST          /debug-mode                               admin_performance_debug_mode_save  [PerformanceController::processDebugModeFormAction]
POST          /optional-features                        admin_performance_optional_features_save  [PerformanceController::processOptionalFeaturesFormAction]
POST          /combine-compress-cache                   admin_performance_combine_compress_cache_save  [PerformanceController::processCombineCompressCacheFormAction]
POST          /media-servers                            admin_performance_media_servers_save  [PerformanceController::processMediaServersFormAction]
POST          /caching                                  admin_performance_caching_save  [PerformanceController::processCachingFormAction]
GET           /disable-non-builtin                      admin_performance_module_disable_non_builtin  [PerformanceController::disableNonBuiltInAction]
GET           /clear-cache                              admin_clear_cache  [PerformanceController::clearCacheAction]
GET           /memcache/servers                         admin_servers  [MemcacheServerController::listAction]
POST          /memcache/servers                         admin_servers_add  [MemcacheServerController::addAction]
DELETE        /memcache/servers                         admin_servers_delete  [MemcacheServerController::deleteAction]
GET           /memcache/servers/test                    admin_servers_test  [MemcacheServerController::testAction]
```

### admin/configure/advanced_parameters/permission
```
GET           /                                         admin_permissions_index  [PermissionController::indexAction]
POST          /update/permissions/tab                   admin_permissions_update_tab_permissions  [PermissionController::updateTabPermissionsAction]
POST          /update/permissions/module                admin_permissions_update_module_permissions  [PermissionController::updateModulePermissionsAction]
```

### admin/configure/advanced_parameters/profiles
```
GET           /                                         admin_profiles_index  [ProfileController::indexAction]
POST          /                                         admin_profiles_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_profiles_create  [ProfileController::createAction]
GET,POST      /{profileId}/edit                         admin_profiles_edit  [ProfileController::editAction]
POST          /{profileId}/delete                       admin_profiles_delete  [ProfileController::deleteAction]
POST          /delete/bulk                              admin_profiles_bulk_delete  [ProfileController::bulkDeleteAction]
```

### admin/configure/advanced_parameters/security
```
GET           /                                         admin_security  [SecurityController::indexAction]
POST          /general                                  admin_security_general_save  [SecurityController::processGeneralFormAction]
POST          /password-policy                          admin_security_password_policy_save  [SecurityController::processPasswordPolicyFormAction]
GET           /session/customer/clear                   admin_security_sessions_customer_clear  [SecurityController::clearCustomerSessionAction]
GET           /session/customer                         admin_security_sessions_customer_list  [SecurityController::customerSessionAction]
DELETE        /session/customer/{sessionId}             admin_security_sessions_customer_delete  [SecurityController::deleteCustomerSessionAction]
GET           /session/employee/clear                   admin_security_sessions_employee_clear  [SecurityController::clearEmployeeSessionAction]
GET           /session/employee                         admin_security_sessions_employee_list  [SecurityController::employeeSessionAction]
DELETE        /session/employee/{sessionId}             admin_security_sessions_employee_delete  [SecurityController::deleteEmployeeSessionAction]
POST,DELETE   /session/employee/delete/bulk             admin_security_sessions_employee_bulk_delete  [SecurityController::bulkDeleteEmployeeSessionAction]
POST,DELETE   /session/customer/delete/bulk             admin_security_sessions_customer_bulk_delete  [SecurityController::bulkDeleteCustomerSessionAction]
POST          /session/customer                         admin_security_sessions_customer_search  [CommonController::searchGridAction]
POST          /session/employee                         admin_security_sessions_employee_search  [CommonController::searchGridAction]
```

### admin/configure/advanced_parameters/shops
```
GET           /search/{searchTerm}                      admin_shops_search  [ShopController::searchAction]
```

### admin/configure/advanced_parameters/sql_request
```
GET           /                                         admin_sql_requests_index  [SqlManagerController::indexAction]
POST          /                                         admin_sql_requests_search  [CommonController::searchGridAction]
POST          /process-settings                         admin_sql_requests_process_settings  [SqlManagerController::processFormAction]
GET,POST      /new                                      admin_sql_requests_create  [SqlManagerController::createAction]
GET,POST      /{sqlRequestId}/edit                      admin_sql_requests_edit  [SqlManagerController::editAction]
GET,DELETE    /{sqlRequestId}/delete                    admin_sql_requests_delete  [SqlManagerController::deleteAction]
POST          /delete-bulk                              admin_sql_requests_delete_bulk  [SqlManagerController::deleteBulkAction]
GET           /tables/{mySqlTableName}/columns          admin_sql_requests_table_columns  [SqlManagerController::ajaxTableColumnsAction]
GET           /{sqlRequestId}/view                      admin_sql_requests_view  [SqlManagerController::viewAction]
GET           /{sqlRequestId}/export                    admin_sql_requests_export  [SqlManagerController::exportAction]
```

### admin/configure/advanced_parameters/system_information
```
GET           /                                         admin_system_information  [SystemInformationController::indexAction]
POST          /files                                    admin_system_information_check_files  [SystemInformationController::displayCheckFilesAction]
```

### admin/configure/advanced_parameters/webservice
```
GET           /                                         admin_webservice_keys_index  [WebserviceController::indexAction]
POST          /                                         admin_webservice_keys_search  [CommonController::searchGridAction]
PATCH,POST    /settings                                 admin_webservice_save_settings  [WebserviceController::saveSettingsAction]
GET,POST      /new                                      admin_webservice_keys_create  [WebserviceController::createAction]
GET,POST      /{webserviceKeyId}/edit                   admin_webservice_keys_edit  [WebserviceController::editAction]
DELETE,POST   /{webserviceKeyId}/delete                 admin_webservice_keys_delete  [WebserviceController::deleteAction]
POST          /bulk-delete                              admin_webservice_keys_bulk_delete  [WebserviceController::bulkDeleteAction]
POST          /{webserviceKeyId}/toggle-status          admin_webservice_keys_toggle_status  [WebserviceController::toggleStatusAction]
POST          /bulk-enable                              admin_webservice_keys_bulk_enable  [WebserviceController::bulkEnableAction]
POST          /bulk-disable                             admin_webservice_keys_bulk_disable  [WebserviceController::bulkDisableAction]
```

### admin/configure/shop_parameters/contacts
```
GET           /                                         admin_contacts_index  [ContactsController::indexAction]
POST          /                                         admin_contacts_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_contacts_create  [ContactsController::createAction]
GET,POST      /{contactId}/edit                         admin_contacts_edit  [ContactsController::editAction]
POST          /{contactId}/delete                       admin_contacts_delete  [ContactsController::deleteAction]
POST          /delete/bulk                              admin_contacts_delete_bulk  [ContactsController::deleteBulkAction]
```

### admin/configure/shop_parameters/customer_groups
```
GET           /                                         admin_customer_groups_index  [CustomerGroupsController::indexAction]
POST          /                                         admin_customer_groups_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_customer_groups_create  [CustomerGroupsController::createAction]
GET,POST      /{groupId}/edit                           admin_customer_groups_edit  [CustomerGroupsController::editAction]
```

### admin/configure/shop_parameters/customer_preferences
```
GET           /                                         admin_customer_preferences  [CustomerPreferencesController::indexAction]
PATCH,POST    /                                         admin_customer_preferences_process  [CustomerPreferencesController::processAction]
```

### admin/configure/shop_parameters/maintenance
```
GET           /                                         admin_maintenance  [MaintenanceController::indexAction]
PATCH,POST    /                                         admin_maintenance_save  [MaintenanceController::processFormAction]
```

### admin/configure/shop_parameters/meta
```
GET           /                                         admin_metas_index  [MetaController::indexAction]
POST          /                                         admin_metas_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_metas_create  [MetaController::createAction]
GET,POST      /{metaId}/edit                            admin_metas_edit  [MetaController::editAction]
DELETE        /{metaId}/delete                          admin_metas_delete  [MetaController::deleteAction]
POST          /delete                                   admin_metas_delete_bulk  [MetaController::deleteBulkAction]
PATCH,POST    /set-up-urls                              admin_metas_set_up_urls_save  [MetaController::processSetUpUrlsFormAction]
POST          /shop-urls                                admin_metas_shop_urls_save  [MetaController::processShopUrlsFormAction]
PATCH,POST    /url-schema                               admin_metas_url_schema_save  [MetaController::processUrlSchemaFormAction]
PATCH,POST    /seo-options                              admin_metas_seo_options_save  [MetaController::processSeoOptionsFormAction]
POST          /generate/robots                          admin_metas_generate_robots_text_file  [MetaController::generateRobotsFileAction]
```

### admin/configure/shop_parameters/order_preferences
```
GET           /                                         admin_order_preferences  [OrderPreferencesController::indexAction]
PATCH,POST    /general                                  admin_order_preferences_general_save  [OrderPreferencesController::processGeneralFormAction]
PATCH,POST    /gift-options                             admin_order_preferences_gift_options_save  [OrderPreferencesController::processGiftOptionsFormAction]
```

### admin/configure/shop_parameters/order_return_states
```
GET,POST      /new                                      admin_order_return_states_create  [OrderStateController::createOrderReturnStateAction]
GET,POST      /{orderReturnStateId}/edit                admin_order_return_states_edit  [OrderStateController::editOrderReturnStateAction]
POST,DELETE   /{orderReturnStateId}/delete              admin_order_return_states_delete  [OrderStateController::deleteOrderReturnStateAction]
POST          /delete-bulk                              admin_order_return_states_delete_bulk  [OrderStateController::deleteOrderReturnStateBulkAction]
```

### admin/configure/shop_parameters/order_states
```
GET           /                                         admin_order_states  [OrderStateController::indexAction]
POST          /                                         admin_order_states_filter  [OrderStateController::searchGridAction]
GET,POST      /new                                      admin_order_states_create  [OrderStateController::createAction]
GET,POST      /{orderStateId}/edit                      admin_order_states_edit  [OrderStateController::editAction]
POST          /{orderStateId}/toggle-delivery           admin_order_states_toggle_delivery  [OrderStateController::toggleDeliveryAction]
POST          /{orderStateId}/toggle-invoice            admin_order_states_toggle_invoice  [OrderStateController::toggleInvoiceAction]
POST          /{orderStateId}/toggle-send-email         admin_order_states_toggle_send_email  [OrderStateController::toggleSendEmailAction]
POST,DELETE   /{orderStateId}/delete                    admin_order_states_delete  [OrderStateController::deleteAction]
POST          /delete-bulk                              admin_order_states_delete_bulk  [OrderStateController::deleteBulkAction]
```

### admin/configure/shop_parameters/preferences
```
GET           preferences                               admin_preferences  [PreferencesController::indexAction]
POST          preferences                               admin_preferences_save  [PreferencesController::processFormAction]
```

### admin/configure/shop_parameters/product_preferences
```
GET           /                                         admin_product_preferences  [ProductPreferencesController::indexAction]
POST          /general                                  admin_product_preferences_general_save  [ProductPreferencesController::processGeneralFormAction]
POST          /pagination                               admin_product_preferences_pagination_save  [ProductPreferencesController::processPaginationFormAction]
POST          /page                                     admin_product_preferences_page_save  [ProductPreferencesController::processPageFormAction]
POST          /stock                                    admin_product_preferences_stock_save  [ProductPreferencesController::processStockFormAction]
```

### admin/configure/shop_parameters/search_engines
```
GET           /                                         admin_search_engines_index  [SearchEnginesController::indexAction]
POST          /                                         admin_search_engines_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_search_engines_create  [SearchEnginesController::createAction]
GET,POST      /{searchEngineId}/edit                    admin_search_engines_edit  [SearchEnginesController::editAction]
POST          /{searchEngineId}/delete                  admin_search_engines_delete  [SearchEnginesController::deleteAction]
POST          /bulk-delete                              admin_search_engines_bulk_delete  [SearchEnginesController::bulkDeleteAction]
```

### admin/configure/shop_parameters/search_preferences
```
GET           /                                         admin_search_alias_index  [SearchAliasController::indexAction]
POST          /                                         admin_search_alias_search  [CommonController::searchGridAction]
POST,DELETE   /{searchTerm}/delete                      admin_search_alias_delete  [SearchAliasController::deleteAction]
POST,DELETE   /bulk-delete                              admin_search_alias_bulk_delete  [SearchAliasController::bulkDeleteAction]
GET,POST      /new                                      admin_search_alias_create  [SearchAliasController::createAction]
GET,POST      /{searchTerm}/edit                        admin_search_alias_edit  [SearchAliasController::editAction]
```

### admin/configure/shop_parameters/stores
```
GET           /                                         admin_stores_index  [StoreController::indexAction]
POST          /                                         admin_stores_search  [CommonController::searchGridAction]
POST          /{storeId}/toggle-status                  admin_stores_toggle_status  [StoreController::toggleStatusAction]
POST,DELETE   /{storeId}/delete                         admin_stores_delete  [StoreController::deleteAction]
POST,DELETE   /bulk-delete                              admin_stores_bulk_delete  [StoreController::bulkDeleteAction]
POST          /bulk-enable                              admin_stores_bulk_enable  [StoreController::bulkEnableAction]
POST          /bulk-disable                             admin_stores_bulk_disable  [StoreController::bulkDisableAction]
```

### admin/configure/shop_parameters/tags
```
GET           /                                         admin_tags_index  [TagController::indexAction]
POST          /                                         admin_tags_search  [CommonController::searchGridAction]
GET,POST,PATCH  /tags/create                              admin_tags_create  [TagController::createAction]
GET,POST,PATCH  /tags/{tagId}/edit                        admin_tags_edit  [TagController::editAction]
POST,DELETE   /{tagId}/delete                           admin_tag_delete  [TagController::deleteAction]
POST          /bulk-delete                              admin_tag_bulk_delete  [TagController::bulkDeleteAction]
```

### admin/configure/shop_parameters/titles
```
GET           /                                         admin_title_index  [TitleController::indexAction]
POST          /                                         admin_title_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_title_create  [TitleController::createAction]
GET,POST      /{titleId}/edit                           admin_title_edit  [TitleController::editAction]
POST,DELETE   /{titleId}/delete                         admin_title_delete  [TitleController::deleteAction]
POST          /bulk-delete                              admin_title_bulk_delete  [TitleController::bulkDeleteAction]
```

### admin/improve/design/cms_pages
```
GET           /                                         admin_cms_pages_index  [CmsPageController::indexAction]
POST          /search                                   admin_cms_pages_search  [CmsPageController::searchAction]
GET,POST      /new                                      admin_cms_pages_create  [CmsPageController::createAction]
GET,POST      /{cmsPageId}/edit                         admin_cms_pages_edit  [CmsPageController::editAction]
GET           /{cmsPageId}/preview                      admin_cms_pages_preview  [CmsPageController::previewAction]
POST          /{cmsId}/toggle-status                    admin_cms_pages_toggle  [CmsPageController::toggleCmsAction]
DELETE        /{cmsId}/delete                           admin_cms_pages_delete  [CmsPageController::deleteCmsAction]
POST          /bulk-enable-status                       admin_cms_pages_bulk_enable_status  [CmsPageController::bulkEnableCmsPageStatusAction]
POST          /bulk-disable-status                      admin_cms_pages_bulk_disable_status  [CmsPageController::bulkDisableCmsPageStatusAction]
POST          /bulk-delete                              admin_cms_pages_bulk_delete  [CmsPageController::bulkDeleteCmsPageAction]
POST          /category/search                          admin_cms_pages_search_cms_category  [CommonController::searchGridAction]
GET,POST      /category/new                             admin_cms_pages_category_create  [CmsPageController::createCmsCategoryAction]
GET,POST      /category/{cmsCategoryId}/edit            admin_cms_pages_category_edit  [CmsPageController::editCmsCategoryAction]
DELETE        /category/{cmsCategoryId}/delete          admin_cms_pages_category_delete  [CmsPageController::deleteCmsCategoryAction]
POST          /category/delete-bulk                     admin_cms_pages_category_delete_bulk  [CmsPageController::deleteBulkCmsCategoryAction]
POST          /category/{cmsCategoryId}/toggle-status   admin_cms_pages_category_toggle  [CmsPageController::toggleCmsCategoryAction]
POST          /category/bulk-enable-status              admin_cms_pages_category_bulk_status_enable  [CmsPageController::bulkCmsPageCategoryStatusEnableAction]
POST          /category/bulk-disable-status             admin_cms_pages_category_bulk_status_disable  [CmsPageController::bulkCmsPageCategoryStatusDisableAction]
POST          /category/update-position                 admin_cms_pages_category_update_position  [CmsPageController::updateCmsCategoryPositionAction]
POST          /update-position                          admin_cms_pages_update_position  [CmsPageController::updateCmsPositionAction]
```

### admin/improve/design/image_settings
```
GET           /                                         admin_image_settings_index  [ImageSettingsController::indexAction]
POST          /                                         admin_image_settings_filter  [CommonController::searchGridAction]
POST          /save-settings                            admin_image_settings_save_settings  [ImageSettingsController::saveSettingsAction]
POST          /regenerate-thumbnails                    admin_image_settings_regenerate_thumbnails  [ImageSettingsController::regenerateThumbnailsAction]
GET,POST      /new                                      admin_image_settings_create  [ImageSettingsController::createAction]
GET,POST      /{imageTypeId}/edit                       admin_image_settings_edit  [ImageSettingsController::editAction]
POST,DELETE   /{imageTypeId}/delete                     admin_image_settings_delete  [ImageSettingsController::deleteAction]
POST,DELETE   /bulk-delete                              admin_image_settings_bulk_delete  [ImageSettingsController::bulkDeleteAction]
```

### admin/improve/design/mail_theme
```
GET           /                                         admin_mail_theme_index  [MailThemeController::indexAction]
POST          /generate                                 admin_mail_theme_generate  [MailThemeController::generateMailsAction]
POST          /save-configuration                       admin_mail_theme_save_configuration  [MailThemeController::saveConfigurationAction]
GET           /preview/{theme}                          admin_mail_theme_preview  [MailThemeController::previewThemeAction]
GET           /preview/{locale}/{theme}/{layout}.{type}  admin_mail_theme_preview_layout  [MailThemeController::previewLayoutAction]
GET           /preview/{locale}/{theme}/{module}/{layout}.{type}  admin_mail_theme_preview_module_layout  [MailThemeController::previewLayoutAction]
GET           /raw/{locale}/{theme}/{layout}.{type}     admin_mail_theme_raw_layout  [MailThemeController::rawLayoutAction]
GET           /raw/{locale}/{theme}/{module}/{layout}.{type}  admin_mail_theme_raw_module_layout  [MailThemeController::rawLayoutAction]
GET           /send-test-mail/{locale}/{theme}/{layout}  admin_mail_theme_send_test_mail  [MailThemeController::sendTestMailAction]
GET           /send-test-mail/{locale}/{theme}/{module}/{layout}  admin_mail_theme_send_test_module_mail  [MailThemeController::sendTestMailAction]
POST          /translate-body                           admin_mail_theme_translate_body  [MailThemeController::translateBodyAction]
```

### admin/improve/design/positions
```
GET           /                                         admin_modules_positions  [PositionsController::indexAction]
POST,GET      /unhook                                   admin_modules_positions_unhook  [PositionsController::unhookAction]
POST          /toggle-status                            admin_modules_positions_toggle_status  [PositionsController::toggleStatusAction]
```

### admin/improve/design/theme
```
GET           /                                         admin_themes_index  [ThemeController::indexAction]
POST          /upload-logos                             admin_themes_upload_logos  [ThemeController::uploadLogosAction]
GET           /export                                   admin_themes_export_current  [ThemeController::exportAction]
GET,POST      /import                                   admin_themes_import  [ThemeController::importAction]
POST          /{themeName}/enable                       admin_themes_enable  [ThemeController::enableAction]
POST          /{themeName}/delete                       admin_themes_delete  [ThemeController::deleteAction]
POST          /adapt-to-rtl-languages                   admin_themes_adapt_to_rtl_languages  [ThemeController::adaptToRTLLanguagesAction]
GET,POST      /customize-layouts                        admin_theme_customize_layouts  [ThemeController::customizeLayoutsAction]
POST          /{themeName}/reset-layouts                admin_themes_reset_layouts  [ThemeController::resetLayoutsAction]
```

### admin/improve/international/country
```
GET           /                                         admin_countries_index  [CountryController::indexAction]
POST          /                                         admin_countries_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_countries_create  [CountryController::createAction]
GET,POST      /{countryId}/edit                         admin_countries_edit  [CountryController::editAction]
POST,DELETE   /{countryId}/delete                       admin_countries_delete  [CountryController::deleteAction]
```

### admin/improve/international/currencies
```
GET           /                                         admin_currencies_index  [CurrencyController::indexAction]
POST          /                                         admin_currencies_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_currencies_create  [CurrencyController::createAction]
GET,POST      /{currencyId}/edit                        admin_currencies_edit  [CurrencyController::editAction]
DELETE        /{currencyId}/delete                      admin_currencies_delete  [CurrencyController::deleteAction]
POST          /{currencyId}/toggle-status               admin_currencies_toggle_status  [CurrencyController::toggleStatusAction]
POST          /update-live-exchange-rates               admin_currencies_update_live_exchange_rates  [CurrencyController::updateLiveExchangeRatesAction]
POST          /refresh-exchange-rates                   admin_currencies_refresh_exchange_rates  [CurrencyController::refreshExchangeRatesAction]
GET           /reference-data/{currencyIsoCode}         admin_currencies_get_reference_data  [CurrencyController::getReferenceDataAction]
POST,DELETE   /bulk-delete                              admin_currencies_bulk_delete  [CurrencyController::bulkDeleteAction]
POST          /bulk-toggle-status/{status}              admin_currencies_bulk_toggle_status  [CurrencyController::bulkToggleStatusAction]
```

### admin/improve/international/geolocation
```
GET           /                                         admin_geolocation_index  [GeolocationController::indexAction]
POST          /by-ip-address                            admin_geolocation_by_ip_address_save  [GeolocationController::processByIpAddressFormAction]
POST          /whitelist                                admin_geolocation_whitelist_save  [GeolocationController::processWhitelistFormAction]
POST          /options                                  admin_geolocation_options_save  [GeolocationController::processOptionsFormAction]
```

### admin/improve/international/language
```
GET           /                                         admin_languages_index  [LanguageController::indexAction]
POST          /                                         admin_languages_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_languages_create  [LanguageController::createAction]
GET,POST      /{languageId}/edit                        admin_languages_edit  [LanguageController::editAction]
POST,DELETE   /{languageId}/delete                      admin_languages_delete  [LanguageController::deleteAction]
POST          /bulk-delete                              admin_languages_bulk_delete  [LanguageController::bulkDeleteAction]
POST          /{languageId}/toggle-status               admin_languages_toggle_status  [LanguageController::toggleStatusAction]
POST          /bulk-toggle-status/{status}              admin_languages_bulk_toggle_status  [LanguageController::bulkToggleStatusAction]
```

### admin/improve/international/localization
```
GET           /                                         admin_localization_index  [LocalizationController::indexAction]
POST          /configuration                            admin_localization_configuration_save  [LocalizationController::processConfigurationFormAction]
POST          /local-units                              admin_localization_local_units_save  [LocalizationController::processLocalUnitsFormAction]
POST          /advanced                                 admin_localization_advanced_save  [LocalizationController::processAdvancedFormAction]
POST          /import-pack                              admin_localization_import_pack  [LocalizationController::importPackAction]
```

### admin/improve/international/state
```
GET           /country-states                           admin_country_states  [StateController::getStatesAction]
GET           /country-states-options                   admin_country_states_options  [StateController::getLegacyStatesOptionsAction]
GET           /                                         admin_states_index  [StateController::indexAction]
POST          /                                         admin_states_filter  [CommonController::searchGridAction]
GET,POST      /new                                      admin_states_create  [StateController::createAction]
GET,POST      /{stateId}/edit                           admin_states_edit  [StateController::editAction]
POST          /delete-bulk                              admin_states_delete_bulk  [StateController::deleteBulkAction]
DELETE        /{stateId}/delete                         admin_states_delete  [StateController::deleteAction]
POST          /bulk-status-enable                       admin_states_bulk_enable_status  [StateController::bulkEnableAction]
POST          /bulk-status-disable                      admin_states_bulk_disable_status  [StateController::bulkDisableAction]
POST          /bulk-update-zone                         admin_states_bulk_update_zone  [StateController::bulkUpdateZoneAction]
POST          /{stateId}/toggle-status                  admin_states_toggle_status  [StateController::toggleStatusAction]
```

### admin/improve/international/tax
```
GET           /                                         admin_taxes_index  [TaxController::indexAction]
PATCH,POST    /save-options                             admin_taxes_save_options  [TaxController::saveOptionsAction]
POST          /                                         admin_taxes_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_taxes_create  [TaxController::createAction]
GET,POST      /{taxId}/edit                             admin_taxes_edit  [TaxController::editAction]
POST          /{taxId}/delete                           admin_taxes_delete  [TaxController::deleteAction]
POST          /{taxId}/toggle-status                    admin_taxes_toggle_status  [TaxController::toggleStatusAction]
POST          /bulk-enable-status                       admin_taxes_bulk_enable_status  [TaxController::bulkEnableStatusAction]
POST          /bulk-disable-status                      admin_taxes_bulk_disable_status  [TaxController::bulkDisableStatusAction]
POST          /bulk-delete                              admin_taxes_bulk_delete  [TaxController::bulkDeleteAction]
```

### admin/improve/international/tax_rules_groups
```
GET           /                                         admin_tax_rules_groups_index  [TaxRulesGroupController::indexAction]
POST          /                                         admin_tax_rules_groups_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_tax_rules_groups_create  [TaxRulesGroupController::createAction]
GET,POST      /{taxRulesGroupId}/edit                   admin_tax_rules_groups_edit  [TaxRulesGroupController::editAction]
POST,DELETE   /{taxRulesGroupId}/delete                 admin_tax_rules_groups_delete  [TaxRulesGroupController::deleteAction]
POST          /{taxRulesGroupId}/toggle-status          admin_tax_rules_groups_toggle_status  [TaxRulesGroupController::toggleStatusAction]
POST          /bulk-enable-status                       admin_tax_rules_groups_bulk_enable_status  [TaxRulesGroupController::bulkEnableStatusAction]
POST          /bulk-disable-status                      admin_tax_rules_groups_bulk_disable_status  [TaxRulesGroupController::bulkDisableStatusAction]
POST          /bulk-delete                              admin_tax_rules_groups_bulk_delete  [TaxRulesGroupController::bulkDeleteAction]
```

### admin/improve/international/translations
```
GET           /                                         admin_international_translation_overview  [TranslationsController::overviewAction]
POST          /export                                   admin_international_translations_export_catalogues  [TranslationsController::exportCataloguesAction]
GET           /settings                                 admin_international_translations_show_settings  [TranslationsController::showSettingsAction]
POST          /modify                                   admin_international_translations_modify  [TranslationsController::modifyTranslationsAction]
POST          /add-update-language                      admin_international_translations_add_update_language  [TranslationsController::addUpdateLanguageAction]
POST          /copy                                     admin_international_translations_copy_language  [TranslationsController::copyLanguageAction]
```

### admin/improve/international/zone
```
GET           /                                         admin_zones_index  [ZoneController::indexAction]
POST          /                                         admin_zones_search  [ZoneController::searchAction]
GET,POST      /new                                      admin_zones_create  [ZoneController::createAction]
GET,POST      /{zoneId}/edit                            admin_zones_edit  [ZoneController::editAction]
POST,DELETE   /{zoneId}/delete                          admin_zones_delete  [ZoneController::deleteAction]
POST          /{zoneId}/toggle-status                   admin_zones_toggle_status  [ZoneController::toggleStatusAction]
POST,DELETE   /bulk-delete                              admin_zones_bulk_delete  [ZoneController::bulkDeleteAction]
POST          /bulk-toggle-status/{status}              admin_zones_bulk_toggle_status  [ZoneController::bulkToggleStatus]
```

### admin/improve/payment/payment_methods
```
GET           /payment_methods                          admin_payment_methods  [PaymentMethodsController::indexAction]
```

### admin/improve/payment/preferences
```
GET           /preferences                              admin_payment_preferences  [PaymentPreferencesController::indexAction]
POST          /preferences                              admin_payment_preferences_process  [PaymentPreferencesController::processFormAction]
```

### admin/improve/shipping/carriers
```
GET           /                                         admin_carriers_index  [CarrierController::indexAction]
POST          /                                         admin_carriers_search  [CarrierController::searchAction]
GET,POST      /create                                   admin_carriers_create  [CarrierController::createAction]
GET,POST      /{carrierId}/edit                         admin_carriers_edit  [CarrierController::editAction]
POST,DELETE   /{carrierId}/delete                       admin_carriers_delete  [CarrierController::deleteAction]
POST          /{carrierId}/toggle-status                admin_carriers_toggle_status  [CarrierController::toggleStatusAction]
POST          /{carrierId}/toggle-is-free               admin_carriers_toggle_is_free  [CarrierController::toggleIsFreeAction]
POST          /update-position                          admin_carriers_update_position  [CarrierController::updatePositionAction]
POST          /bulk-enable                              admin_carriers_bulk_enable_status  [CarrierController::bulkEnableStatusAction]
POST          /bulk-disable                             admin_carriers_bulk_disable_status  [CarrierController::bulkDisableStatusAction]
POST,DELETE   /bulk-delete                              admin_carriers_bulk_delete  [CarrierController::bulkDeleteAction]
```

### admin/improve/shipping/preferences
```
GET           /                                         admin_shipping_preferences  [PreferencesController::indexAction]
PATCH,POST    /handling                                 admin_shipping_preferences_handling_save  [PreferencesController::processHandlingFormAction]
PATCH,POST    /carrier-options                          admin_shipping_preferences_carrier_options_save  [PreferencesController::processCarrierOptionsFormAction]
```

### admin/sell/business_entity/business_entities
```
GET           /                                         admin_business_entities_list  [BusinessEntitiesController::listAction]
```

### admin/sell/business_entity/customer_b2b
```
GET           /                                         admin_customer_b2b_list  [CustomerB2bController::listAction]
```

### admin/sell/catalog/attachment
```
GET           /                                         admin_attachments_index  [AttachmentController::indexAction]
POST          /                                         admin_attachments_filter  [CommonController::searchGridAction]
GET,POST      /new                                      admin_attachments_create  [AttachmentController::createAction]
GET,POST      /{attachmentId}/edit                      admin_attachments_edit  [AttachmentController::editAction]
GET,POST      /{attachmentId}/view                      admin_attachments_view  [AttachmentController::viewAction]
POST          /delete-bulk                              admin_attachments_delete_bulk  [AttachmentController::deleteBulkAction]
POST          /{attachmentId}/delete                    admin_attachments_delete  [AttachmentController::deleteAction]
GET           /{attachmentId}/info                      admin_attachments_attachment_info  [AttachmentController::getAttachmentInfoAction]
GET           /search/{searchPhrase}                    admin_attachments_search  [AttachmentController::searchAction]
```

### admin/sell/catalog/attributes
```
GET           /{attributeGroupId}/attributes/           admin_attributes_index  [AttributeController::indexAction]
POST          /{attributeGroupId}/attributes/           admin_attributes_search  [CommonController::searchGridAction]
GET,POST      /{attributeGroupId}/attributes/{attributeId}/edit  admin_attributes_edit  [AttributeController::editAction]
POST,DELETE   /{attributeGroupId}/attributes/{attributeId}/delete  admin_attributes_delete  [AttributeController::deleteAction]
POST          /{attributeGroupId}/attributes/delete     admin_attributes_bulk_delete  [AttributeController::bulkDeleteAction]
POST          /{attributeGroupId}/attributes/update-position  admin_attributes_update_position  [AttributeController::updatePositionAction]
GET           /{attributeGroupId}/attributes/export     admin_attribute_export  [AttributeController::exportAction]
GET,POST      /new-value                                admin_attributes_create  [AttributeController::createAction]
GET           /                                         admin_attribute_groups_index  [AttributeGroupController::indexAction]
POST          /                                         admin_attribute_groups_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_attribute_groups_create  [AttributeGroupController::createAction]
GET           /{attributeGroupId}/view                  admin_attribute_groups_view  [AttributeController::indexAction]
GET,POST      /{attributeGroupId}/edit                  admin_attribute_groups_edit  [AttributeGroupController::editAction]
POST,DELETE   /{attributeGroupId}/delete                admin_attribute_groups_delete  [AttributeGroupController::deleteAction]
POST          /bulk-delete                              admin_attribute_groups_bulk_delete  [AttributeGroupController::bulkDeleteAction]
GET           /export                                   admin_attribute_groups_export  [AttributeGroupController::exportAction]
POST          /update-position                          admin_attribute_groups_update_position  [AttributeGroupController::updatePositionAction]
POST          /{attributeGroupId}/attributes/{attributeId}/delete-texture-image  admin_attributes_delete_texture_image  [AttributeGroupController::deleteTextureImageAction]
```

### admin/sell/catalog/cart_rule
```
GET           /search                                   admin_cart_rules_search  [CartRuleController::searchAction]
```

### admin/sell/catalog/catalog_price_rule
```
GET           /                                         admin_catalog_price_rules_index  [CatalogPriceRuleController::indexAction]
POST          /                                         admin_catalog_price_rules_search  [CatalogPriceRuleController::searchAction]
GET,POST      /new                                      admin_catalog_price_rules_create  [CatalogPriceRuleController::createAction]
GET,POST      /{catalogPriceRuleId}/edit                admin_catalog_price_rules_edit  [CatalogPriceRuleController::editAction]
POST          /{catalogPriceRuleId}/delete              admin_catalog_price_rules_delete  [CatalogPriceRuleController::deleteAction]
POST          /bulk-delete                              admin_catalog_price_rules_bulk_delete  [CatalogPriceRuleController::bulkDeleteAction]
GET           /list-for-product/{productId}             admin_catalog_price_rules_list_for_product  [CatalogPriceRuleController::listForProductAction]
```

### admin/sell/catalog/categories
```
GET,POST      /{categoryId}                             admin_categories_index  [CategoryController::indexAction]
POST          /search                                   admin_categories_search  [CommonController::searchGridAction]
POST          /bulk-status-enable                       admin_categories_bulk_enable_status  [CategoryController::bulkEnableStatusAction]
POST          /bulk-status-disable                      admin_categories_bulk_disable_status  [CategoryController::bulkDisableStatusAction]
POST          /{categoryId}/toggle-status               admin_categories_toggle_status  [CategoryController::toggleStatusAction]
POST          /bulk-delete                              admin_categories_bulk_delete  [CategoryController::bulkDeleteAction]
POST          /delete                                   admin_categories_delete  [CategoryController::deleteAction]
GET           /export/{categoryId}                      admin_categories_export  [CategoryController::exportAction]
GET,POST      /new                                      admin_categories_create  [CategoryController::createAction]
GET,POST      /new-root                                 admin_categories_create_root  [CategoryController::createRootAction]
GET,POST      /{categoryId}/edit                        admin_categories_edit  [CategoryController::editAction]
GET,POST      /{categoryId}/edit-root                   admin_categories_edit_root  [CategoryController::editRootAction]
POST          /{categoryId}/delete-cover-image          admin_categories_delete_cover_image  [CategoryController::deleteCoverImageAction]
POST          /{categoryId}/delete-thumbnail-image      admin_categories_delete_thumbnail_image  [CategoryController::deleteThumbnailImageAction]
POST          /update-positions                         admin_categories_update_position  [CategoryController::updatePositionAction]
GET           /tree                                     admin_categories_get_categories_tree  [CategoryController::getCategoriesTreeAction]
GET           /list/{limit}                             admin_categories_get_ajax_categories  [CategoryController::getAjaxCategoriesAction]
```

### admin/sell/catalog/discount
```
GET           /                                         admin_discounts_index  [DiscountController::indexAction]
POST          /                                         admin_discounts_search_grid  [CommonController::searchGridAction]
POST          /reset_discount_search                    admin_discounts_reset_grid  [DiscountController::resetSearchAction]
GET,POST      /new/{discountType}                       admin_discounts_create  [DiscountController::createAction]
GET,POST      /{discountId}/edit                        admin_discount_edit  [DiscountController::editAction]
POST          /{discountId}/toggle-status               admin_discount_toggle_status  [DiscountController::toggleStatusAction]
POST          /bulk-status-enable                       admin_discount_bulk_enable_status  [DiscountController::bulkEnableStatusAction]
POST          /bulk-status-disable                      admin_discount_bulk_disable_status  [DiscountController::bulkDisableStatusAction]
POST          /bulk-delete                              admin_discount_bulk_delete  [DiscountController::bulkDeleteAction]
POST          /{discountId}/duplicate                   admin_discounts_duplicate  [DiscountController::duplicateAction]
POST,DELETE   /{discountId}/delete                      admin_discounts_delete  [DiscountController::deleteAction]
```

### admin/sell/catalog/features
```
GET           /                                         admin_features_index  [FeatureController::indexAction]
POST          /                                         admin_features_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_features_add  [FeatureController::createAction]
GET,POST      /{featureId}/edit                         admin_features_edit  [FeatureController::editAction]
GET           /export                                   admin_features_export  [FeatureController::exportAction]
POST          /update-position                          admin_features_update_position  [CommonController::updatePositionAction]
POST,DELETE   /{featureId}/delete                       admin_features_delete  [FeatureController::deleteAction]
POST,DELETE   /bulk-delete                              admin_features_bulk_delete  [FeatureController::bulkDeleteAction]
GET           /{featureId}/values                       admin_feature_values_index  [FeatureValueController::indexAction]
POST          /{featureId}/values                       admin_feature_values_search  [CommonController::searchGridAction]
GET,POST      /new-value                                admin_feature_values_add  [FeatureValueController::createAction]
GET,POST      /{featureId}/values/{featureValueId}/edit  admin_feature_values_edit  [FeatureValueController::editAction]
GET           /{featureId}/values/export                admin_feature_values_export  [FeatureValueController::exportAction]
POST,DELETE   /{featureId}/values/{featureValueId}/delete  admin_feature_values_delete  [FeatureValueController::deleteAction]
POST,DELETE   /{featureId}/values/bulk-delete           admin_feature_values_bulk_delete  [FeatureValueController::bulkDeleteAction]
POST          /{featureId}/values/update-position       admin_feature_values_update_position  [FeatureValueController::updatePositionAction]
GET           /values/{featureId}                       admin_feature_get_feature_values  [FeatureValueController::getFeatureValuesAction]
GET           /all-feature-groups                       admin_all_feature_groups  [FeatureController::getAllFeatureGroupsAction]
```

### admin/sell/catalog/manufacturer
```
GET           /                                         admin_manufacturers_index  [ManufacturerController::indexAction]
POST          /                                         admin_manufacturers_search  [ManufacturerController::searchAction]
GET,POST      /new                                      admin_manufacturers_create  [ManufacturerController::createAction]
GET           /{manufacturerId}/view                    admin_manufacturers_view  [ManufacturerController::viewAction]
GET,POST      /{manufacturerId}/edit                    admin_manufacturers_edit  [ManufacturerController::editAction]
POST,DELETE   /{manufacturerId}/delete                  admin_manufacturers_delete  [ManufacturerController::deleteAction]
POST          /bulk-delete                              admin_manufacturers_bulk_delete  [ManufacturerController::bulkDeleteAction]
POST          /bulk-enable                              admin_manufacturers_bulk_enable_status  [ManufacturerController::bulkEnableAction]
POST          /bulk-disable                             admin_manufacturers_bulk_disable_status  [ManufacturerController::bulkDisableAction]
POST          /{manufacturerId}/toggle-status           admin_manufacturers_toggle_status  [ManufacturerController::toggleStatusAction]
GET           /export                                   admin_manufacturers_export  [ManufacturerController::exportAction]
GET,POST      /{manufacturerId}/delete-logo-image       admin_manufacturers_delete_logo_image  [ManufacturerController::deleteLogoImageAction]
GET,POST      addresses/new                             admin_manufacturer_addresses_create  [ManufacturerController::createAddressAction]
GET,POST      addresses/{addressId}/edit                admin_manufacturer_addresses_edit  [ManufacturerController::editAddressAction]
POST,DELETE   addresses/{addressId}/delete              admin_manufacturer_addresses_delete  [ManufacturerController::deleteAddressAction]
POST          addresses/bulk-delete                     admin_manufacturer_addresses_bulk_delete  [ManufacturerController::bulkDeleteAddressAction]
GET           addresses/export                          admin_manufacturer_addresses_export  [ManufacturerController::exportAddressAction]
```

### admin/sell/catalog/monitoring
```
GET           /                                         admin_monitorings_index  [MonitoringController::indexAction]
POST          /                                         admin_monitorings_search  [MonitoringController::searchAction]
POST          /delete-bulk                              admin_monitoring_products_bulk_delete  [MonitoringController::deleteBulkAction]
```

### admin/sell/catalog/products/combination
```
GET           /{productId}/combinations                 admin_products_combinations  [CombinationController::getListAction]
GET           /{productId}/combinations/ids             admin_products_combinations_ids  [CombinationController::getCombinationIdsAction]
PATCH         /combinations/{productId}/update-combination-from-listing  admin_products_combinations_update_combination_from_listing  [CombinationController::updateCombinationFromListingAction]
GET,POST      /combinations/{combinationId}/edit        admin_products_combinations_edit_combination  [CombinationController::editAction]
GET,PATCH     /{productId}/combinations/bulk-form       admin_products_combinations_bulk_combination_form  [CombinationController::bulkEditFormAction]
PATCH         /{productId}/combinations/bulk-edit       admin_products_combinations_bulk_edit_combination  [CombinationController::bulkEditAction]
DELETE        /combinations/{combinationId}/delete/{shopId}  admin_products_combinations_delete_combination  [CombinationController::deleteAction]
POST          /{productId}/combinations/bulk-delete/{shopId}  admin_products_combinations_bulk_delete  [CombinationController::bulkDeleteAction]
GET           /{productId}/attribute-groups/{shopId}    admin_products_attribute_groups  [CombinationController::getAttributeGroupsAction]
GET           /all-attribute-groups/{shopId}            admin_all_attribute_groups  [CombinationController::getAllAttributeGroupsAction]
POST          /generate-combinations/{productId}/{shopId}  admin_products_combinations_generate  [CombinationController::generateCombinationsAction]
```

### admin/sell/catalog/products/image
```
GET           /{productId}/images-for-shop/{shopId}     admin_products_images_for_shop  [ImageController::getImagesForShopAction]
GET,POST      /{productId}/shopImages                   admin_products_product_shop_images  [ImageController::productShopImagesAction]
POST          /images/add                               admin_products_add_image  [ImageController::addImageAction]
PATCH         /images/{productImageId}/update           admin_products_update_image  [ImageController::updateImageAction]
POST          /images/{productImageId}/delete           admin_products_delete_image  [ImageController::deleteImageAction]
```

### admin/sell/catalog/products/product
```
GET           /                                         admin_products_index  [ProductController::indexAction]
GET           /legacy-list                              admin_product_catalog  [ProductController::backwardCompatibleListAction]
GET           /light-list                               admin_products_light_list  [ProductController::lightListAction]
GET           /{productId}/preview/{shopId}             admin_products_preview  [ProductController::previewAction]
GET           /export                                   admin_products_export  [ProductController::exportAction]
POST          /                                         admin_products_search  [ProductController::searchGridAction]
POST          /reset_grid_search                        admin_products_reset_grid_search  [ProductController::resetGridSearchAction]
POST          /grid_category_filter                     admin_products_grid_category_filter  [ProductController::gridCategoryFilterAction]
GET           /shop_previews/{productId}/{shopGroupId}  admin_products_grid_shop_previews  [ProductController::productShopPreviewsAction]
GET,POST      /create                                   admin_products_create  [ProductController::createAction]
GET,POST,PATCH  /{productId}/edit                         admin_products_edit  [ProductController::editAction]
GET           /{id}                                     admin_product_form  [ProductController::backwardCompatibleEditAction]
GET,POST,PATCH  /{productId}/shops                        admin_products_select_shops  [ProductController::selectProductShopsAction]
GET           /virtual-product-file/{virtualProductFileId}  admin_products_download_virtual_product_file  [ProductController::downloadVirtualFileAction]
POST,DELETE   /{productId}/delete-from-all-shops        admin_products_delete_from_all_shops  [ProductController::deleteFromAllShopsAction]
POST,DELETE   /{productId}/delete-from-shop-group/{shopGroupId}  admin_products_delete_from_shop_group  [ProductController::deleteFromShopGroupAction]
POST,DELETE   /{productId}/delete-from-shop/{shopId}    admin_products_delete_from_shop  [ProductController::deleteFromShopAction]
POST          /{productId}/duplicate-all-shops          admin_products_duplicate_all_shops  [ProductController::duplicateAllShopsAction]
POST          /{productId}/duplicate-shop/{shopId}      admin_products_duplicate_shop  [ProductController::duplicateShopAction]
POST          /{productId}/duplicate-shop-group/{shopGroupId}  admin_products_duplicate_shop_group  [ProductController::duplicateShopGroupAction]
POST          /{productId}/toggle-status-for-shop/{shopId}  admin_products_toggle_status_for_shop  [ProductController::toggleStatusForShopAction]
POST          /{productId}/toggle-status-for-all-shops  admin_products_toggle_status_for_all_shops  [ProductController::toggleStatusForAllShopsAction]
GET           /{productId}/enable-for-all-shops         admin_products_enable_for_all_shops  [ProductController::enableForAllShopsAction]
GET           /{productId}/enable-for-shop-group/{shopGroupId}  admin_products_enable_for_shop_group  [ProductController::enableForShopGroupAction]
GET           /{productId}/disable-for-all-shops        admin_products_disable_for_all_shops  [ProductController::disableForAllShopsAction]
GET           /{productId}/disable-for-shop-group/{shopGroupId}  admin_products_disable_for_shop_group  [ProductController::disableForShopGroupAction]
POST          /update_position                          admin_products_update_position  [ProductController::updatePositionAction]
POST          /bulk-enable-all-shops                    admin_products_bulk_enable_all_shops  [ProductController::bulkEnableAllShopsAction]
POST          /bulk-enable-shop/{shopId}                admin_products_bulk_enable_shop  [ProductController::bulkEnableShopAction]
POST          /bulk-enable-shop-group/{shopGroupId}     admin_products_bulk_enable_shop_group  [ProductController::bulkEnableShopGroupAction]
POST          /bulk-disable-for-all-shops               admin_products_bulk_disable_all_shops  [ProductController::bulkDisableAllShopsAction]
POST          /bulk-disable-shop/{shopId}               admin_products_bulk_disable_shop  [ProductController::bulkDisableShopAction]
POST          /bulk-disable-shop-group/{shopGroupId}    admin_products_bulk_disable_shop_group  [ProductController::bulkDisableShopGroupAction]
POST          /bulk-duplicate-all-shops                 admin_products_bulk_duplicate_all_shops  [ProductController::bulkDuplicateAllShopsAction]
POST          /bulk-duplicate-shop/{shopId}             admin_products_bulk_duplicate_shop  [ProductController::bulkDuplicateShopAction]
POST          /bulk-duplicate-shop-group/{shopGroupId}  admin_products_bulk_duplicate_shop_group  [ProductController::bulkDuplicateShopGroupAction]
POST,DELETE   /bulk-delete-from-all-shops               admin_products_bulk_delete_from_all_shops  [ProductController::bulkDeleteFromAllShopsAction]
POST,DELETE   /bulk-delete-from-shop/{shopId}           admin_products_bulk_delete_from_shop  [ProductController::bulkDeleteFromShopAction]
POST,DELETE   /bulk-delete-from-shop-group/{shopGroupId}  admin_products_bulk_delete_from_shop_group  [ProductController::bulkDeleteFromShopGroupAction]
GET,POST      /search/{languageCode}                    admin_products_search_products_for_association  [ProductController::searchProductsForAssociationAction]
GET,POST      /search/combination/{languageCode}        admin_products_search_combinations_for_association  [CombinationController::searchCombinationsForAssociationAction]
GET           /{productId}/search-product-combinations/{shopId}/{languageId}  admin_products_search_product_combinations  [CombinationController::searchProductCombinationsAction]
GET           /{productId}/quantity/{shopId}            admin_products_quantity  [ProductController::quantityAction]
GET           /category-tree                            admin_category_tree  [ProductController::legacyCategoryTreeAction]
```

### admin/sell/catalog/products/specific_price
```
GET           /{productId}/specific-prices/list         admin_products_specific_prices_list  [SpecificPriceController::listAction]
GET,POST      /{productId}/specific-prices/create       admin_products_specific_prices_create  [SpecificPriceController::createAction]
GET,POST      /specific-prices/{specificPriceId}/edit   admin_products_specific_prices_edit  [SpecificPriceController::editAction]
DELETE        /specific-prices/{specificPriceId}/delete  admin_products_specific_prices_delete  [SpecificPriceController::deleteAction]
```

### admin/sell/catalog/suppliers
```
GET           /                                         admin_suppliers_index  [SupplierController::indexAction]
POST          /                                         admin_suppliers_search  [CommonController::searchGridAction]
GET           /{supplierId}/products                    admin_suppliers_view  [SupplierController::viewAction]
GET,POST      /new                                      admin_suppliers_create  [SupplierController::createAction]
GET,POST      /{supplierId}/edit                        admin_suppliers_edit  [SupplierController::editAction]
DELETE        /{supplierId}/delete                      admin_suppliers_delete  [SupplierController::deleteAction]
POST          /bulk-delete                              admin_suppliers_bulk_delete  [SupplierController::bulkDeleteAction]
POST          /bulk-enable                              admin_suppliers_bulk_enable  [SupplierController::bulkEnableAction]
POST          /bulk-disable                             admin_suppliers_bulk_disable  [SupplierController::bulkDisableAction]
POST          /{supplierId}/toggle-status               admin_suppliers_toggle_status  [SupplierController::toggleStatusAction]
GET           /export                                   admin_suppliers_export  [SupplierController::exportAction]
GET,POST      /{supplierId}/delete-logo-image           admin_suppliers_delete_logo_image  [SupplierController::deleteLogoImageAction]
```

### admin/sell/customer/addresses
```
GET           /                                         admin_addresses_index  [AddressController::indexAction]
POST          /                                         admin_addresses_search  [CommonController::searchGridAction]
GET,POST      /new                                      admin_addresses_create  [AddressController::createAction]
GET,POST      /{addressId}/edit                         admin_addresses_edit  [AddressController::editAction]
GET,POST      /order/{orderId}/{addressType}/edit       admin_order_addresses_edit  [AddressController::editOrderAddressAction]
GET,POST      /cart/{cartId}/{addressType}/edit         admin_cart_addresses_edit  [AddressController::editCartAddressAction]
POST          /delete-bulk                              admin_addresses_delete_bulk  [AddressController::deleteBulkAction]
POST,DELETE   /{addressId}/delete                       admin_addresses_delete  [AddressController::deleteAction]
POST          /save-required-fields                     admin_addresses_save_required_fields  [AddressController::saveRequiredFieldsAction]
```

### admin/sell/customer/customers
```
GET           /                                         admin_customers_index  [CustomerController::indexAction]
POST          /                                         admin_customers_filter  [CommonController::searchGridAction]
GET,POST      /new                                      admin_customers_create  [CustomerController::createAction]
GET,POST      /{customerId}/edit                        admin_customers_edit  [CustomerController::editAction]
GET,POST      /{customerId}/view                        admin_customers_view  [CustomerController::viewAction]
POST          /{customerId}/set-private-note            admin_customers_set_private_note  [CustomerController::setPrivateNoteAction]
POST          /{customerId}/toggle-status               admin_customers_toggle_status  [CustomerController::toggleStatusAction]
POST          /{customerId}/transform-guest-to-customer  admin_customers_transform_guest_to_customer  [CustomerController::transformGuestToCustomerAction]
POST          /{customerId}/toggle-newsletter-subscription  admin_customers_toggle_newsletter_subscription  [CustomerController::toggleNewsletterSubscriptionAction]
POST          /set-required-fields                      admin_customers_set_required_fields  [CustomerController::setRequiredFieldsAction]
POST          /{customerId}/toggle-partner-offer-subscription  admin_customers_toggle_partner_offer_subscription  [CustomerController::togglePartnerOfferSubscriptionAction]
POST          /delete-bulk                              admin_customers_delete_bulk  [CustomerController::deleteBulkAction]
POST          /delete                                   admin_customers_delete  [CustomerController::deleteAction]
POST          /enable-bulk                              admin_customers_enable_bulk  [CustomerController::enableBulkAction]
POST          /disable-bulk                             admin_customers_disable_bulk  [CustomerController::disableBulkAction]
GET           /export                                   admin_customers_export  [CustomerController::exportAction]
GET           /search                                   admin_customers_search  [CustomerController::searchAction]
GET           /customer-information                     admin_customer_for_address_information  [CustomerController::getCustomerInformationAction]
GET           /{customerId}/carts                       admin_customers_carts  [CustomerController::getCartsAction]
GET           /{customerId}/orders                      admin_customers_orders  [CustomerController::getOrdersAction]
```

### admin/sell/customer/outstanding
```
GET           /                                         admin_outstanding_index  [OutstandingController::indexAction]
POST          /                                         admin_outstanding_search  [OutstandingController::searchAction]
```

### admin/sell/customer_service/customer_threads
```
GET           /                                         admin_customer_threads  [CustomerThreadController::indexAction]
POST          /                                         admin_customer_threads_filter  [CommonController::searchGridAction]
GET           /{customerThreadId}/view                  admin_customer_threads_view  [CustomerThreadController::viewAction]
POST          /{customerThreadId}/reply                 admin_customer_threads_reply  [CustomerThreadController::replyAction]
POST          /{customerThreadId}/update-status         admin_customer_threads_view_update_status  [CustomerThreadController::updateStatusFromViewAction]
POST          /list/{customerThreadId}/update-status    admin_customer_threads_list_update_status  [CustomerThreadController::updateStatusFromListAction]
POST          /{customerThreadId}/forward               admin_customer_threads_forward  [CustomerThreadController::forwardAction]
POST,DELETE   /{customerThreadId}/delete                admin_customer_threads_delete  [CustomerThreadController::deleteAction]
POST,DELETE   /bulk_delete                              admin_customer_threads_bulk_delete  [CustomerThreadController::bulkDeleteAction]
```

### admin/sell/customer_service/merchandise_return
```
GET           /                                         admin_merchandise_returns_index  [MerchandiseReturnController::indexAction]
PATCH,POST    /options                                  admin_merchandise_returns_save_options  [MerchandiseReturnController::indexAction]
POST          /                                         admin_merchandise_returns_filter  [CommonController::searchGridAction]
GET,POST      /{orderReturnId}/edit                     admin_order_returns_edit  [MerchandiseReturnController::editAction]
POST          /{orderReturnId}/update                   admin_order_returns_update  [MerchandiseReturnController::editAction]
```

### admin/sell/customer_service/order_message
```
GET           /                                         admin_order_messages_index  [OrderMessageController::indexAction]
POST          /                                         admin_order_messages_filter  [CommonController::searchGridAction]
GET,POST      /new                                      admin_order_messages_create  [OrderMessageController::createAction]
GET,POST      /{orderMessageId}/edit                    admin_order_messages_edit  [OrderMessageController::editAction]
POST          /{orderMessageId}/delete                  admin_order_messages_delete  [OrderMessageController::deleteAction]
POST          /bulk-delete                              admin_order_messages_bulk_delete  [OrderMessageController::bulkDeleteAction]
```

### admin/sell/orders/carts
```
GET           /                                         admin_carts_index  [CartController::indexAction]
POST          /                                         admin_carts_search  [CommonController::searchGridAction]
DELETE        /{cartId}                                 admin_carts_delete  [CartController::deleteCartAction]
POST          /bulk-delete                              admin_carts_bulk_delete  [CartController::bulkDeleteCartAction]
GET           /export                                   admin_carts_export  [CartController::exportCartAction]
GET           /{cartId}/view                            admin_carts_view  [CartController::viewAction]
GET           /{cartId}/info                            admin_carts_info  [CartController::getInfoAction]
POST          /new                                      admin_carts_create  [CartController::createAction]
POST          /{cartId}/addresses                       admin_carts_edit_addresses  [CartController::editAddressesAction]
POST          /{cartId}/carrier                         admin_carts_edit_carrier  [CartController::editCarrierAction]
POST          /{cartId}/currency                        admin_carts_edit_currency  [CartController::editCurrencyAction]
POST          /{cartId}/language                        admin_carts_edit_language  [CartController::editLanguageAction]
POST          /{cartId}/rules/delivery-settings         admin_carts_set_delivery_settings  [CartController::updateDeliverySettingsAction]
POST          /{cartId}/cart-rules                      admin_carts_add_cart_rule  [CartController::addCartRuleAction]
POST          /{cartId}/cart-rules/{cartRuleId}/delete  admin_carts_delete_cart_rule  [CartController::deleteCartRuleAction]
POST          /{cartId}/products                        admin_carts_add_product  [CartController::addProductAction]
POST          /{cartId}/products/{productId}/price      admin_carts_edit_product_price  [CartController::editProductPriceAction]
POST          /{cartId}/products/{productId}/quantity   admin_carts_edit_product_quantity  [CartController::editProductQuantityAction]
POST          /{cartId}/delete-product                  admin_carts_delete_product  [CartController::deleteProductAction]
```

### admin/sell/orders/credit_slips
```
GET           /                                         admin_credit_slips_index  [CreditSlipController::indexAction]
POST          /search                                   admin_credit_slips_search  [CommonController::searchGridAction]
GET           /{creditSlipId}/pdf                       admin_credit_slips_generate_pdf  [CreditSlipController::generatePdfAction]
GET           /pdf-by-date                              _admin_credit_slips_pdf_by_date  [CreditSlipController::generatePdfByDateAction]
POST          /                                         admin_credit_slips_process_options  [CreditSlipController::indexAction]
```

### admin/sell/orders/delivery_slips
```
GET,POST      /                                         admin_order_delivery_slip  [DeliveryController::slipAction]
POST          /pdf                                      admin_order_delivery_slip_pdf  [DeliveryController::generatePdfAction]
```

### admin/sell/orders/invoices
```
GET           /                                         admin_order_invoices  [InvoicesController::indexAction]
PATCH,POST    /                                         admin_order_invoices_process  [InvoicesController::processAction]
POST          /by_date                                  admin_order_invoices_generate_by_date  [InvoicesController::generatePdfByDateAction]
POST          /by_status                                admin_order_invoices_generate_by_status  [InvoicesController::generatePdfByStatusAction]
GET           /{invoiceId}/generate                     admin_order_invoices_generate_by_id  [InvoicesController::generatePdfByIdAction]
```

### admin/sell/orders/orders
```
GET           /                                         admin_orders_index  [OrderController::indexAction]
GET           /new                                      admin_orders_create  [OrderController::createAction]
POST          /place                                    admin_orders_place  [OrderController::placeAction]
POST          /                                         admin_orders_search  [CommonController::searchGridAction]
GET           /{orderId}/generate-invoice-pdf           admin_orders_generate_invoice_pdf  [OrderController::generateInvoicePdfAction]
GET           /{orderId}/generate-delivery-slip-pdf     admin_orders_generate_delivery_slip_pdf  [OrderController::generateDeliverySlipPdfAction]
POST          /change-orders-status                     admin_orders_change_orders_status  [OrderController::changeOrdersStatusAction]
GET           /export                                   admin_orders_export  [OrderController::exportAction]
GET,POST      /{orderId}/view                           admin_orders_view  [OrderController::viewAction]
POST          /{orderId}/cart-rules                     admin_orders_add_cart_rule  [OrderController::addCartRuleAction]
POST          /list/{orderId}/status                    admin_orders_list_update_status  [OrderController::updateStatusFromListAction]
POST          /{orderId}/status                         admin_orders_update_status  [OrderController::updateStatusAction]
POST          /{orderId}/payment                        admin_orders_add_payment  [OrderController::addPaymentAction]
POST          /{orderId}/duplicate-cart                 admin_orders_duplicate_cart  [OrderController::duplicateOrderCartAction]
POST          /{orderId}/currency/change                admin_orders_change_currency  [OrderController::changeCurrencyAction]
POST          /{orderId}/products/{orderDetailId}       admin_orders_update_product  [OrderController::updateProductAction]
GET           /{orderId}/cart-rules/{orderCartRuleId}/delete  admin_orders_remove_cart_rule  [OrderController::removeCartRuleAction]
POST          /{orderId}/history/{orderHistoryId}/statuses/{orderStatusId}/resend-email  admin_orders_resend_email  [OrderController::resendEmailAction]
GET           /{orderId}/preview                        admin_orders_preview  [OrderController::previewAction]
POST          /{orderId}/shipping                       admin_orders_update_shipping  [OrderController::updateShippingAction]
POST          /{orderId}/invoice/{orderInvoiceId}/note  admin_orders_update_invoice_note  [OrderController::updateInvoiceNoteAction]
POST          /{orderId}/shipment/{shipmentId}/split    admin_orders_split_shipment  [OrderController::splitShipmentAction]
GET           /{orderId}/shipment/split-form            admin_orders_shipment_get_split_form  [OrderController::getSplitShipmentForm]
POST          /{orderId}/invoice                        admin_orders_generate_invoice  [OrderController::generateInvoiceAction]
GET           /{orderId}/shipment/merge-form            admin_orders_shipment_get_merge_form  [OrderController::getMergeShipmentForm]
GET           /{orderId}/shipment/{shipmentId}/edit-form  admin_orders_shipment_get_edit_form  [OrderController::getEditShipmentForm]
POST          /{orderId}/shipment/merge                 admin_orders_merge_shipment  [OrderController::mergeShipmentAction]
GET           /{orderId}/product/add                    admin_orders_get_add_product_form  [OrderController::getAddProductForm]
GET           /{orderId}/product/{orderDetailId}/edit   admin_orders_get_edit_product_form  [OrderController::getEditProductForm]
PUT           /{orderId}/shipment/{shipmentId}/edit     admin_orders_edit_shipment  [OrderController::editShipmentAction]
POST          /change-customer-address                  admin_orders_change_customer_address  [OrderController::changeCustomerAddressAction]
POST          /{orderId}/send-message                   admin_orders_send_message  [OrderController::sendMessageAction]
POST          /{orderId}/partial-refund                 admin_orders_partial_refund  [OrderController::partialRefundAction]
POST          /{orderId}/standard-refund                admin_orders_standard_refund  [OrderController::standardRefundAction]
POST          /{orderId}/return-product                 admin_orders_return_product  [OrderController::returnProductAction]
POST          /process-order-email                      admin_orders_send_process_order_email  [OrderController::sendProcessOrderEmailAction]
POST          /orders/{orderId}/product/{productId}/shipments  admin_orders_get_shipments_for_product  [OrderController::getShipmentsForProduct]
POST          /orders/product/{productId}/carriers      admin_orders_get_carriers_for_product  [OrderController::getCarriersForProduct]
POST          /{orderId}/products                       admin_orders_add_product  [OrderController::addProductAction]
POST          /{orderId}/products/{orderDetailId}/delete  admin_orders_delete_product  [OrderController::deleteProductAction]
GET           /{orderId}/discounts                      admin_orders_get_discounts  [OrderController::getDiscountsAction]
GET           /{orderId}/prices                         admin_orders_get_prices  [OrderController::getPricesAction]
GET           /{orderId}/payments                       admin_orders_get_payments  [OrderController::getPaymentsAction]
GET           /{orderId}/products                       admin_orders_get_products  [OrderController::getProductsListAction]
GET           /{orderId}/invoices                       admin_orders_get_invoices  [OrderController::getInvoicesAction]
GET           /{orderId}/documents                      admin_orders_get_documents  [OrderController::getDocumentsAction]
GET           /{orderId}/shipping                       admin_orders_get_shipping  [OrderController::getShippingAction]
GET           /{orderId}/shipments                      admin_orders_get_shipments  [OrderController::getShipmentsAction]
POST          /{orderId}/cancellation                   admin_orders_cancellation  [OrderController::cancellationAction]
POST          /configure-product-pagination             admin_orders_configure_product_pagination  [OrderController::configureProductPaginationAction]
GET           /display-customization-image/{orderId}/{value}  admin_orders_display_customization_image  [OrderController::displayCustomizationImageAction]
GET           /{orderId}/products/prices                admin_orders_product_prices  [OrderController::getProductPricesAction]
GET           /products/search                          admin_orders_products_search  [OrderController::searchProductsAction]
POST          /{orderId}/set-internal-note              admin_orders_set_internal_note  [OrderController::setInternalNoteAction]
```

### admin/sell/stocks
```
GET           /                                         admin_stock_overview  [StockController::overviewAction]
GET           /movements                                admin_stock_movements_overview  [StockController::overviewAction]
```

## admin-api

### admin-api/oauth2
```
POST          /access_token                             api_oauth2_access_token  [AccessTokenController]
```

## api

### api/attributes
```
GET           /                                         api_stock_list_attributes  [AttributeController::listAttributesAction]
```

### api/categories
```
GET           /                                         api_stock_list_categories  [CategoryController::listCategoriesAction]
```

### api/features
```
GET           /                                         api_stock_list_features  [FeatureController::listFeaturesAction]
```

### api/i18n
```
GET           /{page}                                   api_i18n_translations_list  [I18nController::listTranslationAction]
```

### api/improve/design/positions
```
POST          /update                                   api_improve_design_positions_update  [PositionsController::updateAction]
```

### api/manufacturers
```
GET           /                                         api_stock_list_manufacturers  [ManufacturerController::listManufacturersAction]
```

### api/stock_movements
```
GET           /                                         api_stock_list_movements  [StockMovementController::listMovementsAction]
GET           /product/{productId}                      api_stock_product_list_movements  [StockMovementController::listMovementsAction]
GET           /employees                                api_stock_list_movements_employees  [StockMovementController::listMovementsEmployeesAction]
GET           /types                                    api_stock_list_movements_types  [StockMovementController::listMovementsTypesAction]
```

### api/stocks
```
GET           /                                         api_stock_list_products  [StockController::listProductsAction]
GET           /export                                   api_stock_list_products_export  [StockController::listProductsExportAction]
GET           /{productId}                              api_stock_list_product_combinations  [StockController::listProductsAction]
POST          /product/{productId}                      api_stock_edit_product  [StockController::editProductAction]
POST          /product/{productId}/combination/{combinationId}  api_stock_edit_product_combination  [StockController::editProductAction]
POST          /products                                 api_stock_bulk_edit_products  [StockController::bulkEditProductsAction]
```

### api/suppliers
```
GET           /                                         api_stock_list_suppliers  [SupplierController::listSuppliersAction]
```

### api/translations
```
GET           /tree/{lang}/{type}/{selected}            api_translation_domains_tree  [TranslationController::listTreeAction]
GET           /{locale}/{domain}/{theme}                api_translation_domain_catalog  [TranslationController::listDomainTranslationAction]
POST          /edit                                     api_translation_value_edit  [TranslationController::translationEditAction]
POST          /reset                                    api_translation_value_reset  [TranslationController::translationResetAction]
```

