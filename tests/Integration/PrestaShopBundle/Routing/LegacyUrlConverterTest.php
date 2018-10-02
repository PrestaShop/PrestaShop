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


use PrestaShopBundle\Routing\LegacyUrlConverter;
use Tests\Integration\PrestaShopBundle\Test\WebTestCase;

class LegacyUrlConverterTest extends WebTestCase
{
    public function testServiceExists()
    {
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.legacy_url_converter');
        $this->assertInstanceOf(LegacyUrlConverter::class, $converter);
    }

    /**
     * @dataProvider migratedControllers
     * @param string $expectedUrl
     * @param string $controller
     * @param string|null $action
     * @throws \PrestaShopBundle\Routing\Exception\ArgumentException
     * @throws \PrestaShopBundle\Routing\Exception\RouteNotFoundException
     */
    public function testLegacyLink($expectedUrl, $controller, $action = null)
    {
        /** @var LegacyUrlConverter $converter */
        $converter = self::$kernel->getContainer()->get('prestashop.bundle.routing.legacy_url_converter');
        $convertedUrl = $converter->convertByParameters([
            'controller' => $controller,
            'action' => $action,
        ]);
        $parsedUrl = parse_url($convertedUrl);
        $this->assertEquals($expectedUrl, $parsedUrl['path']);
    }

    /**
     * @return array
     */
    public function migratedControllers()
    {
        return [
            'admin_administration' => ['/configure/advanced/administration/', 'AdminAdminPreferences'],
            'admin_administration_save' => ['/configure/advanced/administration/', 'AdminAdminPreferences', 'save'],
        ];
    }
}
