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

namespace LegacyTests\Integration\PrestaShopBundle\Routing\Converter;

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Routing\Converter\LegacyUrlConverter;
use LegacyTests\Integration\PrestaShopBundle\Test\LightWebTestCase;
use Link;
use ReflectionClass;

/**
 * @group routing
 */
class LegacyUrlConverterTest extends LightWebTestCase
{
    /** @var Link */
    private $link;

    public function setUp()
    {
        parent::setUp();
        $this->initContainerInstance();
        if (!$this->link) {
            $this->link = new Link();
        }
    }

    /**
     * @return array
     */
    public static function getMigratedControllers()
    {
        return array(
            'admin_administration' => array('/configure/advanced/administration/', 'AdminAdminPreferences'),
            'admin_administration_save' => array('/configure/advanced/administration/', 'AdminAdminPreferences', 'update'),

            'admin_backups' => array('/configure/advanced/backups/', 'AdminBackup'),
            'admin_backups_save_options' => array('/configure/advanced/backups/', 'AdminBackup', 'update'),
            'admin_backups_create' => array('/configure/advanced/backups/new', 'AdminBackup', 'addbackup'),
            'admin_backups_delete' => array('/configure/advanced/backups/backup_file.zip', 'AdminBackup', 'delete', array('filename' => 'backup_file.zip')),
            'admin_backups_bulk_delete' => array('/configure/advanced/backups/bulk-delete/', 'AdminBackup', 'submitBulkdeletebackup'),

            'admin_module_catalog' => array('/improve/modules/catalog', 'AdminModulesCatalog'),
            'admin_module_catalog_refresh' => array('/improve/modules/catalog/refresh', 'AdminModulesCatalog', 'refresh'),
            'admin_module_catalog_post' => array('/improve/modules/catalog/recommended', 'AdminModulesCatalog', 'recommended'),

            'admin_module_manage' => array('/improve/modules/manage', 'AdminModulesManage'),
            'admin_module_manage_alias' => array('/improve/modules/manage', 'AdminModulesSf'),

            'admin_module_notification' => array('/improve/modules/alerts', 'AdminModulesNotifications'),
            'admin_module_notification_count' => array('/improve/modules/alerts/count', 'AdminModulesNotifications', 'count'),

            'admin_module_updates' => array('/improve/modules/updates', 'AdminModulesUpdates'),

            'admin_module_addons_store' => array('/improve/modules/addons-store', 'AdminAddonsCatalog'),

            'admin_modules_positions' => array('/improve/design/modules/positions/', 'AdminModulesPositions'),
            'admin_modules_positions_unhook' => array('/improve/design/modules/positions/unhook', 'AdminModulesPositions', 'unhook'),

            'admin_customer_preferences' => array('/configure/shop/customer-preferences/', 'AdminCustomerPreferences'),
            'admin_customer_preferences_process' => array('/configure/shop/customer-preferences/', 'AdminCustomerPreferences', 'update'),

            'admin_order_delivery_slip' => array('/sell/orders/delivery-slips/', 'AdminDeliverySlip'),
            'admin_order_delivery_slip_pdf' => array('/sell/orders/delivery-slips/pdf', 'AdminDeliverySlip', 'submitAdddelivery'),

            'admin_import' => array('/configure/advanced/import/', 'AdminImport'),
            'admin_import_file_upload' => array('/configure/advanced/import/file/upload', 'AdminImport', 'uploadCsv'),
            'admin_import_file_delete' => array('/configure/advanced/import/file/delete', 'AdminImport', 'delete'),
            'admin_import_file_download' => array('/configure/advanced/import/file/download', 'AdminImport', 'download'),
            'admin_import_sample_download' => array('/configure/advanced/import/sample/download/categories_import', 'AdminImport', 'sampleDownload', array('sampleName' => 'categories_import')),

            'admin_system_information' => array('/configure/advanced/system-information/', 'AdminInformation'),
            'admin_system_information_check_files' => array('/configure/advanced/system-information/files', 'AdminInformation', 'checkFiles'),

            'admin_logs_index' => array('/configure/advanced/logs/', 'AdminLogs'),
            'admin_logs_save_settings' => array('/configure/advanced/logs/settings', 'AdminLogs', 'update'),
            'admin_logs_delete_all' => array('/configure/advanced/logs/delete-all', 'AdminLogs', 'deletelog'),

            'admin_maintenance' => array('/configure/shop/maintenance/', 'AdminMaintenance'),
            'admin_maintenance_save' => array('/configure/shop/maintenance/', 'AdminMaintenance', 'update'),

            'admin_order_preferences' => array('/configure/shop/order-preferences/', 'AdminOrderPreferences'),
            'admin_order_preferences_save' => array('/configure/shop/order-preferences/', 'AdminOrderPreferences', 'update'),

            'admin_product_preferences' => array('/configure/shop/product-preferences/', 'AdminPPreferences'),
            'admin_product_preferences_process' => array('/configure/shop/product-preferences/', 'AdminPPreferences', 'update'),

            'admin_performance' => array('/configure/advanced/performance/', 'AdminPerformance'),
            'admin_performance_save' => array('/configure/advanced/performance/', 'AdminPerformance', 'update'),
            'admin_clear_cache' => array('/configure/advanced/performance/clear-cache', 'AdminPerformance', 'empty_smarty_cache'),
            'admin_servers_add' => array('/configure/advanced/performance/memcache/servers', 'AdminPerformance', 'submitAddServer'),
            'admin_servers_delete' => array('/configure/advanced/performance/memcache/servers', 'AdminPerformance', 'deleteMemcachedServer'),

            'admin_preferences' => array('/configure/shop/preferences/preferences', 'AdminPreferences'),
            'admin_preferences_save' => array('/configure/shop/preferences/preferences', 'AdminPreferences', 'update'),

            'admin_shipping_preferences' => array('/improve/shipping/preferences', 'AdminShipping'),
            'admin_shipping_preferences_save' => array('/improve/shipping/preferences', 'AdminShipping', 'update'),

            'admin_stock_overview' => array('/sell/stocks/', 'AdminStockManagement'),

            'admin_theme_catalog' => array('/improve/design/themes-catalog/', 'AdminThemesCatalog'),

            'admin_international_translation_overview' => array('/improve/international/translations/', 'AdminTranslationSf'),

            'admin_payment_methods' => array('/improve/payment/payment_methods', 'AdminPayment'),

            'admin_localization_index' => array('/improve/international/localization/', 'AdminLocalization'),
            'admin_localization_save_options' => array('/improve/international/localization/options', 'AdminLocalization', 'update'),
            'admin_localization_import_pack' => array('/improve/international/localization/import-pack', 'AdminLocalization', 'submitLocalizationPack'),

            'admin_geolocation_index' => array('/improve/international/geolocation/', 'AdminGeolocation'),
            'admin_geolocation_save_options' => array('/improve/international/geolocation/process_form', 'AdminGeolocation', 'update'),

            'admin_payment_preferences' => array('/improve/payment/preferences', 'AdminPaymentPreferences'),
            'admin_payment_preferences_process' => array('/improve/payment/preferences', 'AdminPaymentPreferences', 'update'),

            'admin_order_invoices' => array('/sell/orders/invoices/', 'AdminInvoices'),
            'admin_order_invoices_process' => array('/sell/orders/invoices/', 'AdminInvoices', 'update'),
            'admin_order_invoices_generate_by_date' => array('/sell/orders/invoices/by_date', 'AdminInvoices', 'submitAddinvoice_date'),
            'admin_order_invoices_generate_by_status' => array('/sell/orders/invoices/by_status', 'AdminInvoices', 'submitAddinvoice_status'),

            'admin_emails_index' => array('/configure/advanced/emails/', 'AdminEmails'),
            'admin_emails_search' => array('/configure/advanced/emails/', 'AdminEmails', 'search'),
            'admin_emails_save_options' => array('/configure/advanced/emails/options', 'AdminEmails', 'update'),
            'admin_emails_send_test' => array('/configure/advanced/emails/send-testing-email', 'AdminEmails', 'testEmail'),
            'admin_emails_delete_bulk' => array('/configure/advanced/emails/delete-bulk', 'AdminEmails', 'submitBulkdeletemail'),
            'admin_emails_delete_all' => array('/configure/advanced/emails/delete-all', 'AdminEmails', 'deleteAll'),
            'admin_emails_delete' => array('/configure/advanced/emails/delete/42', 'AdminEmails', 'deletemail', array('id_mail' => 42)),

            'admin_metas_index' => array('/configure/shop/seo-urls/', 'AdminMeta'),
            'admin_metas_search' => array('/configure/shop/seo-urls/', 'AdminMeta', 'search'),
            'admin_metas_delete' => array('/configure/shop/seo-urls/42/delete', 'AdminMeta', 'deletemeta', array('id_meta' => 42)),
            'admin_metas_delete_bulk' => array('/configure/shop/seo-urls/delete-bulk', 'AdminMeta', 'submitBulkdeletmeta'),
            'admin_metas_save_options' => array('/configure/shop/seo-urls/options', 'AdminMeta', 'update'),
            'admin_metas_generate_robots_file' => array('/configure/shop/seo-urls/generate-robots-file', 'AdminMeta', 'submitRobots'),

            //This url is not ready to be migrated yet, the SF controller redirects to the legacy url
            // 'admin_meta_list_create' => ['/configure/shop/seo-urls/create', 'AdminMeta', 'addmeta'],
            // 'admin_meta_list_edit' => ['/configure/shop/seo-urls/edit/42', 'AdminMeta', 'updatemeta', ['id_meta' => 42]],

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
        );
    }

    /**
     * @return array
     */
    public static function getLegacyControllers()
    {
        return array(
            array('/admin-dev/index.php?controller=AdminLogin', 'AdminLogin'),
            array('/admin-dev/index.php?controller=AdminModulesPositions&addToHook=', 'AdminModulesPositions', array('addToHook' => '')),
            array('/admin-dev/index.php?controller=AdminModules', 'AdminModules'),
            array('/admin-dev/index.php?controller=AdminModules&configure=ps_linklist', 'AdminModules', array('configure' => 'ps_linklist')),
        );
    }

    public function testServiceExists()
    {
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');
        $this->assertInstanceOf(LegacyUrlConverter::class, $converter);
    }

    public function testLegacyWithRoute()
    {
        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, array('route' => 'admin_module_catalog_post'));
        $this->assertSameUrl('/improve/modules/catalog/recommended', $routeUrl, array('route'));
    }

    public function testDifferentLinkArguments()
    {
        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog');
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', false);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, array());
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, null);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, array(), array());
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);

        $routeUrl = $this->link->getAdminLink('AdminModulesCatalog', true, array(), null);
        $this->assertSameUrl('/improve/modules/catalog', $routeUrl);
    }

    /**
     * Looping manually uses MUCH less memory than dataProvider.
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
     * @param string      $expectedUrl
     * @param string      $controller
     * @param null|string $action
     * @param null|array  $queryParameters
     */
    private function doTestConverterByParameters($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');

        $caughtException = null;
        $caughtExceptionMessage = '';
        try {
            $parameters = array(
                'controller' => $controller,
                'action' => $action,
            );
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

    public function testIdEqualToOne()
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.converter.legacy_url_converter');

        $legacyUrl = $this->link->getAdminBaseLink().basename(_PS_ADMIN_DIR_).'/'.\Dispatcher::getInstance()->createUrl('AdminMeta').'&id_meta=1&conf=4';
        $convertedUrl = $converter->convertByUrl($legacyUrl);
        $this->assertSameUrl('/configure/shop/seo-urls/?id_meta=1&conf=4', $convertedUrl);
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
     * @param string      $expectedUrl
     * @param string      $controller
     * @param null|string $action
     * @param null|array  $queryParameters
     */
    private function doTestLegacyLinkClass($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        $parameters = array(
            'action' => $action,
        );
        if (null !== $queryParameters) {
            $parameters = array_merge($parameters, $queryParameters);
        }
        $linkUrl = $this->link->getAdminLink($controller, true, array(), $parameters);
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
     * @param string      $expectedUrl
     * @param string      $controller
     * @param null|string $action
     * @param null|array  $queryParameters
     */
    private function doTestLegacyClassParameterAction($expectedUrl, $controller, $action = null, array $queryParameters = null)
    {
        $parameters = null !== $queryParameters ? $queryParameters : array();
        if (null != $action) {
            $parameters[$action] = '';
        }
        $linkUrl = $this->link->getAdminLink($controller, true, array(), $parameters);
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
     * @param string     $expectedUrl
     * @param string     $controller
     * @param null|array $parameters
     *
     * @throws \PrestaShopException
     * @throws \ReflectionException
     */
    public function doTestLegacyControllers($expectedUrl, $controller, array $parameters = null)
    {
        $parameters = null === $parameters ? array() : $parameters;
        $linkUrl = $this->link->getAdminLink($controller, true, array(), $parameters);
        $this->assertSameUrl($expectedUrl, $linkUrl);
    }

    public function testRedirectionListener()
    {
        $legacyUrl = $this->link->getAdminBaseLink().basename(_PS_ADMIN_DIR_).'/'.\Dispatcher::getInstance()->createUrl('AdminAdminPreferences');
        $this->client->request('GET', $legacyUrl);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());
        $location = $response->headers->get('location');
        $this->assertSameUrl('/configure/advanced/administration/', $location);
    }

    public function testNoRedirectionListener()
    {
        $legacyUrl = $this->link->getAdminBaseLink().basename(_PS_ADMIN_DIR_).'/'.\Dispatcher::getInstance()->createUrl('AdminLogin');
        $this->client->request('GET', $legacyUrl);
        $response = $this->client->getResponse();
        $this->assertFalse($response->isRedirection());
    }

    public function testPostParameters()
    {
        $legacyUrl = $this->link->getAdminBaseLink().basename(_PS_ADMIN_DIR_).'/'.\Dispatcher::getInstance()->createUrl('AdminModulesPositions');
        $this->client->request('POST', $legacyUrl, array('submitAddToHook' => ''));
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
     * @param string     $expectedUrl
     * @param string     $url
     * @param null|array $ignoredParameters
     */
    private function assertSameUrl($expectedUrl, $url, array $ignoredParameters = null)
    {
        $this->assertNotNull($url);
        $parsedUrl = parse_url($url);
        $parameters = array();
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

        $cleanUrl = http_build_url(array(
            'path' => $parsedUrl['path'],
            'query' => http_build_query($parameters),
        ));

        $this->assertNotEmpty($parsedUrl['path']);
        $this->assertTrue($expectedUrl == $cleanUrl, sprintf(
            'Expected url %s is different with generated one: %s',
            $expectedUrl,
            $cleanUrl
        ));
    }

    /**
     * Force the static property SymfonyContainer::instance so that the Link class
     * has access to the router.
     *
     * @throws \ReflectionException
     */
    private function initContainerInstance()
    {
        $reflectedClass = new ReflectionClass(SymfonyContainer::class);
        $instanceProperty = $reflectedClass->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(self::$kernel->getContainer());
        $instanceProperty->setAccessible(false);
    }
}
