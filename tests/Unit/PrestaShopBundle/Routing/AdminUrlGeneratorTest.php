<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Routing;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Routing\AdminUrlGenerator;

class AdminUrlGeneratorTest extends TestCase
{
    /**
     * @dataProvider provideAdminUrlCases
     */
    public function testGenerateAdminUrl(string $baseUrl, string $adminFolder, string $urlPath, string $expected): void
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getBaseURL')->willReturn($baseUrl);

        $generator = new AdminUrlGenerator($shopContext, $adminFolder);

        self::assertSame($expected, $generator->generateAdminUrl($urlPath));
    }

    public static function provideAdminUrlCases(): iterable
    {
        yield 'reset password route' => [
            'https://example.com/',
            'admin123',
            '/reset-password/some-token',
            'https://example.com/admin123/index.php/reset-password/some-token',
        ];

        yield 'legacy controller query string' => [
            'https://example.com',
            'admin123',
            '?controller=LegacyAdminController',
            'https://example.com/admin123/index.php?controller=LegacyAdminController',
        ];

        yield 'trims trailing slash on base URL and slashes on admin folder' => [
            'https://example.com/shop/',
            '/admin123/',
            '/reset-password/token',
            'https://example.com/shop/admin123/index.php/reset-password/token',
        ];

        yield 'empty url path' => [
            'https://example.com',
            'admin123',
            '',
            'https://example.com/admin123/index.php',
        ];
    }
}
