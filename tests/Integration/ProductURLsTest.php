<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Tests\Integration;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use Context;
use Dispatcher;
use ReflectionClass;

class ProductURLsTest extends IntegrationTestCase
{
    private $link;
    private $language;

    protected function setUp()
    {
        parent::setUp();
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
