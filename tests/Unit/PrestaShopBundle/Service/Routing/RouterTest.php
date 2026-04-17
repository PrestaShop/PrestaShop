<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Service\Routing;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Service\Routing\Router;

class RouterTest extends TestCase
{
    public function testGenerateTokenizedUrlWithFragments(): void
    {
        $url = 'my-shop.com/product#routing-in-prestashop';
        $route = Router::generateTokenizedUrl($url, 'token');
        static::assertEquals('my-shop.com/product?_token=token#routing-in-prestashop', $route);

        $url = 'my-shop.com/product?delete=1#routing-in-prestashop';
        $route = Router::generateTokenizedUrl($url, 'token');
        static::assertEquals('my-shop.com/product?delete=1&_token=token#routing-in-prestashop', $route);

        $url = 'localhost/shopp/product?delete=1&confirm=1#routing-in-prestashop/tokens?route';
        $route = Router::generateTokenizedUrl($url, 'token');
        static::assertEquals('localhost/shopp/product?delete=1&confirm=1&_token=token#routing-in-prestashop/tokens?route', $route);
    }
}
