<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Util\Url;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;

class UrlCleanerTest extends TestCase
{
    /**
     * @dataProvider getUrlsToClean
     *
     * @param string $url
     * @param array $removedParams
     * @param string $expectedUrl
     */
    public function testCleanUrl(string $url, array $removedParams, string $expectedUrl): void
    {
        $cleanUrl = UrlCleaner::cleanUrl($url, $removedParams);
        $this->assertEquals($expectedUrl, $cleanUrl);
    }

    public function getUrlsToClean(): iterable
    {
        yield 'clean absolute url _token' => [
            'http://localhost/admin-dev/product/page?_token=sdfgsdfgsdfgsdfg',
            ['_token'],
            'http://localhost/admin-dev/product/page',
        ];

        yield 'clean absolute url https _token' => [
            'https://localhost/admin-dev/product/page?_token=sdfgsdfgsdfgsdfg',
            ['_token'],
            'https://localhost/admin-dev/product/page',
        ];

        yield 'clean absolute url without params to remove' => [
            'http://localhost/admin-dev/product/page?_token=sdfgsdfgsdfgsdfg',
            [],
            'http://localhost/admin-dev/product/page?_token=sdfgsdfgsdfgsdfg',
        ];

        yield 'clean absolute url _token and parameter before' => [
            'http://localhost/admin-dev/product/page?productId=42&_token=sdfgsdfgsdfgsdfg',
            ['_token'],
            'http://localhost/admin-dev/product/page?productId=42',
        ];

        yield 'clean absolute url _token and parameter after' => [
            'http://localhost/admin-dev/product/page?_token=sdfgsdfgsdfgsdfg&productId=42',
            ['_token'],
            'http://localhost/admin-dev/product/page?productId=42',
        ];

        yield 'clean absolute url _token and parameter before and after' => [
            'http://localhost/admin-dev/product/page?combinationId=51&_token=sdfgsdfgsdfgsdfg&productId=42',
            ['_token'],
            'http://localhost/admin-dev/product/page?combinationId=51&productId=42',
        ];

        yield 'clean absolute url _token empty query' => [
            'http://localhost/admin-dev/product/page?',
            ['_token'],
            'http://localhost/admin-dev/product/page',
        ];

        yield 'clean absolute url _token and fragment' => [
            'http://localhost/admin-dev/product/page?combinationId=51&_token=sdfgsdfgsdfgsdfg&productId=42#anchor',
            ['_token'],
            'http://localhost/admin-dev/product/page?combinationId=51&productId=42#anchor',
        ];

        yield 'clean relative url _token' => [
            '/admin-dev/product/page?_token=sdfgsdfgsdfgsdfg',
            ['_token'],
            '/admin-dev/product/page',
        ];

        yield 'clean url _token without _token' => [
            'http://localhost/admin-dev/product/page?combinationId=51&productId=42',
            ['_token'],
            'http://localhost/admin-dev/product/page?combinationId=51&productId=42',
        ];

        yield 'clean url _token without parameters' => [
            'http://localhost/admin-dev/product/page',
            ['_token'],
            'http://localhost/admin-dev/product/page',
        ];

        yield 'clean url setShopContext without parameters' => [
            'http://localhost/admin-dev/product/page?setShopContext=s-1',
            ['setShopContext'],
            'http://localhost/admin-dev/product/page',
        ];

        yield 'clean url setShopContext with full domain' => [
            'http://www.myshop.com/admin-dev/product/page?setShopContext=s-1',
            ['setShopContext'],
            'http://www.myshop.com/admin-dev/product/page',
        ];

        yield 'clean url setShopContext with sub domain' => [
            'http://www.demo.myshop.com/admin-dev/product/page?setShopContext=s-1',
            ['setShopContext'],
            'http://www.demo.myshop.com/admin-dev/product/page',
        ];

        yield 'clean url setShopContext in url with port username and password' => [
            'http://username:password@hostname:9090/path?arg=value&setShopContext=s-1#anchor',
            ['setShopContext'],
            'http://username:password@hostname:9090/path?arg=value#anchor',
        ];

        yield 'clean url setShopContext in url with port username and password and no scheme defined' => [
            '//username:password@hostname:9090/path?arg=value&setShopContext=s-1#anchor',
            ['setShopContext'],
            'username:password@hostname:9090/path?arg=value#anchor',
        ];

        yield 'clean bloubibulga' => [
            'bloubibulga',
            ['setShopContext'],
            '/bloubibulga',
        ];

        yield 'clean url setShopContext in url with port username and password and associative array parameters' => [
            'http://usr:pss@example.com:81/mypath/myfile.html?a=b&b[4]=2&setShopContext=s-1&b[6]=3#myfragment',
            ['setShopContext'],
            'http://usr:pss@example.com:81/mypath/myfile.html?a=b&b[4]=2&b[6]=3#myfragment',
        ];

        yield 'clean url setShopContext in url with port username and password and form property path parameters' => [
            'http://usr:pss@example.com:81/mypath/myfile.html?form[toto][field]=test&setShopContext=s-1&b[6]=3&team[players][]=42&team[players][]=51#myfragment',
            ['setShopContext'],
            'http://usr:pss@example.com:81/mypath/myfile.html?form[toto][field]=test&b[6]=3&team[players][0]=42&team[players][1]=51#myfragment',
        ];

        yield 'clean url with action without value, the action parameter at the end must remain without value' => [
            'index.php?controller=AdminCartRules&addcart_rule',
            [],
            '/index.php?controller=AdminCartRules&addcart_rule',
        ];

        yield 'clean url with action without value, the action parameter in the middle must remain without value' => [
            '/index.php?controller=AdminCartRules&addcart_rule&titi=tata',
            [],
            '/index.php?controller=AdminCartRules&addcart_rule&titi=tata',
        ];

        yield 'clean url with action without value, the action parameter at the beginning must remain without value' => [
            '/index.php?addcart_rule&controller=AdminCartRules&titi=tata',
            [],
            '/index.php?addcart_rule&controller=AdminCartRules&titi=tata',
        ];

        yield 'clean url with action with empty value, the action parameter in the middle must remain with empty value' => [
            'index.php?controller=AdminCartRules&addcart_rule=&titi=tata',
            [],
            '/index.php?controller=AdminCartRules&addcart_rule=&titi=tata',
        ];

        yield 'clean url with action with empty value, the action parameter at the end must remain with empty value' => [
            'index.php?controller=AdminCartRules&addcart_rule=',
            [],
            '/index.php?controller=AdminCartRules&addcart_rule=',
        ];

        yield 'clean url with action with empty value, the action parameter at the beginnin must remain with empty value' => [
            'index.php?addcart_rule=&controller=AdminCartRules&titi=tata',
            [],
            '/index.php?addcart_rule=&controller=AdminCartRules&titi=tata',
        ];
    }
}
