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
    public function testServiceExists()
    {
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.legacy_url_converter');
        $this->assertInstanceOf(LegacyUrlConverter::class, $converter);
    }

    public function xtestSample()
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
        $this->initStaticInstance();

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
     * @return array
     */
    public function migratedControllers()
    {
        return [
            'admin_administration' => ['/configure/advanced/administration/', 'AdminAdminPreferences'],
            'admin_administration_save' => ['/configure/advanced/administration/', 'AdminAdminPreferences', 'save'],
            'admin_backup' => ['/configure/advanced/backup/', 'AdminBackup'],
            'admin_backup_create' => ['/configure/advanced/backup/create', 'AdminBackup', 'add'],
            'admin_backup_delete' => ['/configure/advanced/backup/backup_file.zip', 'AdminBackup', 'delete', ['filename' => 'backup_file.zip']],
            'admin_backup_delete' => ['/configure/advanced/backup/bulk-delete/', 'AdminBackup', 'submitBulkdeletebackup']
        ];
    }

    /**
     * @param string $expectedUrl
     * @param string $url
     */
    private function assertSameUrl($expectedUrl, $url)
    {
        $this->assertNotNull($url);
        $parsedUrl = parse_url($url);
        $this->assertNotEmpty($parsedUrl['path']);
        $this->assertTrue($expectedUrl == $parsedUrl['path'], sprintf(
            'Expected url %s is different with generated one: %s',
            $expectedUrl,
            $parsedUrl['path']
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
