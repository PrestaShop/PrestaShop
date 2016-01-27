<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Product;

use Phake;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use PrestaShop\PrestaShop\Core\Price\PricePresenterInterface;
use Product;
use Language;
use Link;
use Context;
use Adapter_ProductPriceCalculator;
use PrestaShop\PrestaShop\Adapter\Product\PricePresenter as BasePricePresenter;

class PricePresenter extends BasePricePresenter
{
    public function convertAmount($price)
    {
        return $price;
    }
    public function format($price)
    {
        return "#$price";
    }
}

class ProductPresenterTest extends UnitTestCase
{
    private $settings;
    private $product;
    private $language;

    public function setup()
    {
        parent::setup();
        $this->settings = new ProductPresentationSettings;

        $this->settings->catalog_mode = false;
        $this->settings->restricted_country_mode = false;

        $this->product = [];
        $this->product['show_price'] = true;
        $this->product['available_for_order'] = true;
        $this->product['id_product'] = 1;
        $this->product['id_product_attribute'] = 0;
        $this->product['link_rewrite'] = 'hérisson';
        $this->product['price'] = null;
        $this->product['price_tax_exc'] = null;
        $this->product['specific_prices'] = null;
        $this->product['customizable'] = false;
        $this->product['quantity'] = 1;
        $this->product['allow_oosp'] = false;
        $this->product['online_only'] = false;
        $this->product['reduction'] = false;
        $this->product['on_sale'] = false;
        $this->product['new'] = false;
        $this->language = new Language;
    }

    private function _presentProduct($method, $field)
    {
        $translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($translator)->l(Phake::anyParameters())->thenReturn('some label');

        $link = Phake::mock('Link');
        Phake::when($link)->getAddToCartURL(Phake::anyParameters())->thenReturn('http://add-to-cart.url');

        $imageRetriever = Phake::mock('PrestaShop\PrestaShop\Adapter\Image\ImageRetriever');
        Phake::when($imageRetriever)->getProductImages(Phake::anyParameters())->thenReturn([
            ['id_image' => 0, 'associatedVariants' => []]
        ]);

        $presenter = new ProductPresenter(
            $imageRetriever,
            $link,
            new PricePresenter,
            Phake::mock('PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever'),
            $translator
        );

        $product = $presenter->$method(
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
        return $this->_presentProduct('present', $field);
    }

    private function getPresentedProductForListing($field = null)
    {
        return $this->_presentProduct('presentForListing', $field);
    }


    public function test_price_should_not_be_shown_in_catalog_mode()
    {
        $this->product['show_price'] = true;
        $this->settings->catalog_mode = true;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function test_price_should_not_be_shown_in_restricted_country_mode()
    {
        $this->product['show_price'] = true;
        $this->settings->restricted_country_mode = true;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function test_price_should_not_be_shown_if_product_not_available_for_order()
    {
        $this->product['show_price'] = true;
        $this->product['available_for_order'] = false;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function test_price_is_tax_excluded()
    {
        $this->settings->include_taxes = false;
        $this->product['price_tax_exc'] = 8;
        $this->assertStringStartsWith(
            '#8',
            $this->getPresentedProduct('price')
        );
    }

    public function test_price_is_tax_included()
    {
        $this->settings->include_taxes = true;
        $this->product['price'] = 16;
        $this->assertStringStartsWith(
            '#16',
            $this->getPresentedProduct('price')
        );
    }

    public function test_cannot_add_to_cart_if_not_customized()
    {
        $this->product['customization_required'] = true;
        $this->assertNotEquals(
            'http://add-to-cart.url',
            $this->getPresentedProduct('add_to_cart_url')
        );
    }

    public function test_can_add_to_cart_if_customized()
    {
        $this->product['customization_required'] = true;
        $this->product['customizations'] = [
            'fields' => [
                ['is_customized' => true, 'required' => true]
            ]
        ];
        $this->assertEquals(
            'http://add-to-cart.url',
            $this->getPresentedProduct('add_to_cart_url')
        );
    }

    public function test_can_add_to_cart_if_customized_all_required_fields()
    {
        $this->product['customization_required'] = true;
        $this->product['customizations'] = [
            'fields' => [
                ['is_customized' => true, 'required' => true],
                ['is_customized' => false, 'required' => false]
            ]
        ];
        $this->assertEquals(
            'http://add-to-cart.url',
            $this->getPresentedProduct('add_to_cart_url')
        );
    }

    public function test_cannot_add_to_cart_from_listing_if_variants()
    {
        $this->product['id_product_attribute'] = 42;
        $this->settings->allow_add_variant_to_cart_from_listing = false;
        $this->assertEquals(
            null,
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }

    public function test_cannot_add_to_cart_from_listing_even_when_customized()
    {
        $this->product['customization_required'] = true;
        $this->product['customizations'] = [
            'fields' => [
                ['is_customized' => true, 'required' => true]
            ]
        ];
        $this->assertEquals(
            null,
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }

    public function test_can_add_to_cart_from_listing_if_variants()
    {
        $this->product['id_product_attribute'] = 42;
        $this->settings->allow_add_variant_to_cart_from_listing = true;
        $this->assertEquals(
            'http://add-to-cart.url',
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }

    public function test_product_has_online_only_label_if_it_is_online_only()
    {
        $this->product['online_only'] = true;
        $this->assertEquals(
            ['online-only' => [
                'type'  => 'online-only',
                'label' => 'some label'
            ]],
            $this->getPresentedProduct('labels')
        );
    }

    public function test_product_has_discount_label_if_it_has_a_discount()
    {
        $this->product['reduction'] = true;
        $this->assertEquals(
            ['discount' => [
                'type'  => 'discount',
                'label' => 'some label'
            ]],
            $this->getPresentedProduct('labels')
        );
    }

    public function test_product_has_only_on_sale_label_if_it_has_a_discount_and_is_on_sale()
    {
        $this->product['reduction'] = true;
        $this->product['on_sale'] = true;
        $this->assertEquals(
            ['on-sale' => [
                'type'  => 'on-sale',
                'label' => 'some label'
            ]],
            $this->getPresentedProduct('labels')
        );
    }

    public function test_product_has_new_label_if_it_is_new()
    {
        $this->product['new'] = true;
        $this->assertEquals(
            ['new' => [
                'type'  => 'new',
                'label' => 'some label'
            ]],
            $this->getPresentedProduct('labels')
        );
    }

    public function test_product_has_no_labels_if_not_available_for_order()
    {
        $this->product['online_only'] = true;
        $this->product['available_for_order'] = false;
        $this->assertEquals(
            [],
            $this->getPresentedProduct('labels')
        );
    }
}
