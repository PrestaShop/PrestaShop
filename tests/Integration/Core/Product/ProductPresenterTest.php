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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Product;

use Language;
use Link;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo Move to Unit Tests when HookManager will be instanciated in ProductPresenter
 */
class ProductPresenterTest extends TestCase
{
    /**
     * @var ProductPresentationSettings
     */
    private $settings;

    /**
     * @var array<string, mixed>
     */
    private $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settings = new ProductPresentationSettings();
        $this->settings->catalog_mode = false;
        $this->settings->restricted_country_mode = false;
        $this->settings->showPrices = true;

        $this->product = [
            'available_for_order' => true,
            'id_product' => 1,
            'id_product_attribute' => 0,
            'link_rewrite' => 'hérisson',
            'reference' => 'ref-herisson',
            'price' => null,
            'price_without_reduction' => null,
            'price_tax_exc' => null,
            'specific_prices' => null,
            'customizable' => false,
            'quantity' => 1,
            'allow_oosp' => false,
            'online_only' => false,
            'reduction' => false,
            'on_sale' => false,
            'new' => false,
            'pack' => false,
            'show_price' => true,
            'active' => true,
        ];
    }

    private function _presentProduct(string $presenterClass, ?string $field)
    {
        $imageRetriever = $this->createMock(ImageRetriever::class);
        $imageRetriever->method('getAllProductImages')->withAnyParameters()->willReturn([
            ['id_image' => 0, 'associatedVariants' => []],
        ]);

        $link = $this->createMock(Link::class);
        $link->method('getAddToCartURL')->withAnyParameters()->willReturn('http://add-to-cart.url');

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->withAnyParameters()->willReturn('some label');

        $priceFormatter = $this->createMock(PriceFormatter::class);
        $priceFormatter->method('convertAmount')->withAnyParameters()->willReturnArgument(0);
        $priceFormatter->method('format')->withAnyParameters()->willReturnCallback(function (?float $price) {
            return '#' . $price;
        });

        $presenter = new $presenterClass(
            $imageRetriever,
            $link,
            $priceFormatter,
            $this->createMock(ProductColorsRetriever::class),
            $translator
        );

        $product = $presenter->present(
            $this->settings,
            $this->product,
            $this->createMock(Language::class)
        );

        if (null === $field) {
            return $product;
        } else {
            return $product[$field];
        }
    }

    private function getPresentedProduct(string $field = null)
    {
        return $this->_presentProduct(ProductPresenter::class, $field);
    }

    private function getPresentedProductForListing(string $field = null)
    {
        return $this->_presentProduct(ProductListingPresenter::class, $field);
    }

    public function testPriceShouldNotBeShownInCatalogMode(): void
    {
        $this->settings->catalog_mode = true;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function testPriceShouldBeShownInCatalogModeWithPrices(): void
    {
        $this->settings->catalog_mode = true;
        $this->settings->catalog_mode_with_prices = true;
        $this->assertTrue($this->getPresentedProduct('show_price'));
    }

    public function testPriceShouldNotBeShownInCatalogModeWithoutPrices(): void
    {
        $this->settings->catalog_mode = true;
        $this->settings->catalog_mode_with_prices = false;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function testPriceShouldShownInRestrictedCountryMode(): void
    {
        $this->settings->restricted_country_mode = true;
        $this->assertTrue($this->getPresentedProduct('show_price'));
    }

    public function testPriceShouldNotBeShownIfProductNotAvailableForOrder(): void
    {
        $this->product['available_for_order'] = false;
        $this->product['show_price'] = false;

        $this->assertFalse($this->getPresentedProduct('show_price'));

        $this->product['available_for_order'] = false;
        $this->product['show_price'] = true;

        $this->assertTrue($this->getPresentedProduct('show_price'));
    }

    public function testPriceIsTaxExcluded(): void
    {
        $this->settings->include_taxes = false;
        $this->product['price_tax_exc'] = 8;
        $this->assertStringStartsWith(
            '#8',
            $this->getPresentedProduct('price')
        );
    }

    public function testPriceIsTaxIncluded(): void
    {
        $this->settings->include_taxes = true;
        $this->product['price'] = 16;
        $this->assertStringStartsWith(
            '#16',
            $this->getPresentedProduct('price')
        );
    }

    public function testCannotAddToCartIfNotCustomized(): void
    {
        $this->product['customization_required'] = true;
        $this->assertNotEquals(
            'http://add-to-cart.url',
            $this->getPresentedProduct('add_to_cart_url')
        );
    }

    public function testCanAddToCartIfCustomized(): void
    {
        $this->product['customization_required'] = true;
        $this->product['customizations'] = [
            'fields' => [
                ['is_customized' => true, 'required' => true],
            ],
        ];
        $this->assertEquals(
            'http://add-to-cart.url',
            $this->getPresentedProduct('add_to_cart_url')
        );
    }

    public function testCanAddToCartIfCustomizedAllRequiredFields(): void
    {
        $this->product['customization_required'] = true;
        $this->product['customizations'] = [
            'fields' => [
                ['is_customized' => true, 'required' => true],
                ['is_customized' => false, 'required' => false],
            ],
        ];
        $this->assertEquals(
            'http://add-to-cart.url',
            $this->getPresentedProduct('add_to_cart_url')
        );
    }

    public function testCannotAddToCartFromListingIfVariants(): void
    {
        $this->product['id_product_attribute'] = 42;
        $this->settings->allow_add_variant_to_cart_from_listing = false;
        $this->assertNull(
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }

    public function testCannotAddToCartFromListingEvenWhenCustomized(): void
    {
        $this->product['customization_required'] = true;
        $this->product['customizations'] = [
            'fields' => [
                ['is_customized' => true, 'required' => true],
            ],
        ];
        $this->assertNull(
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }

    public function testCanAddToCartFromListingIfVariants(): void
    {
        $this->product['id_product_attribute'] = 42;
        $this->settings->allow_add_variant_to_cart_from_listing = true;
        $this->assertEquals(
            'http://add-to-cart.url',
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }

    public function testProductHasOnlineOnlyFlagIfItIsOnlineOnly(): void
    {
        $this->product['online_only'] = true;
        $this->assertEquals(
            ['online-only' => [
                'type' => 'online-only',
                'label' => 'some label',
            ]],
            $this->getPresentedProduct('flags')
        );
    }

    public function testProductHasDiscountFlagIfItHasADiscount(): void
    {
        $this->product['reduction'] = true;
        $this->assertEquals(
            ['discount' => [
                'type' => 'discount',
                'label' => 'some label',
            ]],
            $this->getPresentedProduct('flags')
        );
    }

    public function testProductHasBothOnSaleFlagAndDiscountFlagIfItHasADiscountAndIsOnSale(): void
    {
        $this->product['reduction'] = true;
        $this->product['on_sale'] = true;
        $this->assertEquals(
            [
                'on-sale' => [
                    'type' => 'on-sale',
                    'label' => 'some label',
                ],
                'discount' => [
                    'type' => 'discount',
                    'label' => 'some label',
                ],
            ],
            $this->getPresentedProduct('flags')
        );
    }

    public function testProductHasNewFlagIfItIsNew(): void
    {
        $this->product['new'] = true;
        $this->assertEquals(
            ['new' => [
                'type' => 'new',
                'label' => 'some label',
            ]],
            $this->getPresentedProduct('flags')
        );
    }

    public function testProductHasNewFlagIfConditionMustBeShown(): void
    {
        $this->product['show_condition'] = true;
        $this->product['condition'] = 'new';
        $this->assertEquals(
            [
                'type' => 'new',
                'label' => 'some label',
                'schema_url' => 'https://schema.org/NewCondition',
            ],
            $this->getPresentedProduct('condition')
        );
    }

    public function testProductHasNoFlagsIfNotAvailableForOrder(): void
    {
        $this->product['online_only'] = true;
        $this->product['available_for_order'] = false;
        $this->product['show_price'] = false;
        $this->assertEquals(
            [],
            $this->getPresentedProduct('flags')
        );
    }
}
