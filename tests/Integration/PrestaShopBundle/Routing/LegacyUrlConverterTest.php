<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Routing;

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Routing\LegacyUrlConverter;
use Tests\Integration\PrestaShopBundle\Test\LightWebTestCase;
use Link;
use ReflectionClass;

/**
 * @group demo
 */
class LegacyUrlConverterTest extends LightWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->initContainerInstance();
    }

    /**
     * @return array
     */
    public static function getMigratedControllers()
    {
        return [
            'admin_administration' => ['/configure/advanced/administration/', 'AdminAdminPreferences'],
            'admin_administration_save' => ['/configure/advanced/administration/', 'AdminAdminPreferences', 'update'],

            'admin_backup' => ['/configure/advanced/backup/', 'AdminBackup'],
            'admin_backup_process' => ['/configure/advanced/backup/', 'AdminBackup', 'update'],
            'admin_backup_create' => ['/configure/advanced/backup/create', 'AdminBackup', 'addbackup'],
            'admin_backup_delete' => ['/configure/advanced/backup/backup_file.zip', 'AdminBackup', 'delete', ['filename' => 'backup_file.zip']],
            'admin_backup_bulk_delete' => ['/configure/advanced/backup/bulk-delete/', 'AdminBackup', 'submitBulkdeletebackup'],

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

            'admin_order_delivery_slip' => ['/sell/orders/delivery-slips/', 'AdminDeliverySlip'],
            'admin_order_delivery_slip_pdf' => ['/sell/orders/delivery-slips/pdf', 'AdminDeliverySlip', 'submitAdddelivery'],

            'admin_import' => ['/configure/advanced/import/', 'AdminImport'],
            'admin_import_file_upload' => ['/configure/advanced/import/file/upload', 'AdminImport', 'uploadCsv'],
            'admin_import_file_delete' => ['/configure/advanced/import/file/delete', 'AdminImport', 'delete'],
            'admin_import_file_download' => ['/configure/advanced/import/file/download', 'AdminImport', 'download'],
            'admin_import_sample_download' => ['/configure/advanced/import/sample/download/categories_import', 'AdminImport', 'sampleDownload', ['sampleName' => 'categories_import']],

            'admin_system_information' => ['/configure/advanced/system-information/', 'AdminInformation'],
            'admin_system_information_check_files' => ['/configure/advanced/system-information/files', 'AdminInformation', 'checkFiles'],

            'admin_logs' => ['/configure/advanced/logs/', 'AdminLogs'],
            'admin_logs_save' => ['/configure/advanced/logs/settings', 'AdminLogs', 'update'],
            'admin_logs_delete' => ['/configure/advanced/logs/erase-all', 'AdminLogs', 'deletelog'],

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

            'admin_localization_show_settings' => ['/improve/international/localization/', 'AdminLocalization'],
            'admin_localization_save_settings' => ['/improve/international/localization/', 'AdminLocalization', 'update'],
            'admin_localization_import_pack' => ['/improve/international/localization/import_pack', 'AdminLocalization', 'submitLocalizationPack'],

            'admin_geolocation' => ['/improve/international/geolocation/', 'AdminGeolocation'],
            'admin_geolocation_process_form' => ['/improve/international/geolocation/process_form', 'AdminGeolocation', 'update'],

            'admin_payment_preferences' => ['/improve/payment/preferences', 'AdminPaymentPreferences'],
            'admin_payment_preferences_process' => ['/improve/payment/preferences', 'AdminPaymentPreferences', 'update'],

            'admin_order_invoices' => ['/sell/orders/invoices/', 'AdminInvoices'],
            'admin_order_invoices_process' => ['/sell/orders/invoices/', 'AdminInvoices', 'update'],
            'admin_order_invoices_generate_by_date' => ['/sell/orders/invoices/by_date', 'AdminInvoices', 'submitAddinvoice_date'],
            'admin_order_invoices_generate_by_status' => ['/sell/orders/invoices/by_status', 'AdminInvoices', 'submitAddinvoice_status'],

            'admin_email' => ['/configure/advanced/emails/', 'AdminEmails'],
            'admin_email_search' => ['/configure/advanced/emails/', 'AdminEmails', 'search'],
            'admin_email_process' => ['/configure/advanced/emails/settings', 'AdminEmails', 'update'],
            'admin_send_test_email' => ['/configure/advanced/emails/send-testing-email', 'AdminEmails', 'testEmail'],
            'admin_delete_selected_email_logs' => ['/configure/advanced/emails/delete/selected', 'AdminEmails', 'submitBulkdeletemail'],
            'admin_delete_all_email_logs' => ['/configure/advanced/emails/delete/all', 'AdminEmails', 'deleteAll'],
            'admin_delete_single_email_log' => ['/configure/advanced/emails/delete/42', 'AdminEmails', 'deletemail', ['id_mail' => 42]],

            'admin_meta' => ['/configure/shop/seo-urls/', 'AdminMeta'],
            'admin_meta_search' => ['/configure/shop/seo-urls/', 'AdminMeta', 'search'],
            'admin_meta_list_create' => ['/configure/shop/seo-urls/create', 'AdminMeta', 'addmeta'],
            'admin_meta_list_edit' => ['/configure/shop/seo-urls/edit/42', 'AdminMeta', 'updatemeta', ['id_meta' => 42]],
            'admin_meta_list_delete_single' => ['/configure/shop/seo-urls/delete/42', 'AdminMeta', 'deletemeta', ['id_meta' => 42]],
            'admin_meta_list_delete_multiple' => ['/configure/shop/seo-urls/delete', 'AdminMeta', 'submitBulkdeletmeta'],
            'admin_meta_process' => ['/configure/shop/seo-urls/settings', 'AdminMeta', 'update'],
            'admin_meta_generate_robots_file' => ['/configure/shop/seo-urls/generate/robots', 'AdminMeta', 'submitRobots'],

            //This url is not ready to be migrated yet, the SF controller redirects to the legacy url
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
        ];
    }

    /**
     * @return array
     */
    public static function getLegacyControllers()
    {
        return [
            ['/admin-dev/index.php?controller=AdminLogin', 'AdminLogin'],
            ['/admin-dev/index.php?controller=AdminModulesPositions&addToHook=', 'AdminModulesPositions', ['addToHook' => '']],
            ['/admin-dev/index.php?controller=AdminModules', 'AdminModules'],
            ['/admin-dev/index.php?controller=AdminModules&configure=ps_linklist', 'AdminModules', ['configure' => 'ps_linklist']],
        ];
    }

    public function testServiceExists()
    {
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.legacy_url_converter');
        $this->assertInstanceOf(LegacyUrlConverter::class, $converter);
    }

    public function testLegacyWithRoute()
    {
        $link = new Link();
        $routeUrl = $link->getAdminLink("AdminModulesCatalog", true, ['route' => "admin_module_catalog_post"]);
        $this->assertSameUrl('/improve/modules/catalog/recommended', $routeUrl, ['route']);
    }

    /**
     * @dataProvider migratedControllers
     * @param string $expectedUrl
     * @param string $controller
     * @param string|null $action
     * @param array|null $queryParameters
     */
    public function testConverterByParameters($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.legacy_url_converter');

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

    /**
     * @dataProvider migratedControllers
     */
    public function testLegacyLinkClass($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        $link = new Link();

        $parameters = [
            'action' => $action,
        ];
        if (null !== $queryParameters) {
            $parameters = array_merge($parameters, $queryParameters);
        }
        $linkUrl = $link->getAdminLink($controller, true, [], $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    /**
     * @dataProvider migratedControllers
     */
    public function testLegacyClassParameterAction($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        $link = new Link();

        $parameters = null !== $queryParameters ? $queryParameters : [];
        if (null != $action) {
            $parameters[$action] = '';
        }
        $linkUrl = $link->getAdminLink($controller, true, [], $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    /**
     * Mainly used to ensure the legacy links are not broken.
     * @dataProvider legacyControllers
     * @param string $expectedUrl
     * @param string $controller
     * @param array|null $parameters
     * @throws \PrestaShopException
     * @throws \ReflectionException
     */
    public function testLegacyControllers($expectedUrl, $controller, array $parameters = null)
    {
        $link = new Link();

        $parameters = null === $parameters ? [] : $parameters;
        $linkUrl = $link->getAdminLink($controller, true, [], $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    /**
     * @dataProvider migratedControllers
     * @param string $expectedUrl
     * @param string $controller
     * @param string|null $action
     * @param array|null $queryParameters
     * @throws \PrestaShopException
     */
    public function testRedirectionListener($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        $link = new Link();
        $params = [];
        if (null !== $action) {
            $params['action'] = $action;
        }
        if (null !== $queryParameters) {
            $params = array_merge($params, $queryParameters);
        }

        $legacyUrl = $link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' .  \Dispatcher::getInstance()->createUrl($controller, null, $params);
        $this->client->request('GET', $legacyUrl);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());
        $location = $response->headers->get('location');
        $this->assertSameUrl($expectedUrl, $location);
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
        $this->assertNotNull($url);
        $parsedUrl = parse_url($url);
        $parameters = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parameters);
        }

        unset($parameters['token']);
        unset($parameters['_token']);
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
        $this->assertTrue($expectedUrl == $cleanUrl, sprintf(
            'Expected url %s is different with generated one: %s',
            $expectedUrl,
            $cleanUrl
        ));
    }

    /**
     * Force the static property SymfonyContainer::instance so that the Link class
     * has access to the router
     * @throws \ReflectionException
     */
    private function initContainerInstance()
    {
        $reflectedClass = new ReflectionClass(SymfonyContainer::class);
        $instanceProperty = $reflectedClass->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(self::$kernel->getContainer());
    }
}
