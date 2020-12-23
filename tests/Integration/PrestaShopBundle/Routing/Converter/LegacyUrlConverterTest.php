<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Routing\Converter;

use Link;
use PrestaShopBundle\Routing\Converter\Exception\AlreadyConvertedException;
use PrestaShopBundle\Routing\Converter\LegacyUrlConverter;
use Tests\TestCase\SymfonyIntegrationTestCase;

class LegacyUrlConverterTest extends SymfonyIntegrationTestCase
{
    /** @var Link|null */
    private $link;

    protected function setUp(): void
    {
        parent::setUp();
        if (!$this->link) {
            $this->link = new Link();
        }
    }

    /**
     * @return array<string, array<string|array>>
     */
    public static function getMigratedControllers(): array
    {
        return [
            'admin_administration' => ['/configure/advanced/administration/', 'AdminAdminPreferences'],
            'admin_administration_general_save' => ['/configure/advanced/administration/general', 'AdminAdminPreferences', 'update'],

            'admin_backups' => ['/configure/advanced/backups/', 'AdminBackup'],
            'admin_backups_save_options' => ['/configure/advanced/backups/', 'AdminBackup', 'update'],
            'admin_backups_create' => ['/configure/advanced/backups/new', 'AdminBackup', 'addbackup'],
            'admin_backups_delete' => ['/configure/advanced/backups/backup_file.zip', 'AdminBackup', 'delete', ['filename' => 'backup_file.zip']],
            'admin_backups_bulk_delete' => ['/configure/advanced/backups/bulk-delete/', 'AdminBackup', 'submitBulkdeletebackup'],

            'admin_module_manage' => ['/improve/modules/manage', 'AdminModulesManage'],
            'admin_module_manage_alias' => ['/improve/modules/manage', 'AdminModulesSf'],

            'admin_module_notification' => ['/improve/modules/alerts', 'AdminModulesNotifications'],
            'admin_module_notification_count' => ['/improve/modules/alerts/count', 'AdminModulesNotifications', 'count'],

            'admin_module_updates' => ['/improve/modules/updates', 'AdminModulesUpdates'],

            'admin_modules_positions' => ['/improve/design/modules/positions/', 'AdminModulesPositions'],
            'admin_modules_positions_unhook' => ['/improve/design/modules/positions/unhook', 'AdminModulesPositions', 'unhook'],

            'admin_customer_preferences' => ['/configure/shop/customer-preferences/', 'AdminCustomerPreferences'],
            'admin_customer_preferences_process' => ['/configure/shop/customer-preferences/', 'AdminCustomerPreferences', 'update'],

            'admin_customers_index' => ['/sell/customers/', 'AdminCustomers'],
            'admin_customers_filter' => ['/sell/customers/', 'AdminCustomers', 'submitFiltercustomer'],
            'admin_customers_create' => ['/sell/customers/new', 'AdminCustomers', 'addcustomer'],
            'admin_customers_edit' => ['/sell/customers/42/edit', 'AdminCustomers', 'updatecustomer', ['id_customer' => 42]],
            'admin_customers_view' => ['/sell/customers/42/view', 'AdminCustomers', 'viewcustomer', ['id_customer' => 42]],
            'admin_customers_save_private_note' => ['/sell/customers/42/set-private-note', 'AdminCustomers', 'updateCustomerNote', ['id_customer' => 42]],
            'admin_customers_toggle_status' => ['/sell/customers/42/toggle-status', 'AdminCustomers', 'statuscustomer', ['id_customer' => 42]],
            'admin_customers_transform_guest_to_customer' => ['/sell/customers/42/transform-guest-to-customer', 'AdminCustomers', 'guesttocustomer', ['id_customer' => 42]],
            'admin_customers_toggle_newsletter_subscription' => ['/sell/customers/42/toggle-newsletter-subscription', 'AdminCustomers', 'changeNewsletterVal', ['id_customer' => 42]],
            'admin_customers_set_required_fields' => ['/sell/customers/set-required-fields', 'AdminCustomers', 'submitFields'],
            'admin_customers_toggle_partner_offer_subscription' => ['/sell/customers/42/toggle-partner-offer-subscription', 'AdminCustomers', 'changeOptinVal', ['id_customer' => 42]],
            'admin_customers_delete_bulk' => ['/sell/customers/delete-bulk', 'AdminCustomers', 'submitBulkdeletecustomer'],
            'admin_customers_delete' => ['/sell/customers/delete', 'AdminCustomers', 'deletecustomer'],
            'admin_customers_enable_bulk' => ['/sell/customers/enable-bulk', 'AdminCustomers', 'submitBulkenableSelectioncustomer'],
            'admin_customers_disable_bulk' => ['/sell/customers/disable-bulk', 'AdminCustomers', 'submitBulkdisableSelectioncustomer'],
            'admin_customers_export' => ['/sell/customers/export', 'AdminCustomers', 'exportcustomer'],
            'admin_customers_search' => ['/sell/customers/search', 'AdminCustomers', 'searchCustomers'],

            'admin_order_delivery_slip' => ['/sell/orders/delivery-slips/', 'AdminDeliverySlip'],
            'admin_order_delivery_slip_pdf' => ['/sell/orders/delivery-slips/pdf', 'AdminDeliverySlip', 'submitAdddelivery'],

            'admin_import' => ['/configure/advanced/import/', 'AdminImport'],
            'admin_import_file_upload' => ['/configure/advanced/import/file/upload', 'AdminImport', 'uploadCsv'],
            'admin_import_file_delete' => ['/configure/advanced/import/file/delete', 'AdminImport', 'delete'],
            'admin_import_file_download' => ['/configure/advanced/import/file/download', 'AdminImport', 'download'],
            'admin_import_sample_download' => ['/configure/advanced/import/sample/download/categories_import', 'AdminImport', 'sampleDownload', ['sampleName' => 'categories_import']],

            'admin_system_information' => ['/configure/advanced/system-information/', 'AdminInformation'],
            'admin_system_information_check_files' => ['/configure/advanced/system-information/files', 'AdminInformation', 'checkFiles'],

            'admin_logs_index' => ['/configure/advanced/logs/', 'AdminLogs'],
            'admin_logs_save_settings' => ['/configure/advanced/logs/settings', 'AdminLogs', 'update'],
            'admin_logs_delete_all' => ['/configure/advanced/logs/delete-all', 'AdminLogs', 'deletelog'],

            'admin_maintenance' => ['/configure/shop/maintenance/', 'AdminMaintenance'],
            'admin_maintenance_save' => ['/configure/shop/maintenance/', 'AdminMaintenance', 'update'],

            'admin_order_preferences' => ['/configure/shop/order-preferences/', 'AdminOrderPreferences'],
            'admin_order_preferences_general_save' => ['/configure/shop/order-preferences/general', 'AdminOrderPreferences', 'update'],

            'admin_product_preferences' => ['/configure/shop/product-preferences/', 'AdminPPreferences'],
            'admin_product_preferences_general_save' => ['/configure/shop/product-preferences/general', 'AdminPPreferences', 'update'],

            'admin_performance' => ['/configure/advanced/performance/', 'AdminPerformance'],
            'admin_performance_smarty_save' => ['/configure/advanced/performance/smarty', 'AdminPerformance', 'update'],
            'admin_clear_cache' => ['/configure/advanced/performance/clear-cache', 'AdminPerformance', 'empty_smarty_cache'],
            'admin_servers_add' => ['/configure/advanced/performance/memcache/servers', 'AdminPerformance', 'submitAddServer'],
            'admin_servers_delete' => ['/configure/advanced/performance/memcache/servers', 'AdminPerformance', 'deleteMemcachedServer'],

            'admin_preferences' => ['/configure/shop/preferences/preferences', 'AdminPreferences'],
            'admin_preferences_save' => ['/configure/shop/preferences/preferences', 'AdminPreferences', 'update'],

            'admin_shipping_preferences' => ['/improve/shipping/preferences/', 'AdminShipping'],
            'admin_shipping_preferences_handling_save' => ['/improve/shipping/preferences/handling', 'AdminShipping', 'update'],

            'admin_stock_overview' => ['/sell/stocks/', 'AdminStockManagement'],

            'admin_international_translation_overview' => ['/improve/international/translations/', 'AdminTranslationSf'],

            'admin_payment_methods' => ['/improve/payment/payment_methods', 'AdminPayment'],

            'admin_localization_index' => ['/improve/international/localization/', 'AdminLocalization'],
            'admin_localization_configuration_save' => ['/improve/international/localization/configuration', 'AdminLocalization', 'update'],
            'admin_localization_import_pack' => ['/improve/international/localization/import-pack', 'AdminLocalization', 'submitLocalizationPack'],

            'admin_geolocation_index' => ['/improve/international/geolocation/', 'AdminGeolocation'],
            'admin_geolocation_by_ip_address_save' => ['/improve/international/geolocation/by-ip-address', 'AdminGeolocation', 'update'],

            'admin_payment_preferences' => ['/improve/payment/preferences', 'AdminPaymentPreferences'],
            'admin_payment_preferences_carrier_restrictions_save' => ['/improve/payment/carrier-restrictions', 'AdminPaymentPreferences', 'update'],

            'admin_order_invoices' => ['/sell/orders/invoices/', 'AdminInvoices'],
            'admin_order_invoices_process' => ['/sell/orders/invoices/', 'AdminInvoices', 'update'],
            'admin_order_invoices_generate_by_date' => ['/sell/orders/invoices/by_date', 'AdminInvoices', 'submitAddinvoice_date'],
            'admin_order_invoices_generate_by_status' => ['/sell/orders/invoices/by_status', 'AdminInvoices', 'submitAddinvoice_status'],

            'admin_emails_index' => ['/configure/advanced/emails/', 'AdminEmails'],
            'admin_emails_search' => ['/configure/advanced/emails/', 'AdminEmails', 'search'],
            'admin_emails_save_options' => ['/configure/advanced/emails/options', 'AdminEmails', 'update'],
            'admin_emails_send_test' => ['/configure/advanced/emails/send-testing-email', 'AdminEmails', 'testEmail'],
            'admin_emails_delete_bulk' => ['/configure/advanced/emails/delete-bulk', 'AdminEmails', 'submitBulkdeletemail'],
            'admin_emails_delete_all' => ['/configure/advanced/emails/delete-all', 'AdminEmails', 'deleteAll'],
            'admin_emails_delete' => ['/configure/advanced/emails/delete/42', 'AdminEmails', 'deletemail', ['id_mail' => 42]],
            'admin_metas_index' => ['/configure/shop/seo-urls/', 'AdminMeta'],
            'admin_metas_search' => ['/configure/shop/seo-urls/', 'AdminMeta', 'submitFiltermeta'],
            'admin_metas_create' => ['/configure/shop/seo-urls/new', 'AdminMeta', 'addmeta'],
            'admin_metas_edit' => ['/configure/shop/seo-urls/1000/edit', 'AdminMeta', 'updatemeta', ['id_meta' => 1000]],
            'admin_metas_delete' => ['/configure/shop/seo-urls/1000/delete', 'AdminMeta', 'deletemeta', ['id_meta' => 1000]],
            'admin_metas_delete_bulk' => ['/configure/shop/seo-urls/delete', 'AdminMeta', 'submitBulkdeletemeta'],
            'admin_metas_set_up_urls_save' => ['/configure/shop/seo-urls/set-up-urls', 'AdminMeta', 'submitOptionsmeta'],
            'admin_metas_generate_robots_text_file' => ['/configure/shop/seo-urls/generate/robots', 'AdminMeta', 'submitRobots'],

            // 'admin_permissions_index' => ['/configure/advanced/permissions/', 'AdminAccess'],
            // 'admin_permissions_update_tab_permissions' => ['/configure/advanced/permissions/update/permissions/tab', 'AdminAccess', 'updateAccess'],
            // 'admin_permissions_update_module_permissions' => ['/configure/advanced/permissions/update/permissions/module', 'AdminAccess', 'updateModuleAccess'],

            //'admin_module_configure_action' => ['/improve/modules/manage/action/configure/ps_linklist', 'AdminModules', 'configure', ['module_name' => 'ps_linklist']],
            //'admin_module_configure_action_legacy' => ['/improve/modules/manage/action/configure/ps_linklist', 'AdminModules', 'configure', ['configure' => 'ps_linklist']],

            'admin_sql_request' => ['/configure/advanced/sql-requests/', 'AdminRequestSql'],
            'admin_sql_request_search' => ['/configure/advanced/sql-requests/', 'AdminRequestSql', 'search'],
            'admin_sql_request_process' => ['/configure/advanced/sql-requests/process-settings', 'AdminRequestSql', 'update'],
            'admin_sql_request_create' => ['/configure/advanced/sql-requests/new', 'AdminRequestSql', 'addrequest_sql'],
            'admin_sql_request_edit' => ['/configure/advanced/sql-requests/42/edit', 'AdminRequestSql', 'updaterequest_sql', ['id_request_sql' => 42]],
            'admin_sql_request_delete' => ['/configure/advanced/sql-requests/42/delete', 'AdminRequestSql', 'deleterequest_sql', ['id_request_sql' => 42]],
            'admin_sql_request_delete_bulk' => ['/configure/advanced/sql-requests/delete-bulk', 'AdminRequestSql', 'submitBulkdeleterequest_sql'],
            'admin_sql_request_table_columns' => ['/configure/advanced/sql-requests/tables/plop/columns', 'AdminRequestSql', 'ajax', ['table' => 'plop']],
            'admin_sql_request_view' => ['/configure/advanced/sql-requests/42/view', 'AdminRequestSql', 'viewsql_request', ['id_request_sql' => 42]],
            'admin_sql_request_export' => ['/configure/advanced/sql-requests/42/export', 'AdminRequestSql', 'exportsql_request', ['id_request_sql' => 42]],

            'admin_webservice_keys_index' => ['/configure/advanced/webservice-keys/', 'AdminWebservice'],
            'admin_webservice_keys_search' => ['/configure/advanced/webservice-keys/', 'AdminWebservice', 'submitFilterwebservice_account'],
            'admin_webservice_save_settings' => ['/configure/advanced/webservice-keys/settings', 'AdminWebservice', 'submitOptionswebservice_account'],
            'admin_webservice_keys_create' => ['/configure/advanced/webservice-keys/new', 'AdminWebservice', 'addwebservice_account'],
            'admin_webservice_keys_edit' => ['/configure/advanced/webservice-keys/42/edit', 'AdminWebservice', 'updatewebservice_account', ['id_webservice_account' => 42]],
            'admin_webservice_keys_delete' => ['/configure/advanced/webservice-keys/42/delete', 'AdminWebservice', 'deletewebservice_account', ['id_webservice_account' => 42]],
            'admin_webservice_keys_bulk_delete' => ['/configure/advanced/webservice-keys/bulk-delete', 'AdminWebservice', 'submitBulkdeletewebservice_account'],
            'admin_webservice_keys_toggle_status' => ['/configure/advanced/webservice-keys/42/toggle-status', 'AdminWebservice', 'statuswebservice_account', ['id_webservice_account' => 42]],
            'admin_webservice_keys_bulk_enable' => ['/configure/advanced/webservice-keys/bulk-enable', 'AdminWebservice', 'submitBulkenableSelectionwebservice_account'],
            'admin_webservice_keys_bulk_disable' => ['/configure/advanced/webservice-keys/bulk-disable', 'AdminWebservice', 'submitBulkdisableSelectionwebservice_account'],

            'admin_profiles_index' => ['/configure/advanced/profiles/', 'AdminProfiles'],
            'admin_profiles_search' => ['/configure/advanced/profiles/', 'AdminProfiles', 'submitFilterprofile'],
            'admin_profiles_create' => ['/configure/advanced/profiles/new', 'AdminProfiles', 'addprofile'],
            'admin_profiles_edit' => ['/configure/advanced/profiles/42/edit', 'AdminProfiles', 'updateprofile', ['id_profile' => 42]],
            'admin_profiles_bulk_delete' => ['/configure/advanced/profiles/delete/bulk', 'AdminProfiles', 'submitBulkdeleteprofile'],
            'admin_profiles_delete' => ['/configure/advanced/profiles/12/delete', 'AdminProfiles', 'deleteprofile', ['id_profile' => 12]],

            'admin_currencies_index' => ['/improve/international/currencies/', 'AdminCurrencies'],
            'admin_currencies_search' => ['/improve/international/currencies/', 'AdminCurrencies', 'submitFiltercurrency'],
            'admin_currencies_create' => ['/improve/international/currencies/new', 'AdminCurrencies', 'addcurrency'],
            'admin_currencies_edit' => ['/improve/international/currencies/42/edit', 'AdminCurrencies', 'updatecurrency', ['id_currency' => 42]],
            'admin_currencies_delete' => ['/improve/international/currencies/42/delete', 'AdminCurrencies', 'deletecurrency', ['id_currency' => 42]],
            'admin_currencies_toggle_status' => ['/improve/international/currencies/42/toggle-status', 'AdminCurrencies', 'statuscurrency', ['id_currency' => 42]],
            'admin_currencies_refresh_exchange_rates' => ['/improve/international/currencies/refresh-exchange-rates', 'AdminCurrencies', 'SubmitExchangesRates'],

            'admin_employees_index' => ['/configure/advanced/employees/', 'AdminEmployees'],
            'admin_employees_search' => ['/configure/advanced/employees/', 'AdminEmployees', 'submitFilteremployee'],
            'admin_employees_save_options' => ['/configure/advanced/employees/save-options', 'AdminEmployees', 'submitOptionsemployee'],
            'admin_employees_toggle_status' => ['/configure/advanced/employees/42/toggle-status', 'AdminEmployees', 'statusemployee', ['id_employee' => 42]],
            'admin_employees_bulk_enable_status' => ['/configure/advanced/employees/bulk-enable-status', 'AdminEmployees', 'submitBulkenableSelectionemployee'],
            'admin_employees_bulk_disable_status' => ['/configure/advanced/employees/bulk-disable-status', 'AdminEmployees', 'submitBulkdisableSelectionemployee'],
            'admin_employees_delete' => ['/configure/advanced/employees/42/delete', 'AdminEmployees', 'deleteemployee', ['id_employee' => 42]],
            'admin_employees_bulk_delete' => ['/configure/advanced/employees/bulk-delete', 'AdminEmployees', 'submitBulkdeleteemployee'],
            'admin_employees_create' => ['/configure/advanced/employees/new', 'AdminEmployees', 'addemployee'],
            'admin_employees_edit' => ['/configure/advanced/employees/42/edit', 'AdminEmployees', 'updateemployee', ['id_employee' => 42]],

            'admin_international_translations_export_theme' => ['/improve/international/translations/export', 'AdminTranslations', 'submitExport'],
            'admin_international_translations_add_update_language' => ['/improve/international/translations/add-update-language', 'AdminTranslations', 'submitAddLanguage'],
            'admin_international_translations_copy_language' => ['/improve/international/translations/copy', 'AdminTranslations', 'submitCopyLang'],

            'admin_themes_index' => ['/improve/design/themes/', 'AdminThemes'],
            'admin_themes_upload_logos' => ['/improve/design/themes/upload-logos', 'AdminThemes', 'submitOptionsconfiguration'],
            'admin_themes_export_current' => ['/improve/design/themes/export', 'AdminThemes', 'exporttheme'],
            'admin_themes_import' => ['/improve/design/themes/import', 'AdminThemes', 'importtheme'],
            'admin_themes_enable' => ['/improve/design/themes/prestashop_theme/enable', 'AdminThemes', 'enableTheme', ['theme_name' => 'prestashop_theme']],
            'admin_themes_delete' => ['/improve/design/themes/prestashop_theme/delete', 'AdminThemes', 'deleteTheme', ['theme_name' => 'prestashop_theme']],
            'admin_themes_adapt_to_rtl_languages' => ['/improve/design/themes/adapt-to-rtl-languages', 'AdminThemes', 'submitGenerateRTL'],
            'admin_theme_customize_layouts' => ['/improve/design/themes/customize-layouts', 'AdminThemes', 'submitConfigureLayouts'],
            'admin_themes_reset_layouts' => ['/improve/design/themes/prestashop_theme/reset-layouts', 'AdminThemes', 'resetToDefaults', ['theme_name' => 'prestashop_theme']],

            'admin_attachments_index' => ['/sell/attachments/', 'AdminAttachments'],
            'admin_attachments_filter' => ['/sell/attachments/', 'AdminAttachments', 'submitFilterattachment'],
            'admin_attachments_create' => ['/sell/attachments/new', 'AdminAttachments', 'addattachment'],
            'admin_attachments_edit' => ['/sell/attachments/42/edit', 'AdminAttachments', 'updateattachment', ['id_attachment' => 42]],
            'admin_attachments_view' => ['/sell/attachments/42/view', 'AdminAttachments', 'viewattachment', ['id_attachment' => 42]],
            'admin_attachments_delete_bulk' => ['/sell/attachments/delete-bulk', 'AdminAttachments', 'submitBulkdeleteattachment'],
            'admin_attachments_delete' => ['/sell/attachments/42/delete', 'AdminAttachments', 'deleteattachment', ['id_attachment' => 42]],
        ];
    }

    /**
     * @return array<int, array<string|array<string, string>>>
     */
    public static function getLegacyControllers(): array
    {
        return [
            ['/admin-dev/index.php?controller=AdminLogin', 'AdminLogin'],
            ['/admin-dev/index.php?controller=AdminModulesPositions&addToHook=', 'AdminModulesPositions', ['addToHook' => '']],
            ['/admin-dev/index.php?controller=AdminModules', 'AdminModules'],
            ['/admin-dev/index.php?controller=AdminModules&configure=ps_linklist', 'AdminModules', ['configure' => 'ps_linklist']],
        ];
    }

    public function testServiceExists(): void
    {
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');
        $this->assertInstanceOf(LegacyUrlConverter::class, $converter);
    }

    /**
     * @dataProvider getMigratedControllers
     *
     * @param string $expectedUrl
     * @param string $controller
     * @param string|null $action
     * @param array|null $params
     *
     * @return void
     */
    public function testConverterByParameters(
        string $expectedUrl,
        string $controller,
        string $action = null,
        array $params = null
    ): void {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');

        $caughtException = null;
        $caughtExceptionMessage = '';

        try {
            $parameters = [
                'controller' => $controller,
                'action' => $action,
            ];
            if (null !== $params) {
                $parameters = array_merge($parameters, $params);
            }
            $convertedUrl = $converter->convertByParameters($parameters);
        } catch (\Exception $e) {
            $caughtException = $e;
            $caughtExceptionMessage = sprintf('Unexpected exception %s: %s', get_class($e), $e->getMessage());
            $convertedUrl = null;
        }
        $this->assertNull($caughtException, $caughtExceptionMessage);
        $this->assertSameUrl($expectedUrl, $convertedUrl);
    }

    public function testTabParameter(): void
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');
        $convertedUrl = $converter->convertByParameters(['tab' => 'AdminCustomers']);
        $this->assertSameUrl('/sell/customers/', $convertedUrl);

        $convertedUrl = $converter->convertByParameters(
            [
                'tab' => 'AdminCustomers',
                'controller' => 'admincustomers',
                'id_customer' => 42,
                'viewcustomer' => '',
            ]
        );
        $this->assertSameUrl('/sell/customers/42/view', $convertedUrl);

        $legacyUrl = $this->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/index.php?tab=AdminCustomers&id_customer=42&viewcustomer&token=932d64a68d64faff8f692d84fc0e1d89';
        $convertedUrl = $converter->convertByUrl($legacyUrl);
        $this->assertSameUrl('/sell/customers/42/view', $convertedUrl);

        $legacyUrl = $this->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/index.php?tab=AdminCustomers&controller=admincustomers&id_customer=42&viewcustomer&token=932d64a68d64faff8f692d84fc0e1d89';
        $convertedUrl = $converter->convertByUrl($legacyUrl);
        $this->assertSameUrl('/sell/customers/42/view', $convertedUrl);
    }

    public function testInsensitiveControllersAndActions(): void
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');
        $convertedUrl = $converter->convertByParameters(['controller' => 'admincustomers']);
        $this->assertSameUrl('/sell/customers/', $convertedUrl);

        $convertedUrl = $converter->convertByParameters(['controller' => 'AdminCustomers', 'VIEWCUSTOMER' => '1', 'id_customer' => 42]);
        $this->assertSameUrl('/sell/customers/42/view', $convertedUrl);
    }

    public function testIdEqualToOne(): void
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');

        $legacyUrl = $this->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . \Dispatcher::getInstance()->createUrl('AdminMeta') . '&id_meta=1&conf=4';
        $convertedUrl = $converter->convertByUrl($legacyUrl);
        $this->assertSameUrl('/configure/shop/seo-urls/?id_meta=1&conf=4', $convertedUrl);
    }

    public function testAlreadyConverted(): void
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');

        $convertedUrl = $converter->convertByParameters(['controller' => 'AdminAdminPreferences']);
        $convertedUrl .= '&controller=AdminAdminPreferences';
        $caughtException = null;
        try {
            $converter->convertByUrl($convertedUrl);
        } catch (AlreadyConvertedException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertTrue($convertedUrl . ' is already a converted url' == $caughtException->getMessage());
    }

    /**
     * @dataProvider getMigratedControllers
     *
     * @param string $expectedUrl
     * @param string $controller
     * @param string|null $action
     * @param array|null $params
     *
     * @return void
     */
    public function testLegacyLinkClass(
        string $expectedUrl,
        string $controller,
        string $action = null,
        array $params = null
    ): void {
        $parameters = [
            'action' => $action,
        ];
        if (null !== $params) {
            $parameters = array_merge($parameters, $params);
        }
        $linkUrl = $this->link->getAdminLink($controller, true, [], $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    /**
     * @dataProvider getMigratedControllers
     */
    public function testLegacyClassParameterAction(
        string $expectedUrl,
        string $controller,
        string $action = null,
        array $params = null
    ): void {
        $parameters = null !== $params ? $params : [];
        if (null != $action) {
            $parameters[$action] = '';
        }
        $linkUrl = $this->link->getAdminLink($controller, true, [], $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    /**
     * Mainly used to ensure the legacy links are not broken.
     *
     * @dataProvider getLegacyControllers
     *
     * @param string $expectedUrl
     * @param string $controller
     * @param array|null $parameters
     *
     * @return void
     *
     * @throws \PrestaShopException
     * @throws \ReflectionException
     */
    public function testLegacyControllers(string $expectedUrl, string $controller, array $parameters = null)
    {
        $parameters = null === $parameters ? [] : $parameters;
        $linkUrl = $this->link->getAdminLink($controller, true, [], $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    public function testRedirectionListener(): void
    {
        $legacyUrl = $this->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . \Dispatcher::getInstance()->createUrl('AdminAdminPreferences');
        $this->client->request('GET', $legacyUrl);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());
        $location = $response->headers->get('location');
        $this->assertSameUrl('/configure/advanced/administration/', $location);
    }

    public function testRedirectionListenerWithoutLoop(): void
    {
        $legacyUrl = $this->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . \Dispatcher::getInstance()->createUrl('AdminAdminPreferences');
        $this->client->request('GET', $legacyUrl);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());
        $location = $response->headers->get('location');

        $this->client->request('GET', $location . '&controller=AdminAdminPreferences');
        $response = $this->client->getResponse();
        $this->assertFalse($response->isRedirection());
    }

    public function testNoRedirectionListener(): void
    {
        $legacyUrl = $this->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . \Dispatcher::getInstance()->createUrl('AdminUnkown');
        $this->client->request('GET', $legacyUrl);
        $response = $this->client->getResponse();
        $this->assertFalse($response->isRedirection());
    }

    public function testPostParameters()
    {
        $legacyUrl = $this->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . \Dispatcher::getInstance()->createUrl('AdminModulesPositions');
        $this->client->request('POST', $legacyUrl, ['submitAddToHook' => '']);
        $response = $this->client->getResponse();
        $this->assertFalse($response->isRedirection());
        $this->assertNull($response->headers->get('location'));
    }

    /**
     * @param string $expectedUrl
     * @param string $url
     */
    private function assertSameUrl(string $expectedUrl, string $url)
    {
        $cleanUrl = $this->getCleanUrl($url);
        $this->assertTrue($expectedUrl == $cleanUrl, sprintf(
            'Expected url %s is different with generated one: %s',
            $expectedUrl,
            $cleanUrl
        ));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function getCleanUrl(string $url): string
    {
        $this->assertNotNull($url);
        $parsedUrl = parse_url($url);
        $parameters = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parameters);
        }

        unset(
            $parameters['token'],
            $parameters['_token']
        );

        $cleanUrl = http_build_url([
            'path' => $parsedUrl['path'],
            'query' => http_build_query($parameters),
        ]);

        $this->assertNotEmpty($parsedUrl['path']);

        return $cleanUrl;
    }
}
