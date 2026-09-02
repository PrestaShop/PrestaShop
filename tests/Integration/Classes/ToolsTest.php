<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
