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

class PricePresenter implements PricePresenterInterface
{
    public function convertAmount($price)
    {
        return 2 * $price;
    }
    public function format($price)
    {
        return "#$price";
    }
}

class PriceCalculator extends Adapter_ProductPriceCalculator
{
    public function getProductPrice(
        $id_product,
        $usetax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1,
        $force_associated_tax = false,
        $id_customer = null,
        $id_cart = null,
        $id_address = null,
        &$specific_price_output = null,
        $with_ecotax = true,
        $use_group_reduction = true,
        Context $context = null,
        $use_customer_price = true
    ) {
        if ($usetax) {
            return 8;
        } else {
            return 4;
        }
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

        $this->product = new Product;
        $this->product->show_price = true;
        $this->product->available_for_order = true;
        $this->language = new Language;
    }

    private function getPresentedProduct($field = null)
    {
        $presenter = new ProductPresenter(
            new PriceCalculator,
            Phake::mock('Adapter_ImageRetriever'),
            Phake::mock('Link'),
            new PricePresenter
        );
        $product = $presenter->present(
            $this->settings,
            $this->product,
            null,
            $this->language
        );

        if (null === $field) {
            return $product;
        } else {
            return $product[$field];
        }
    }

    public function test_price_should_not_be_shown_in_catalog_mode()
    {
        $this->product->show_price = true;
        $this->settings->catalog_mode = true;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function test_price_should_not_be_shown_in_restricted_country_mode()
    {
        $this->product->show_price = true;
        $this->settings->restricted_country_mode = true;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function test_price_should_not_be_shown_if_product_not_available_for_order()
    {
        $this->product->show_price = true;
        $this->product->available_for_order = false;
        $this->assertFalse($this->getPresentedProduct('show_price'));
    }

    public function test_price_is_tax_excluded()
    {
        $this->settings->include_taxes = false;
        $this->assertStringStartsWith(
            '#8',
            $this->getPresentedProduct('price')
        );
    }

    public function test_price_is_tax_included()
    {
        $this->settings->include_taxes = true;
        $this->assertStringStartsWith(
            '#16',
            $this->getPresentedProduct('price')
        );
    }
}
