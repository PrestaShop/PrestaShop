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

    /**
     * The fail-closed branch is the security-relevant half of the guard and nothing else exercises it:
     * preg_match() has to return false, not 0. Exhausting the backtrack limit does that. The JIT is
     * disabled alongside because its error path would not reach the same branch, and the test needs
     * its own process: PHP caches compiled patterns per process, so once an earlier test has compiled
     * this pattern with the JIT enabled, setting pcre.jit at runtime no longer affects it and the
     * backtrack limit is never hit.
     *
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function testItDropsTheUrlWhenTheSchemeCheckErrors()
    {
        $backUrlProvider = new BackUrlProvider();
        $previousLimit = ini_get('pcre.backtrack_limit');
        $previousJit = ini_get('pcre.jit');
        ini_set('pcre.backtrack_limit', '2');
        ini_set('pcre.jit', '0');

        try {
            $actualResult = $backUrlProvider->getBackUrl(
                new Request(
                    ['back' => rawurlencode(str_repeat('a', 50) . ':')],
                    [],
                    [],
                    [],
                    [],
                    ['HTTP_HOST' => 'localhost']
                )
            );
        } finally {
            ini_set('pcre.backtrack_limit', (string) $previousLimit);
            ini_set('pcre.jit', (string) $previousJit);
        }

        $this->assertSame('', $actualResult, 'a scheme check that errored must not let the value through');
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
