<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Meta;
use PHPUnit\Framework\TestCase;

final class MetaTest extends TestCase
{
    /**
     * @dataProvider listingPageProvider
     */
    public function testListingControllersUseTheirDeclaredPageName(string $page, string $filenameFallback): void
    {
        $pages = Meta::getPages();

        self::assertArrayHasKey($page, $pages);
        self::assertSame($page, $pages[$page]);
        self::assertNotContains($filenameFallback, $pages);
    }

    public static function listingPageProvider(): iterable
    {
        yield 'promotions' => ['prices-drop', 'pricesdrop'];
        yield 'new products' => ['new-products', 'newproducts'];
        yield 'best sellers' => ['best-sales', 'bestsales'];
    }

    public function testExcludedControllersInSubdirectoriesRemainUnavailable(): void
    {
        self::assertNotContains('category', Meta::getPages());
    }

    public function testDirectoryIndexFilesAreNotOfferedAsPages(): void
    {
        $pages = Meta::getPages();

        self::assertSame('index', $pages['index']);
        self::assertNotContains('index.php', $pages);
        self::assertCount(1, array_keys($pages, 'index', true));
    }

    public function testRootControllerKeepsItsDeclaredPageName(): void
    {
        self::assertSame('contact', Meta::getPages()['contact']);
    }

    public function testModuleControllerKeepsItsPageName(): void
    {
        self::assertSame('module-translationtest-bar', Meta::getPages()['translationtest - bar']);
    }

    public function testExplicitlySelectedPageRemainsAvailable(): void
    {
        $pages = Meta::getPages(false, 'custom-selected-page');

        self::assertSame('custom-selected-page', $pages['custom-selected-page']);
    }
}
