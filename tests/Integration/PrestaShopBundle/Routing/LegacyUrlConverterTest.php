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

class LegacyUrlConverterTest extends LightWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->initStaticInstance();
    }

    /**
     * @return array
     */
    public static function getMigratedControllers()
    {
        return [
            'admin_administration' => ['/configure/advanced/administration/', 'AdminAdminPreferences'],
            'admin_administration_save' => ['/configure/advanced/administration/', 'AdminAdminPreferences', 'save'],

            'admin_backup' => ['/configure/advanced/backup/', 'AdminBackup'],
            'admin_backup_create' => ['/configure/advanced/backup/create', 'AdminBackup', 'add'],
            'admin_backup_delete' => ['/configure/advanced/backup/backup_file.zip', 'AdminBackup', 'delete', ['filename' => 'backup_file.zip']],
            'admin_backup_bulk_delete' => ['/configure/advanced/backup/bulk-delete/', 'AdminBackup', 'submitBulkdeletebackup'],

            'admin_module_catalog' => ['/improve/modules/catalog', 'AdminModulesCatalog'],
            'admin_module_catalog_refresh' => ['/improve/modules/catalog/refresh', 'AdminModulesCatalog', 'refresh'],
            'admin_module_catalog_post' => ['/improve/modules/catalog/recommended', 'AdminModulesCatalog', 'recommended'],

            'admin_module_manage' => ['/improve/modules/manage', 'AdminModulesManage'],

            'admin_module_configure_action' => ['/improve/modules/manage/action/configure/ps_linklist', 'AdminModules', null, ['module_name' => 'ps_linklist']],

            'admin_module_notification' => ['/improve/modules/alerts', 'AdminModulesNotifications'],
            'admin_module_notification_count' => ['/improve/modules/alerts/count', 'AdminModulesNotifications', 'count'],

            'admin_module_updates' => ['/improve/modules/updates', 'AdminModulesUpdates'],

            'admin_module_addons_store' => ['/improve/modules/addons-store', 'AdminAddonsCatalog'],

            'admin_modules_positions' => ['/improve/design/modules/positions/', 'AdminModulesPositions'],
            'admin_modules_positions_unhook' => ['/improve/design/modules/positions/unhook', 'AdminModulesPositions', 'unhook'],
        ];
    }

    /**
     * @return array
     */
    public static function getLegacyControllers()
    {
        return [
            ['/admin-dev/index.php?controller=AdminLogin', 'AdminLogin'],
            ['/admin-dev/index.php?controller=AdminModulesPositions&addToHook=', 'AdminModulesPositions', ['addToHook' => '']]
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

    public function testSample()
    {
        $this->testLegacyLinkClass('/configure/advanced/backup/create', 'AdminBackup', 'add');
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
        $this->initStaticInstance();
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
        parse_str($parsedUrl['query'], $parameters);

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
    private function initStaticInstance()
    {
        $reflectedClass = new ReflectionClass(SymfonyContainer::class);
        $instanceProperty = $reflectedClass->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(self::$kernel->getContainer());
    }
}
