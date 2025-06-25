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

namespace Tests\Integration\Classes;

use PHPUnit\Framework\TestCase;
use Tests\Integration\Utility\ContextMockerTrait;
use Tools;

class ToolsTest extends TestCase
{
    use ContextMockerTrait;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
    }

    /**
     * @dataProvider getUrlsToSanitize
     *
     * @param string $url
     * @param string $expected
     * @param string $physicalUri
     *
     * @return void
     */
    public function testSanitizeAdminUrl(string $url, string $expected, string $physicalUri = ''): void
    {
        // Override the mocked shop in context
        static::getContext()->shop->physical_uri = $physicalUri;
        $this->assertEquals($expected, Tools::sanitizeAdminUrl($url));
    }

    public function getUrlsToSanitize(): iterable
    {
        yield 'url starting with index.php' => [
            'index.php?controller=AdminModules',
            'http://localhost/admin-dev/index.php?controller=AdminModules',
        ];

        yield 'url starting with /admin-dev/index.php' => [
            '/admin-dev/index.php?controller=AdminModules',
            'http://localhost/admin-dev/index.php?controller=AdminModules',
        ];

        yield 'url symfony style with admin-dev' => [
            '/admin-dev/modules/link-widget/list',
            'http://localhost/admin-dev/modules/link-widget/list',
        ];

        yield 'url symfony style without admin-dev' => [
            '/modules/link-widget/list',
            'http://localhost/admin-dev/modules/link-widget/list',
        ];

        yield 'url symfony style without starting /' => [
            'modules/link-widget/list',
            'http://localhost/admin-dev/modules/link-widget/list',
        ];

        yield 'absolute legacy url' => [
            'http://localhost/admin-dev/index.php?controller=AdminModules',
            'http://localhost/admin-dev/index.php?controller=AdminModules',
        ];

        yield 'absolute symfony url' => [
            'http://localhost/admin-dev/modules/link-widget/list',
            'http://localhost/admin-dev/modules/link-widget/list',
        ];

        yield 'external url' => [
            'http://www.prestahop-project.org',
            'http://www.prestahop-project.org',
        ];

        // Now test use cases where the shop is installed in a sub folder so physical URI is not empty
        yield 'shop with physical uri, url does not contain it' => [
            '/admin-dev/modules/blockwishlist/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/configuration',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url does not contain it nor the admin folder' => [
            '/modules/blockwishlist/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/configuration',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url already contains it at the beginning' => [
            '/shop_sub_folder/admin-dev/modules/blockwishlist/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/configuration',
            // Works with trailing / also
            '/shop_sub_folder/',
        ];

        yield 'shop with physical uri, url already contains it but in the middle' => [
            '/admin-dev/modules/blockwishlist/shop_sub_folder/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/shop_sub_folder/configuration',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, absolute url' => [
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/shop_sub_folder/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/shop_sub_folder/configuration',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url starting with index.php' => [
            'index.php?controller=AdminModules',
            'http://localhost/shop_sub_folder/admin-dev/index.php?controller=AdminModules',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url starting with /admin-dev/index.php' => [
            '/admin-dev/index.php?controller=AdminModules',
            'http://localhost/shop_sub_folder/admin-dev/index.php?controller=AdminModules',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url starting with /shop_sub_folder/admin-dev/index.php' => [
            '/shop_sub_folder/admin-dev/index.php?controller=AdminModules',
            'http://localhost/shop_sub_folder/admin-dev/index.php?controller=AdminModules',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, external url' => [
            'http://www.prestahop-project.org',
            'http://www.prestahop-project.org',
            '/shop_sub_folder',
        ];
    }
}
