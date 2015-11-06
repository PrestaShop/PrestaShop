<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Business\Product;

use Phake;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Business\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Business\Product\ProductPresentationSettings;
use PrestaShop\PrestaShop\Core\Business\Price\PricePresenterInterface;
use Product;
use Language;
use Link;
use Context;
use Adapter_ProductPriceCalculator;
use Adapter_PricePresenter;

class PricePresenter extends Adapter_PricePresenter
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
        $this->product['quantity'] = 0;
        $this->product['allow_oosp'] = false;
        $this->language = new Language;
    }

    private function _presentProduct($method, $field)
    {
        $presenter = new ProductPresenter(
            Phake::mock('Adapter_ImageRetriever'),
            Phake::mock('Link'),
            new PricePresenter
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

    public function test_cannot_add_to_cart_from_listing_if_variants()
    {
        $this->settings->allow_add_variant_to_cart_from_listing = false;
        $this->assertEquals(
            null,
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }

    public function test_can_add_to_cart_from_listing_if_variants()
    {
        $this->product['add_to_cart_url'] = 'yay';
        $this->settings->allow_add_variant_to_cart_from_listing = true;
        $this->assertNotEquals(
            'yay',
            $this->getPresentedProductForListing('add_to_cart_url')
        );
    }
}
