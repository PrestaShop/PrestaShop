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
use Symfony\Component\Finder\Finder;

class CategoryImageFinderTest extends TestCase
{
    /**
     * @return void
     */
    public function testFindMenuThumbnails(): void
    {
        $categoryImageFinder = new CategoryImageFinder(dirname(__DIR__, 4) . '/Unit/Resources/category_menu_thumbnails/');
        $thumbnails = $categoryImageFinder->findMenuThumbnails(1);
        $this->findFileName('1-0_thumb.jpg', $thumbnails, true);
        $this->findFileName('1-1_thumb.jpg', $thumbnails, true);
        $this->findFileName('3-1_thumb.jpg', $thumbnails, false);
        $this->findFileName('1-1_thum.jpg', $thumbnails, false);
    }

    /**
     * Checks finder results for specific file name
     *
     * @param string $name
     * @param Finder $thumbnails
     * @param bool $shouldExist
     *
     * @return void
     */
    private function findFileName(string $name, Finder $thumbnails, bool $shouldExist): void
    {
        $hasValue = false;
        foreach ($thumbnails as $thumbnail) {
            if ($thumbnail->getFilename() === $name) {
                $hasValue = true;
            }
        }

        self::assertEquals($hasValue, $shouldExist);
    }
}
