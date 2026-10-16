<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Context;
use Language;
use Meta;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Translation\Translator;

final class MetaTest extends TestCase
{
    private mixed $previousLanguage;
    private mixed $previousTranslator;

    protected function setUp(): void
    {
        $context = Context::getContext();
        $this->previousLanguage = $context->language;
        $translator = new ReflectionProperty($context, 'translator');
        $this->previousTranslator = $translator->getValue($context);
        $context->language = (new ReflectionClass(Language::class))->newInstanceWithoutConstructor();
        $context->language->locale = 'en-US';
        $translator->setValue($context, new Translator('en-US'));
    }

    protected function tearDown(): void
    {
        $context = Context::getContext();
        $context->language = $this->previousLanguage;
        (new ReflectionProperty($context, 'translator'))->setValue($context, $this->previousTranslator);
    }

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
