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
    public function testItDropsOnlyNonHttpSchemes(string $backUrl, string $expected)
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

        yield 'external http url stays' => [
            'https://dashboard.example/control',
            'https://dashboard.example/control',
        ];

        yield 'protocol relative url stays' => [
            '//dashboard.example/control',
            '//dashboard.example/control',
        ];

        yield 'javascript scheme is dropped' => [
            'javascript:alert(document.cookie)',
            '',
        ];

        yield 'data scheme is dropped' => [
            'data:text/html,<script>alert(1)</script>',
            '',
        ];

        yield 'javascript scheme with leading control character is dropped' => [
            "\0javascript:alert(1)",
            '',
        ];

        yield 'javascript scheme with embedded tab is dropped' => [
            "java\tscript:alert(1)",
            '',
        ];

        yield 'javascript scheme with embedded line feed is dropped' => [
            "java\nscript:alert(1)",
            '',
        ];

        yield 'javascript scheme with embedded carriage return is dropped' => [
            "java\rscript:alert(1)",
            '',
        ];
    }
}
