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

namespace Tests\Unit\Core\Category\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Category\Provider\CategoryImageFinder;
use PrestaShop\PrestaShop\Core\Category\Provider\MenuThumbnailAvailableKeyProvider;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class MenuThumbnailAvailableKeyProviderTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetAvailableKeys(): void
    {
        $categoryId = 1;
        $categoryImageFinder = $this->getMockBuilder(CategoryImageFinder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $file1 = new SplFileInfo(sprintf('%d-0_thumb.jpg', $categoryId));
        $file2 = new SplFileInfo(sprintf('%d-1_thumb.jpg', $categoryId));

        $mockFinder = $this->getMockBuilder(Finder::class)
            ->getMock();
        $mockFinder->method('getIterator')->willReturn(
            new \ArrayIterator([
                $file1,
                $file2,
            ])
        );

        $categoryImageFinder->method('findMenuThumbnails')
            ->willReturn(
                $mockFinder
            );

        $menuThumbnailAvailableKeyProvider = new MenuThumbnailAvailableKeyProvider($categoryImageFinder);

        $result = $menuThumbnailAvailableKeyProvider->getAvailableKeys($categoryId);

        self::assertEquals(
            [
                2 => 2,
            ],
            $result
        );
    }
}
