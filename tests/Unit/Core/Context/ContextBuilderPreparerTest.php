<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Context;

use Context;
use Language;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Shop;

class ContextBuilderPreparerTest extends TestCase
{
    public function testLegacyContextWithShopAndLanguage(): void
    {
        $languageBuilder = $this->createMock(LanguageContextBuilder::class);
        $languageBuilder->expects($this->once())->method('setLanguageId')->with(2);
        $languageBuilder->expects($this->once())->method('setDefaultLanguageId')->with(2);
        $shopBuilder = $this->createMock(ShopContextBuilder::class);
        $shopBuilder->expects($this->once())->method('setShopConstraint')->with(ShopConstraint::shop(5));
        $shopBuilder->expects($this->once())->method('setShopId')->with(5);

        $context = new Context();
        $context->language = $this->createMock(Language::class);
        $context->language->id = 2;
        $context->shop = $this->createMock(Shop::class);
        $context->shop->id = 5;

        (new ContextBuilderPreparer($languageBuilder, $shopBuilder))->prepareFromLegacyContext($context);
    }

    public function testLegacyContextWithoutShopPreparesNoShopScope(): void
    {
        // The installation prepares the builders before any shop exists
        $shopBuilder = $this->createMock(ShopContextBuilder::class);
        $shopBuilder->expects($this->never())->method('setShopConstraint');
        $shopBuilder->expects($this->never())->method('setShopId');

        $context = new Context();
        $context->shop = $this->createMock(Shop::class);
        $context->shop->id = 0;

        (new ContextBuilderPreparer($this->createMock(LanguageContextBuilder::class), $shopBuilder))->prepareFromLegacyContext($context);
    }

    /**
     * @dataProvider provideShopContexts
     */
    public function testPrepareShopContext(ShopConstraint $shopConstraint, ?int $shopId, ?int $expectedShopId): void
    {
        $shopBuilder = $this->createMock(ShopContextBuilder::class);
        $shopBuilder->expects($this->once())->method('setShopConstraint')->with($shopConstraint);
        if (null === $expectedShopId) {
            $shopBuilder->expects($this->never())->method('setShopId');
        } else {
            $shopBuilder->expects($this->once())->method('setShopId')->with($expectedShopId);
        }

        $preparer = new ContextBuilderPreparer($this->createMock(LanguageContextBuilder::class), $shopBuilder);
        $preparer->prepareShopContext($shopConstraint, $shopId);
    }

    /**
     * @return iterable<string, array{0: ShopConstraint, 1: int|null, 2: int|null}>
     */
    public static function provideShopContexts(): iterable
    {
        yield 'single shop, id from the constraint' => [ShopConstraint::shop(5), null, 5];
        yield 'single shop constraint running in another shop' => [ShopConstraint::shop(5), 3, 3];
        yield 'all shops on a shop' => [ShopConstraint::allShops(), 3, 3];
        yield 'shop group without shop, left to the builder' => [ShopConstraint::shopGroup(2), null, null];
    }
}
