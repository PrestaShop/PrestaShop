<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Routing\Converter;

use Link;
use PrestaShopBundle\Routing\Converter\Exception\AlreadyConvertedException;
use PrestaShopBundle\Routing\Converter\LegacyUrlConverter;
use Tests\TestCase\SymfonyIntegrationTestCase;

class LegacyUrlConverterTest extends SymfonyIntegrationTestCase
{
    /** @var Link */
    private $link;

    protected function setUp()
    {
        parent::setUp();
        if (!$this->link) {
            $this->link = new Link();
        }
    }

    /**
     * @return array
     */
    public static function getMigratedControllers()
    {
        return [
            'admin_administration' => ['/configure/advanced/administration/', 'AdminAdminPreferences'],
            'admin_administration_save' => ['/configure/advanced/administration/', 'AdminAdminPreferences', 'update'],

            'admin_backups' => ['/configure/advanced/backups/', 'AdminBackup'],
            'admin_backups_save_options' => ['/configure/advanced/backups/', 'AdminBackup', 'update'],
            'admin_backups_create' => ['/configure/advanced/backups/new', 'AdminBackup', 'addbackup'],
            'admin_backups_delete' => ['/configure/advanced/backups/backup_file.zip', 'AdminBackup', 'delete', ['filename' => 'backup_file.zip']],
            'admin_backups_bulk_delete' => ['/configure/advanced/backups/bulk-delete/', 'AdminBackup', 'submitBulkdeletebackup'],

            'admin_module_catalog' => ['/improve/modules/catalog', 'AdminModulesCatalog'],
            'admin_module_catalog_refresh' => ['/improve/modules/catalog/refresh', 'AdminModulesCatalog', 'refresh'],
            'admin_module_catalog_post' => ['/improve/modules/catalog/recommended', 'AdminModulesCatalog', 'recommended'],

            'admin_module_manage' => ['/improve/modules/manage', 'AdminModulesManage'],
            'admin_module_manage_alias' => ['/improve/modules/manage', 'AdminModulesSf'],

            'admin_module_notification' => ['/improve/modules/alerts', 'AdminModulesNotifications'],
            'admin_module_notification_count' => ['/improve/modules/alerts/count', 'AdminModulesNotifications', 'count'],

            'admin_module_updates' => ['/improve/modules/updates', 'AdminModulesUpdates'],

            'admin_module_addons_store' => ['/improve/modules/addons-store', 'AdminAddonsCatalog'],

            'admin_modules_positions' => ['/improve/design/modules/positions/', 'AdminModulesPositions'],
            'admin_modules_positions_unhook' => ['/improve/design/modules/positions/unhook', 'AdminModulesPositions', 'unhook'],

            'admin_customer_preferences' => ['/configure/shop/customer-preferences/', 'AdminCustomerPreferences'],
            'admin_customer_preferences_process' => ['/configure/shop/customer-preferences/', 'AdminCustomerPreferences', 'update'],

            'admin_customers_index' => ['/sell/customers/', 'AdminCustomers'],
            'admin_customers_filter' => ['/sell/customers/', 'AdminCustomers', 'submitFiltercustomer'],
            'admin_customers_create' => ['/sell/customers/new', 'AdminCustomers', 'addcustomer'],
            'admin_customers_edit' => ['/sell/customers/42/edit', 'AdminCustomers', 'updatecustomer', ['id_customer' => 42]],
            'admin_customers_view' => ['/sell/customers/42/view', 'AdminCustomers', 'viewcustomer', ['id_customer' => 42]],
            'admin_customers_save_private_note' => ['/sell/customers/42/save-private-note', 'AdminCustomers', 'updateCustomerNote', ['id_customer' => 42]],
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
            'admin_order_preferences_save' => ['/configure/shop/order-preferences/', 'AdminOrderPreferences', 'update'],

            'admin_product_preferences' => ['/configure/shop/product-preferences/', 'AdminPPreferences'],
            'admin_product_preferences_process' => ['/configure/shop/product-preferences/', 'AdminPPreferences', 'update'],

            'admin_performance' => ['/configure/advanced/performance/', 'AdminPerformance'],
            'admin_performance_save' => ['/configure/advanced/performance/', 'AdminPerformance', 'update'],
            'admin_clear_cache' => ['/configure/advanced/performance/clear-cache', 'AdminPerformance', 'empty_smarty_cache'],
            'admin_servers_add' => ['/configure/advanced/performance/memcache/servers', 'AdminPerformance', 'submitAddServer'],
            'admin_servers_delete' => ['/configure/advanced/performance/memcache/servers', 'AdminPerformance', 'deleteMemcachedServer'],

            'admin_preferences' => ['/configure/shop/preferences/preferences', 'AdminPreferences'],
            'admin_preferences_save' => ['/configure/shop/preferences/preferences', 'AdminPreferences', 'update'],

            'admin_shipping_preferences' => ['/improve/shipping/preferences', 'AdminShipping'],
            'admin_shipping_preferences_save' => ['/improve/shipping/preferences', 'AdminShipping', 'update'],

            'admin_stock_overview' => ['/sell/stocks/', 'AdminStockManagement'],

            'admin_theme_catalog' => ['/improve/design/themes-catalog/', 'AdminThemesCatalog'],

            'admin_international_translation_overview' => ['/improve/international/translations/', 'AdminTranslationSf'],

            'admin_payment_methods' => ['/improve/payment/payment_methods', 'AdminPayment'],

            'admin_localization_index' => ['/improve/international/localization/', 'AdminLocalization'],
            'admin_localization_save_options' => ['/improve/international/localization/options', 'AdminLocalization', 'update'],
            'admin_localization_import_pack' => ['/improve/international/localization/import-pack', 'AdminLocalization', 'submitLocalizationPack'],

            'admin_geolocation_index' => ['/improve/international/geolocation/', 'AdminGeolocation'],
            'admin_geolocation_save_options' => ['/improve/international/geolocation/process_form', 'AdminGeolocation', 'update'],

            'admin_payment_preferences' => ['/improve/payment/preferences', 'AdminPaymentPreferences'],
            'admin_payment_preferences_process' => ['/improve/payment/preferences', 'AdminPaymentPreferences', 'update'],

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
            'admin_metas_save_options' => ['/configure/shop/seo-urls/options', 'AdminMeta', 'submitOptionsmeta'],
            'admin_metas_generate_robots_text_file' => ['/configure/shop/seo-urls/generate/robots', 'AdminMeta', 'submitRobots'],

            //'admin_module_configure_action' => ['/improve/modules/manage/action/configure/ps_linklist', 'AdminModules', 'configure', ['module_name' => 'ps_linklist']],
            //'admin_module_configure_action_legacy' => ['/improve/modules/manage/action/configure/ps_linklist', 'AdminModules', 'configure', ['configure' => 'ps_linklist']],

            /*'admin_sql_request' => ['/configure/advanced/request-sql/', 'AdminRequestSql'],
            'admin_sql_request_search' => ['/configure/advanced/request-sql/', 'AdminRequestSql', 'search'],
            'admin_sql_request_process' => ['/configure/advanced/request-sql/settings', 'AdminRequestSql', 'update'],
            'admin_sql_request_create' => ['/configure/advanced/request-sql/new', 'AdminRequestSql', 'addrequest_sql'],
            'admin_sql_request_edit' => ['/configure/advanced/request-sql/edit/42', 'AdminRequestSql', 'updaterequest_sql', ['id_request_sql' => 42]],
            'admin_sql_request_delete' => ['/configure/advanced/request-sql/delete/42', 'AdminRequestSql', 'deleterequest_sql', ['id_request_sql' => 42]],
            'admin_sql_request_delete_bulk' => ['/configure/advanced/request-sql/delete/bulk', 'AdminRequestSql', 'submitBulkdeleterequest_sql'],
            'admin_sql_request_table_columns' => ['/configure/advanced/request-sql/tables/plop/columns', 'AdminRequestSql', 'ajax', ['table' => 'plop']],
            'admin_sql_request_view' => ['/configure/advanced/request-sql/view/42', 'AdminRequestSql', 'viewsql_request', ['id_request_sql' => 42]],
            'admin_sql_request_export' => ['/configure/advanced/request-sql/export/42', 'AdminRequestSql', 'exportsql_request', ['id_request_sql' => 42]],*/

            /*'admin_webservice' => ['/configure/advanced/webservice/', 'AdminWebservice'],
            'admin_webservice_search' => ['/configure/advanced/webservice/', 'AdminWebservice', 'search'],
            'admin_webservice_settings_save' => ['/configure/advanced/webservice/settings', 'AdminWebservice', 'update'],
            'admin_webservice_list_create' => ['/configure/advanced/webservice/create', 'AdminWebservice', 'addwebservice_account'],
            'admin_webservice_list_edit' => ['/configure/advanced/webservice/settings', 'AdminWebservice', 'update'],
            'admin_delete_single_webservice_log' => ['/configure/advanced/webservice/delete/42', 'AdminWebservice', 'deletewebservice_account', ['id_webservice_account' => 42]],
            'admin_delete_multiple_webservice_log' => ['/configure/advanced/webservice/delete', 'AdminWebservice', 'submitBulkdeletewebservice_account'],
            'admin_webservice_status_toggle' => ['/configure/advanced/webservice/status/42', 'AdminWebservice', 'status', ['id_webservice_account' => 42]],
            'admin_webservice_bulk_enable' => ['/configure/advanced/webservice/status/bulk/enable', 'AdminWebservice', 'submitBulkenableSelectionwebservice_account'],
            'admin_webservice_bulk_disable' => ['/configure/advanced/webservice/status/bulk/disable', 'AdminWebservice', 'submitBulkdisableSelectionwebservice_account'],*/

            'admin_profiles_index' => ['/configure/advanced/profiles/', 'AdminProfiles'],
            'admin_profiles_search' => ['/configure/advanced/profiles/', 'AdminProfiles', 'submitFilterprofile'],
            'admin_profiles_create' => ['/configure/advanced/profiles/new', 'AdminProfiles', 'addprofile'],
            'admin_profiles_edit' => ['/configure/advanced/profiles/1000/edit', 'AdminProfiles', 'updateprofile', ['id_profile' => 1000]],
            'admin_profiles_bulk_delete' => ['/configure/advanced/profiles/delete/bulk', 'AdminProfiles', 'submitBulkdeleteprofile'],
            'admin_profiles_delete' => ['/configure/advanced/profiles/12/delete', 'AdminProfiles', 'deleteprofile', ['id_profile' => 12]],

            'admin_currencies_index' => ['/improve/international/currencies/', 'AdminCurrencies'],
            'admin_currencies_search' => ['/improve/international/currencies/', 'AdminCurrencies', 'submitFiltercurrency'],
            'admin_currencies_create' => ['/improve/international/currencies/new', 'AdminCurrencies', 'addcurrency'],
            'admin_currencies_edit' => ['/improve/international/currencies/1000/edit', 'AdminCurrencies', 'updatecurrency', ['id_currency' => 1000]],
            'admin_currencies_delete' => ['/improve/international/currencies/1000/delete', 'AdminCurrencies', 'deletecurrency', ['id_currency' => 1000]],
            'admin_currencies_toggle_status' => ['/improve/international/currencies/1000/toggle-status', 'AdminCurrencies', 'statuscurrency', ['id_currency' => 1000]],
            'admin_currencies_refresh_exchange_rates' => ['/improve/international/currencies/refresh-exchange-rates', 'AdminCurrencies', 'SubmitExchangesRates'],

            'admin_employees_index' => ['/configure/advanced/employees/', 'AdminEmployees'],
            'admin_employees_search' => ['/configure/advanced/employees/', 'AdminEmployees', 'submitFilteremployee'],
            'admin_employees_save_options' => ['/configure/advanced/employees/save-options', 'AdminEmployees', 'submitOptionsemployee'],
            'admin_employees_toggle_status' => ['/configure/advanced/employees/1000/toggle-status', 'AdminEmployees', 'statusemployee', ['id_employee' => 1000]],
            'admin_employees_bulk_enable_status' => ['/configure/advanced/employees/bulk-enable-status', 'AdminEmployees', 'submitBulkenableSelectionemployee'],
            'admin_employees_bulk_disable_status' => ['/configure/advanced/employees/bulk-disable-status', 'AdminEmployees', 'submitBulkdisableSelectionemployee'],
            'admin_employees_delete' => ['/configure/advanced/employees/1000/delete', 'AdminEmployees', 'deleteemployee', ['id_employee' => 1000]],
            'admin_employees_bulk_delete' => ['/configure/advanced/employees/bulk-delete', 'AdminEmployees', 'submitBulkdeleteemployee'],
            'admin_employees_create' => ['/configure/advanced/employees/new', 'AdminEmployees', 'addemployee'],
            'admin_employees_edit' => ['/configure/advanced/employees/1000/edit', 'AdminEmployees', 'updateemployee', ['id_employee' => 1000]],

            'admin_international_translations_export_theme' => ['/improve/international/translations/export', 'AdminTranslations', 'submitExport'],
            'admin_international_translations_add_update_language' => ['/improve/international/translations/add-update-language', 'AdminTranslations', 'submitAddLanguage'],
            'admin_international_translations_copy_language' => ['/improve/international/translations/copy', 'AdminTranslations', 'submitCopyLang'],
        ];
    }

    /**
     * @return array
     */
    public static function getLegacyControllers()
    {
        return [
            ['/Integration/index.php?controller=AdminLogin', 'AdminLogin'],
            ['/Integration/index.php?controller=AdminModulesPositions&addToHook=', 'AdminModulesPositions', ['addToHook' => '']],
            ['/Integration/index.php?controller=AdminModules', 'AdminModules'],
            ['/Integration/index.php?controller=AdminModules&configure=ps_linklist', 'AdminModules', ['configure' => 'ps_linklist']],
        ];
    }

    public function testServiceExists()
    {
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');
        $this->assertInstanceOf(LegacyUrlConverter::class, $converter);
    }

    public function testLegacyWithRoute()
    {
        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, ['route' => 'admin_module_catalog_post']);
        $this->assertSameUrl('/improve/modules/catalog/recommended', $routeUrl, ['route']);
    }

    public function testDifferentLinkArguments()
    {
        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog');
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', false);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, []);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, null);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, [], []);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, [], null);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);
    }

    /**
     * Looping manually uses MUCH less memory than dataProvider
     */
    public function testConverterByParameters()
    {
        $migratedControllers = $this->getMigratedControllers();
        foreach ($migratedControllers as $migratedController) {
            $expectedUrl = $migratedController[0];
            $controller = $migratedController[1];
            $action = isset($migratedController[2]) ? $migratedController[2] : null;
            $params = isset($migratedController[3]) ? $migratedController[3] : null;
            $this->dotestConverterByParameters($expectedUrl, $controller, $action, $params);
        }
    }

    /**
     * @param string $expectedUrl
     * @param string $controller
     * @param string|null $action
     * @param array|null $queryParameters
     */
    private function doTestConverterByParameters($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');

        $caughtException = null;
        $caughtExceptionMessage = '';

        try {
            $parameters = [
                'controller' => $controller,
                'action' => $action,
            ];
            if (null !== $queryParameters) {
                $parameters = array_merge($parameters, $queryParameters);
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

    public function testTabParameter()
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

    public function testInsensitiveControllersAndActions()
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');
        $convertedUrl = $converter->convertByParameters(['controller' => 'admincustomers']);
        $this->assertSameUrl('/sell/customers/', $convertedUrl);

        $convertedUrl = $converter->convertByParameters(['controller' => 'AdminCustomers', 'VIEWCUSTOMER' => '1', 'id_customer' => 42]);
        $this->assertSameUrl('/sell/customers/42/view', $convertedUrl);
    }

    public function testIdEqualToOne()
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');

        $legacyUrl = $this->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . \Dispatcher::getInstance()->createUrl('AdminMeta') . '&id_meta=1&conf=4';
        $convertedUrl = $converter->convertByUrl($legacyUrl);
        $this->assertSameUrl('/configure/shop/seo-urls/?id_meta=1&conf=4', $convertedUrl);
    }

    public function testAlreadyConverted()
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

    public function testLegacyLinkClass()
    {
        $migratedControllers = $this->getMigratedControllers();
        foreach ($migratedControllers as $migratedController) {
            $expectedUrl = $migratedController[0];
            $controller = $migratedController[1];
            $action = isset($migratedController[2]) ? $migratedController[2] : null;
            $params = isset($migratedController[3]) ? $migratedController[3] : null;
            $this->doTestLegacyLinkClass($expectedUrl, $controller, $action, $params);
        }
    }

    /**
     * @param string $expectedUrl
     * @param string $controller
     * @param string|null $action
     * @param array|null $queryParameters
     */
    private function doTestLegacyLinkClass($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        $parameters = [
            'action' => $action,
        ];
        if (null !== $queryParameters) {
            $parameters = array_merge($parameters, $queryParameters);
        }
        $linkUrl = $this->link->getAdminLink($controller, true, [], $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    public function testLegacyClassParameterAction()
    {
        $migratedControllers = $this->getMigratedControllers();
        foreach ($migratedControllers as $migratedController) {
            $expectedUrl = $migratedController[0];
            $controller = $migratedController[1];
            $action = isset($migratedController[2]) ? $migratedController[2] : null;
            $params = isset($migratedController[3]) ? $migratedController[3] : null;
            $this->doTestLegacyClassParameterAction($expectedUrl, $controller, $action, $params);
        }
    }

    /**
     * @param string $expectedUrl
     * @param string $controller
     * @param string|null $action
     * @param array|null $queryParameters
     */
    private function doTestLegacyClassParameterAction($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        $parameters = null !== $queryParameters ? $queryParameters : [];
        if (null != $action) {
            $parameters[$action] = '';
        }
        $linkUrl = $this->link->getAdminLink($controller, true, [], $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    public function testLegacyControllers()
    {
        $legacyControllers = $this->getLegacyControllers();
        foreach ($legacyControllers as $legacyController) {
            $expectedUrl = $legacyController[0];
            $controller = $legacyController[1];
            $action = isset($legacyController[2]) ? $legacyController[2] : null;
            $this->doTestLegacyControllers($expectedUrl, $controller, $action);
        }
    }

    /**
     * Mainly used to ensure the legacy links are not broken.
     *
     * @param string $expectedUrl
     * @param string $controller
     * @param array|null $parameters
     *
     * @throws \PrestaShopException
     * @throws \ReflectionException
     */
    public function doTestLegacyControllers($expectedUrl, $controller, array $parameters = null)
    {
        $parameters = null === $parameters ? [] : $parameters;
        $linkUrl = $this->link->getAdminLink($controller, true, [], $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    public function testRedirectionListener()
    {
        $legacyUrl = $this->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . \Dispatcher::getInstance()->createUrl('AdminAdminPreferences');
        $this->client->request('GET', $legacyUrl);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());
        $location = $response->headers->get('location');
        $this->assertSameUrl('/configure/advanced/administration/', $location);
    }

    public function testRedirectionListenerWithoutLoop()
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

    public function testNoRedirectionListener()
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
     * @return array
     */
    public function migratedControllers()
    {
        return self::getMigratedControllers();
    }

    /**
     * @return array
     */
    public function legacyControllers()
    {
        return self::getLegacyControllers();
    }

    /**
     * @param string $expectedUrl
     * @param string $url
     * @param array|null $ignoredParameters
     */
    private function assertSameUrl($expectedUrl, $url, array $ignoredParameters = null)
    {
        $cleanUrl = $this->getCleanUrl($url, $ignoredParameters);
        $this->assertTrue($expectedUrl == $cleanUrl, sprintf(
            'Expected url %s is different with generated one: %s',
            $expectedUrl,
            $cleanUrl
        ));
    }

    /**
     * @param $url
     * @param array|null $ignoredParameters
     *
     * @return string
     */
    private function getCleanUrl($url, array $ignoredParameters = null)
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

        if (null !== $ignoredParameters) {
            foreach ($ignoredParameters as $ignoredParameter) {
                unset($parameters[$ignoredParameter]);
            }
        }

        $cleanUrl = http_build_url([
            'path' => $parsedUrl['path'],
            'query' => http_build_query($parameters),
        ]);

        $this->assertNotEmpty($parsedUrl['path']);

        return $cleanUrl;
    }
}
