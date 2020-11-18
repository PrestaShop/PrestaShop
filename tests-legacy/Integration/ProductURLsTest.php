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

namespace LegacyTests\Integration;

use Context;
use Dispatcher;
use LegacyTests\TestCase\IntegrationTestCase;
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
            null,
            null,
            null,
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

    public function testUrlTakesVariantIntoAccountWithUrlRewriting()
    {
        $this->enableURLRewriting();
        $filename = basename($this->getURL(1, 2)['path']);

        $this->assertEquals(
            '1-2-hummingbird-printed-t-shirt.html',
            $filename
        );
    }

    public function testUrlIgnoresVariantIfNotSpecifiedWithUrlRewriting()
    {
        $this->enableURLRewriting();
        $filename = basename($this->getURL(1, null)['path']);

        $this->assertEquals(
            '1-hummingbird-printed-t-shirt.html',
            $filename
        );
    }

    public function testUrlTakesVariantIntoAccountWithoutUrlRewriting()
    {
        $this->disableURLRewriting();
        $query = [];
        parse_str($this->getURL(1, 6)['query'], $query);

        $this->assertEquals(1, $query['id_product']);
        $this->assertEquals(6, $query['id_product_attribute']);
    }

    public function testUrlIgnoresVariantIfNotSpecifiedWithoutUrlRewriting()
    {
        $this->disableURLRewriting();
        $query = [];
        parse_str($this->getURL(1, null)['query'], $query);

        $this->assertEquals(1, $query['id_product']);
        $this->assertArrayNotHasKey('id_product_attribute', $query);
    }
}
