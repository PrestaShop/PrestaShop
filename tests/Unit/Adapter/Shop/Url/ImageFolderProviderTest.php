<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
