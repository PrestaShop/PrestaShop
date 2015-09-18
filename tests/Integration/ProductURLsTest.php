<?php

namespace PrestaShop\PrestaShop\Tests\Integration;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use Context;
use Dispatcher;
use ReflectionClass;

class ProductURLsTest extends IntegrationTestCase
{
    private $link;
    private $language;

    public function setup()
    {
        parent::setup();
        $context = Context::getContext();
        $this->link = $context->link;
        $this->language = $context->language;
    }

    private function enableURLRewriting($yesOrNo = true)
    {
        $refl = new ReflectionClass('Dispatcher');
        $prop = $refl->getProperty('use_routes');
        $prop->setAccessible(true);
        $prop->setValue(Dispatcher::getInstance(), $yesOrNo);
    }

    private function disableURLRewriting($yesOrNo = true)
    {
        return $this->enableURLRewriting(!$yesOrNo);
    }

    private function getURL($id_product, $id_product_attribute)
    {
        $url = $this->link->getProductLink(
            $id_product,
            null, null, null,
            $this->language->id,
            null,
            $id_product_attribute,
            false,
            false,
            true
        );

        $parts = parse_url($url);

        return $parts;
    }

    public function test_url_takes_variant_into_account__with_url_rewriting()
    {
        $this->enableURLRewriting();
        $filename = basename($this->getURL(1, 6)['path']);

        $this->assertEquals(
            '1-6-faded-short-sleeves-tshirt.html',
            $filename
        );
    }

    public function test_url_ignores_variant_if_not_specified__with_url_rewriting()
    {
        $this->enableURLRewriting();
        $filename = basename($this->getURL(1, null)['path']);

        $this->assertEquals(
            '1-faded-short-sleeves-tshirt.html',
            $filename
        );
    }

    public function test_url_takes_variant_into_account__without_url_rewriting()
    {
        $this->disableURLRewriting();
        $query = [];
        parse_str($this->getURL(1, 6)['query'], $query);

        $this->assertEquals(1, $query['id_product']);
        $this->assertEquals(6, $query['id_product_attribute']);
    }

    public function test_url_ignores_variant_if_not_specified__without_url_rewriting()
    {
        $this->disableURLRewriting();
        $query = [];
        parse_str($this->getURL(1, null)['query'], $query);

        $this->assertEquals(1, $query['id_product']);
        $this->assertTrue(empty($query['id_product_attribute']));
    }
}
