<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Seo;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Seo\NoIndexUrlParametersMatcher;

class NoIndexUrlParametersMatcherTest extends TestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testMatchesUrl(string $parameterNames, string $url, bool $expected): void
    {
        $this->assertSame($expected, (new NoIndexUrlParametersMatcher($parameterNames))->matchesUrl($url));
    }

    public static function provideUrls(): iterable
    {
        yield 'no query string' => ['back,page', '/3-clothes', false];
        yield 'empty query string' => ['back,page', '/3-clothes?', false];
        yield 'listed parameter' => ['back,page', '/3-clothes?page=2', true];
        yield 'listed parameter among others' => ['back,page', '/login?create_account=1&back=my-account', true];
        yield 'listed parameter without value' => ['back,page', '/login?back', true];
        yield 'array parameter' => ['back,page', '/3-clothes?page[]=2', true];
        yield 'unlisted parameter' => ['back,page', '/3-clothes?order=product.price.asc', false];
        yield 'parameter name contained in another one' => ['q', '/3-clothes?search_query=shirt', false];
        yield 'listed name used as a value' => ['q', '/3-clothes?page=q', false];
        yield 'listed name in the path only' => ['order', '/order?step=1', false];
        yield 'absolute url' => ['page', 'https://shop.example/3-clothes?page=2#products', true];
        yield 'list with spaces and empty items' => [' back , ,page ', '/3-clothes?page=2', true];
        yield 'numeric parameter name' => ['2,page', '/3-clothes?2=x', true];
        yield 'empty list' => ['', '/3-clothes?page=2', false];
    }

    public function testParseParameterNames(): void
    {
        $this->assertSame(['back', 'page', 'utm_source'], NoIndexUrlParametersMatcher::parseParameterNames(' back, page ,,utm_source,back'));
        $this->assertSame([], NoIndexUrlParametersMatcher::parseParameterNames(''));
    }
}
