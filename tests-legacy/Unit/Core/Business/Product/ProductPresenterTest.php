<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Product;

use Language;
use LegacyTests\TestCase\UnitTestCase;
use Phake;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter as BasePricePresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;

class PriceFormatter extends BasePricePresenter
{
    public function convertAmount($price, $currency = null)
    {
        return $price;
    }

    public function format($price, $currency = null)
    {
        return "#$price";
    }
}

class ProductPresenterTest extends UnitTestCase
{
    /** @var ProductPresentationSettings */
    private $settings;
    private $product;
    private $language;

    protected function setUp()
    {
        parent::setUp();
        $this->settings = new ProductPresentationSettings();

        $this->settings->catalog_mode = false;
        $this->settings->restricted_country_mode = false;
        $this->settings->showPrices = true;

        $this->product = [];
        $this->product['available_for_order'] = true;
        $this->product['id_product'] = 1;
        $this->product['id_product_attribute'] = 0;
        $this->product['link_rewrite'] = 'hérisson';
        $this->product['reference'] = 'ref-herisson';
        $this->product['price'] = null;
        $this->product['price_without_reduction'] = null;
        $this->product['price_tax_exc'] = null;
        $this->product['specific_prices'] = null;
        $this->product['customizable'] = false;
        $this->product['quantity'] = 1;
        $this->product['allow_oosp'] = false;
        $this->product['online_only'] = false;
        $this->product['reduction'] = false;
        $this->product['on_sale'] = false;
        $this->product['new'] = false;
        $this->product['pack'] = false;
        $this->product['show_price'] = true;
        $this->language = new Language();
    }

    private function _presentProduct($presenterClass, $field)
    {
        $translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($translator)->trans(Phake::anyParameters())->thenReturn('some label');

        $link = Phake::mock('Link');
        Phake::when($link)->getAddToCartURL(Phake::anyParameters())->thenReturn('http://add-to-cart.url');

        $imageRetriever = Phake::mock('PrestaShop\PrestaShop\Adapter\Image\ImageRetriever');
        Phake::when($imageRetriever)->getProductImages(Phake::anyParameters())->thenReturn([
            ['id_image' => 0, 'associatedVariants' => []],
        ]);

        $presenter = new $presenterClass(
            $imageRetriever,
            $link,
            new PriceFormatter(),
            Phake::mock('PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever'),
            $translator
        );

        $product = $presenter->present(
            $this->settings,
            $this->product,
            $this->language
        );

        if (null === $field) {
            return $product;
        } else {
            return $product[$field];
        }
    }

    private function getPresentedProduct($field = null)
    {
        return $this->_presentProduct('PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter', $field);
    }

    private function getPresentedProductForListing($field = null)
    {
        return $this->_presentProduct('PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter', $field);
    }

    public function testPriceShouldNotBeShownInCatalogMode()
    {
        $this->settings->catalog_mode = true;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function testPriceShouldBeShownInCatalogModeWithPrices()
    {
        $this->settings->catalog_mode = true;
        $this->settings->catalog_mode_with_prices = true;
        $this->assertTrue($this->getPresentedProduct('show_price'));
    }

    public function testPriceShouldNotBeShownInCatalogModeWithoutPrices()
    {
        $this->settings->catalog_mode = true;
        $this->settings->catalog_mode_with_prices = false;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function testPriceShouldShownInRestrictedCountryMode()
    {
        $this->settings->restricted_country_mode = true;
        $this->assertTrue($this->getPresentedProduct('show_price'));
    }

    public function testPriceShouldNotBeShownIfProductNotAvailableForOrder()
    {
        $this->product['available_for_order'] = false;
        $this->product['show_price'] = false;

        $this->assertFalse($this->getPresentedProduct('show_price'));

        $this->product['available_for_order'] = false;
        $this->product['show_price'] = true;

        $this->assertTrue($this->getPresentedProduct('show_price'));
    }

    public function testPriceIsTaxExcluded()
    {
        $this->settings->include_taxes = false;
        $this->product['price_tax_exc'] = 8;
        $this->assertStringStartsWith(
            '#8',
            $this->getPresentedProduct('price')
        );
    }

    public function testPriceIsTaxIncluded()
    {
        $this->settings->include_taxes = true;
        $this->product['price'] = 16;
        $this->assertStringStartsWith(
            '#16',
            $this->getPresentedProduct('price')
        );
    }

    public function testCannotAddToCartIfNotCustomized()
    {
        $this->product['customization_required'] = true;
        $this->assertNotEquals(
            'http://add-to-cart.url',
            $this->getPresentedProduct('add_to_cart_url')
        );
    }

    public function testCanAddToCartIfCustomized()
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

    public function testCanAddToCartIfCustomizedAllRequiredFields()
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

    public function testCannotAddToCartFromListingIfVariants()
    {
        $this->product['id_product_attribute'] = 42;
        $this->settings->allow_add_variant_to_cart_from_listing = false;
        $this->assertNull(
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }

    public function testCannotAddToCartFromListingEvenWhenCustomized()
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

    public function testCanAddToCartFromListingIfVariants()
    {
        $this->product['id_product_attribute'] = 42;
        $this->settings->allow_add_variant_to_cart_from_listing = true;
        $this->assertEquals(
            'http://add-to-cart.url',
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }

    public function testProductHasOnlineOnlyFlagIfItIsOnlineOnly()
    {
        $this->product['online_only'] = true;
        $this->assertEquals(
            ['online-only' => [
                'type'  => 'online-only',
                'label' => 'some label',
            ]],
            $this->getPresentedProduct('flags')
        );
    }

    public function testProductHasDiscountFlagIfItHasADiscount()
    {
        $this->product['reduction'] = true;
        $this->assertEquals(
            ['discount' => [
                'type'  => 'discount',
                'label' => 'some label',
            ]],
            $this->getPresentedProduct('flags')
        );
    }

    public function testProductHasBothOnSaleFlagAndDiscountFlagIfItHasADiscountAndIsOnSale()
    {
        $this->product['reduction'] = true;
        $this->product['on_sale'] = true;
        $this->assertEquals(
            [
                'on-sale' => [
                    'type'  => 'on-sale',
                    'label' => 'some label',
                ],
                'discount' => [
                    'type'  => 'discount',
                    'label' => 'some label',
                ]
            ],
            $this->getPresentedProduct('flags')
        );
    }

    public function testProductHasNewFlagIfItIsNew()
    {
        $this->product['new'] = true;
        $this->assertEquals(
            ['new' => [
                'type'  => 'new',
                'label' => 'some label',
            ]],
            $this->getPresentedProduct('flags')
        );
    }

    public function testProductHasNewFlagIfConditionMustBeShown()
    {
        $this->product['show_condition'] = true;
        $this->product['condition'] = 'new';
        $this->assertEquals(
            [
                'type'  => 'new',
                'label' => 'some label',
                'schema_url' => 'https://schema.org/NewCondition',
            ],
            $this->getPresentedProduct('condition')
        );
    }

    public function testProductHasNoFlagsIfNotAvailableForOrder()
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
