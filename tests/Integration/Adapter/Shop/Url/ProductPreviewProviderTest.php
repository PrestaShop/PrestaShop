<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Shop\Url;

use Link;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Shop\Url\ProductPreviewProvider;

class ProductPreviewProviderTest extends TestCase
{
    public function testGetUrl(): void
    {
        $employeeId = 1;
        $productId = 18;
        $expectedUrlActive = 'http://superurl';
        $linkMock = $this->createMock(Link::class);
        $linkMock->method('getProductLink')
            ->with($productId)
            ->willReturn($expectedUrlActive)
        ;
        $provider = new ProductPreviewProvider($linkMock, true, $employeeId);
        $generatedUrlActive = $provider->getUrl($productId, true);
        $this->assertEquals($expectedUrlActive, $generatedUrlActive);

        $expectedUrlInactive = 'http://superurl';
        $linkMock = $this->createMock(Link::class);
        $linkMock->method('getProductLink')
            ->with($productId)
            ->willReturn($expectedUrlInactive)
        ;
        $provider = new ProductPreviewProvider($linkMock, true, $employeeId);
        $generatedUrlInactive = $provider->getUrl($productId, false);
        $urlParts = parse_url($generatedUrlInactive);
        parse_str($urlParts['query'], $queryParts);
        $this->assertEquals('http', $urlParts['scheme']);
        $this->assertEquals('superurl', $urlParts['host']);
        $this->assertArrayHasKey('adtoken', $queryParts);
        $this->assertArrayHasKey('id_employee', $queryParts);
        $this->assertArrayHasKey('preview', $queryParts);
        $this->assertEquals('1', $queryParts['id_employee']);
        $this->assertEquals('1', $queryParts['preview']);
    }
}
