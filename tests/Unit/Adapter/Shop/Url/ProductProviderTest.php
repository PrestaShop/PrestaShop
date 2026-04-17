<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Shop\Url;

use Link;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Shop\Url\ProductProvider;

class ProductProviderTest extends TestCase
{
    public function testGetUrl()
    {
        $productId = 42;
        $alias = 'super-product';
        $expectedUrl = 'http://superurl';
        $linkMock = $this->createMock(Link::class);
        $linkMock->method('getProductLink')
            ->with($productId, $alias)
            ->willReturn($expectedUrl)
        ;
        $provider = new ProductProvider($linkMock);
        $generatedUrl = $provider->getUrl($productId, $alias);
        $this->assertEquals($expectedUrl, $generatedUrl);
    }
}
