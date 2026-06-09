<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util\Url;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use Symfony\Component\HttpFoundation\Request;

class BackUrlProviderTest extends TestCase
{
    public function testItReturnsDecodedUrl()
    {
        $backUrlProvider = new BackUrlProvider();

        $actualResult = $backUrlProvider->getBackUrl(
            new Request(
                ['back' => 'http%3A%2F%2Flocalhost'],
                [],
                [],
                [],
                [],
                ['HTTP_HOST' => 'localhost']
            )
        );

        $this->assertEquals('http://localhost', $actualResult);
    }

    /**
     * @dataProvider provideBackUrls
     */
    public function testItOnlyKeepsBackUrlsPointingToTheCurrentShop(string $backUrl, string $expected)
    {
        $backUrlProvider = new BackUrlProvider();

        $actualResult = $backUrlProvider->getBackUrl(
            new Request(
                ['back' => rawurlencode($backUrl)],
                [],
                [],
                [],
                [],
                ['HTTP_HOST' => 'localhost.org']
            )
        );

        $this->assertEquals($expected, $actualResult);
    }

    public static function provideBackUrls(): iterable
    {
        yield 'relative url stays' => [
            '/admin-dev/index.php?controller=AdminCustomers&conf=4',
            '/admin-dev/index.php?controller=AdminCustomers&conf=4',
        ];

        yield 'bare relative url stays' => [
            'index.php?controller=AdminProducts',
            'index.php?controller=AdminProducts',
        ];

        yield 'absolute url on the same host stays' => [
            'http://localhost.org/admin-dev/index.php/sell/customers/2/view',
            'http://localhost.org/admin-dev/index.php/sell/customers/2/view',
        ];

        yield 'external host is dropped' => [
            'https://evil.example/login',
            '',
        ];

        yield 'protocol relative url is dropped' => [
            '//evil.example/login',
            '',
        ];

        yield 'backslash host is dropped' => [
            '/\\evil.example',
            '',
        ];

        yield 'javascript scheme is dropped' => [
            'javascript:alert(document.cookie)',
            '',
        ];

        yield 'data scheme is dropped' => [
            'data:text/html,<script>alert(1)</script>',
            '',
        ];

        yield 'look-alike host is dropped' => [
            'http://localhost.org.evil.example/',
            '',
        ];
    }
}
