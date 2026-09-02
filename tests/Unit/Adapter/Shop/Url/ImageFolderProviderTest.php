<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Shop\Url;

use Generator;
use Link;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Shop\Url\ImageFolderProvider;

class ImageFolderProviderTest extends TestCase
{
    /**
     * @dataProvider getTestData
     *
     * @param string $baseUrl
     * @param string $relativeImagePath
     * @param string $expectedUrl
     */
    public function testGetUrl(string $baseUrl, string $relativeImagePath, string $expectedUrl): void
    {
        $linkMock = $this->createMock(Link::class);
        $linkMock->method('getBaseLink')
            ->willReturn($baseUrl)
        ;
        $provider = new ImageFolderProvider($linkMock, $relativeImagePath);
        $generatedUrl = $provider->getUrl();
        $this->assertEquals($expectedUrl, $generatedUrl);
    }

    public function getTestData(): Generator
    {
        yield ['http://superurl', 'img/p', 'http://superurl/img/p'];
        yield ['http://superurl/', 'img/p', 'http://superurl/img/p'];
        yield ['http://superurl', 'img/p/', 'http://superurl/img/p'];
        yield ['http://superurl/', 'img/p/', 'http://superurl/img/p'];
    }
}
