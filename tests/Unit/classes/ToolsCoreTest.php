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

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use PHPUnit_Framework_TestCase;
use Tools;

class ToolsCoreTest extends PHPUnit_Framework_TestCase
{
    protected function setUp() {
        $_POST = array();
        $_GET = array();
        Tools::resetRequest();
    }

    private function setPostAndGet(array $post = array(), array $get = array())
    {
        $_POST = $post;
        $_GET = $get;

        return $this;
    }

    public function testGetValueBaseCase()
    {
        $this->setPostAndGet(array('hello' => 'world'));
        $this->assertEquals('world', Tools::getValue('hello'));
    }

    public function testGetValueDefaultValueIsFalse()
    {
        $this->setPostAndGet();
        $this->assertEquals(false, Tools::getValue('hello'));
    }

    public function testGetValueUsesDefaultValue()
    {
        $this->setPostAndGet();
        $this->assertEquals('I AM DEFAULT', Tools::getValue('hello', 'I AM DEFAULT'));
    }

    public function testGetValuePrefersPost()
    {
        $this->setPostAndGet(array('hello' => 'world'), array('hello' => 'cruel world'));
        $this->assertEquals('world', Tools::getValue('hello'));
    }

    public function testGetValueAcceptsOnlyTruthyStringsAsKeys()
    {
        $this->setPostAndGet(array(
            '' => true,
            ' ' => true,
            null => true
        ));

        $this->assertEquals(false, Tools::getValue('', true));
        $this->assertEquals(true, Tools::getValue(' '));
        $this->assertEquals(false, Tools::getValue(null, true));
    }

    public function testGetValueStripsNullCharsFromReturnedStringsExamples()
    {
        return array(
            array("\0", ''),
            array("haxx\0r", 'haxxr'),
            array("haxx\0\0\0r", 'haxxr'),
        );
    }

    /**
     * @dataProvider testGetValueStripsNullCharsFromReturnedStringsExamples
     */
    public function testGetValueStripsNullCharsFromReturnedStrings($rawString, $cleanedString)
    {
        /**
         * Check it cleans values stored in POST
         */
        $this->setPostAndGet(array('rawString' => $rawString));
        $this->assertEquals($cleanedString, Tools::getValue('rawString'));

        /**
         * Check it cleans values stored in GET
         */
        $this->setPostAndGet(array(), array('rawString' => $rawString));
        $this->assertEquals($cleanedString, Tools::getValue('rawString'));

        /**
         * Check it cleans default values too
         */
        $this->setPostAndGet();
        $this->assertEquals($cleanedString, Tools::getValue('NON EXISTING KEY', $rawString));
    }

    public function testSpreadAmountExamples()
    {
        return array(
            array(
                // base case
                array(array('a' => 2), array('a' => 1)), // expected result
                1, 0,                                     // amount and precision
                array(array('a' => 1), array('a' => 1)), // source rows
                'a'                                         // sort column
            ),
            array(
                // check with 1 decimal
                array(array('a' => 1.5), array('a' => 1.5)),
                1, 1,
                array(array('a' => 1), array('a' => 1)),
                'a'
            ),
            array(
                // 2 decimals, but only one really needed
                array(array('a' => 1.5), array('a' => 1.5)),
                1, 2,
                array(array('a' => 1), array('a' => 1)),
                'a'
            ),
            array(
                // check that the biggest "a" gets the adjustment
                array(array('a' => 3), array('a' => 1)),
                1, 0,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // check it works with amount > count($rows)
                array(array('a' => 4), array('a' => 2)),
                3, 0,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // 2 decimals
                array(array('a' => 2.01), array('a' => 1)),
                0.01, 2,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // 2 decimals, equal level of adjustment
                array(array('a' => 2.01), array('a' => 1.01)),
                0.02, 2,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // 2 decimals, different levels of adjustmnt
                array(array('a' => 2.02), array('a' => 1.01)),
                0.03, 2,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // check associative arrays are OK too
                array(array('a' => 2.01), array('a' => 1.01)),
                0.02, 2,
                array('z' => array('a' => 1), 'x' => array('a' => 2)),
                'a'
            ),
            array(
                // check amount is rounded if it needs more precision than asked for
                array(array('a' => 2.02), array('a' => 1.01)),
                0.025, 2,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                array(array('a' => 7.69), array('a' => 4.09), array('a' => 1.8)),
                -0.32, 2,
                array(array('a' => 7.8), array('a' => 4.2), array('a' => 1.9)),
                'a'
            )
        );
    }

    /**
     * @dataProvider testSpreadAmountExamples
     */
    public function testSpreadAmount($expectedRows, $amount, $precision, $rows, $column)
    {
        Tools::spreadAmount($amount, $precision, $rows, $column);
        $this->assertEquals(array_values($expectedRows), array_values($rows));
    }

    /**
     * @return array of example taken from the installation of PrestaShop
     */
    public function testCamelCaseExample()
    {
        return array(
            array('address_format', 'addressFormat', false),
            array('attachment_lang', 'attachmentLang', false),
            array('attribute_group', 'attributeGroup', false),
            array('attribute_group_lang', 'attributeGroupLang', false),
            array('attribute_lang', 'attributeLang', false),
            array('carrier', 'carrier', false),
            array('carrier_group', 'carrierGroup', false),
            array('carrier_lang', 'carrierLang', false),
            array('carrier_tax_rules_group_shop', 'carrierTaxRulesGroupShop', false),
            array('carrier_zone', 'carrierZone', false),
            array('cart_product', 'cartProduct', false),
            array('cart_rule_lang', 'cartRuleLang', false),
            array('category_group', 'categoryGroup', false),
            array('category_lang', 'categoryLang', false),
            array('category_product', 'categoryProduct', false),
            array('cms_category', 'cmsCategory', false),
            array('cms_category_lang', 'cmsCategoryLang', false),
            array('cms_lang', 'cmsLang', false),
            array('cms_role', 'cmsRole', false),
            array('cms_role_lang', 'cmsRoleLang', false),
            array('configuration_kpi_lang', 'configurationKpiLang', false),
            array('configuration_lang', 'configurationLang', false),
            array('contact', 'contact', false),
            array('contact_lang', 'contactLang', false),
            array('country', 'country', false),
            array('country_lang', 'countryLang', false),
            array('customization_field_lang', 'customizationFieldLang', false),
            array('feature_lang', 'featureLang', false),
            array('feature_product', 'featureProduct', false),
            array('feature_value', 'featureValue', false),
            array('feature_value_lang', 'featureValueLang', false),
            array('gamificationtasks', 'gamificationtasks', false),
            array('gender_lang', 'genderLang', false),
            array('group_lang', 'groupLang', false),
            array('image_lang', 'imageLang', false),
            array('manufacturer_lang', 'manufacturerLang', false),
            array('meta_lang', 'metaLang', false),
            array('operating_system', 'operatingSystem', false),
            array('order_carrier', 'orderCarrier', false),
            array('order_detail', 'orderDetail', false),
            array('order_history', 'orderHistory', false),
            array('order_message', 'orderMessage', false),
            array('order_message_lang', 'orderMessageLang', false),
            array('order_return_state', 'orderReturnState', false),
            array('order_return_state_lang', 'orderReturnStateLang', false),
            array('order_state', 'orderState', false),
            array('order_state_lang', 'orderStateLang', false),
            array('product_attribute', 'productAttribute', false),
            array('product_attribute_combination', 'productAttributeCombination', false),
            array('product_attribute_image', 'productAttributeImage', false),
            array('product_lang', 'productLang', false),
            array('product_supplier', 'productSupplier', false),
            array('profile_lang', 'profileLang', false),
            array('quick_access', 'quickAccess', false),
            array('quick_access_lang', 'quickAccessLang', false),
            array('range_price', 'rangePrice', false),
            array('range_weight', 'rangeWeight', false),
            array('risk_lang', 'riskLang', false),
            array('search_engine', 'searchEngine', false),
            array('specific_price', 'specificPrice', false),
            array('stock_available', 'stockAvailable', false),
            array('stock_mvt_reason', 'stockMvtReason', false),
            array('stock_mvt_reason_lang', 'stockMvtReasonLang', false),
            array('store_lang', 'storeLang', false),
            array('supplier_lang', 'supplierLang', false),
            array('supply_order_state', 'supplyOrderState', false),
            array('supply_order_state_lang', 'supplyOrderStateLang', false),
            array('tab', 'tab', false),
            array('tax_lang', 'taxLang', false),
            array('warehouse', 'warehouse', false),
            array('web_browser', 'webBrowser', false),
            array('zone', 'zone', false),
            // True
            array('supplier_lang', 'SupplierLang', true),
            array('supply_order_state', 'SupplyOrderState', true),
            array('supply_order_state_lang', 'SupplyOrderStateLang', true),
            array('tab', 'Tab', true),
        );
    }

    /**
     * @dataProvider testCamelCaseExample
     */
    public function testToCamelCase($source, $expected, $firstCharUpperCase)
    {
        $actual = Tools::toCamelCase($source, $firstCharUpperCase);
        $this->assertEquals($expected, $actual, "Expected $source to be $expected in camel case, got $actual instead.");
    }

    public static function tearDownAfterClass() {
        $_POST = array();
        $_GET = array();
    }

    /**
     * @dataProvider testStrReplaceFirstProvider
     */
    public function testStrReplaceFirst($search, $replace, $subject, $cur, $expected) {
        $this->assertEquals($expected, Tools::StrReplaceFirst($search, $replace, $subject, $cur));
    }

    /**
     * @param string $url
     * @param string $expectedDomain
     *
     * @dataProvider domainProvider
     */
    public function testExtractUrlDomain($url, $expectedDomain)
    {
        $this->assertSame($expectedDomain, Tools::extractHost($url));
    }

    public function domainProvider()
    {
        return [
            ['http://example.com:80#@google.com/', 'example.com'],
            ['http://example.com:80?@google.com/', 'example.com'],
            ['http://example.com#@google.com/', 'example.com'],
            ['http://example.com?@google.com/', 'example.com'],
            ['https://example.com:80#@google.com/', 'example.com'],
            ['https://example.com:80?@google.com/', 'example.com'],
            ['http://example.com:80/', 'example.com'],
            ['http://example.com/', 'example.com'],
            ['https://example.com/', 'example.com'],
            ['http://foo@bar.com:yolo@example.com:80/foo', 'example.com'],
            ['https://example.com/', 'example.com'],
            ['ttp://example.com:80#@google.com/', 'example.com'],
            ['example.com:80#@google.com/', ''],
            ['example.com:80?@google.com/', ''],
            ['example.com#@google.com/', ''],
            ['example.com?@google.com/', ''],
            ['example.com:80#@google.com/', ''],
            ['example.com:80?@google.com/', ''],
            ['example.com:80/', ''],
            ['example.com/', ''],
            ['example.com/', ''],
            ['foo@bar.com:yolo@example.com:80/foo', ''],
            ['example.com/', ''],
            ['/blah/bleh', ''],
            ['/plop.html', ''],
        ];
    }

    public function testStrReplaceFirstProvider() {
        return [
            ['s', 'f', 'seed', 0, 'feed'],
            ['s', 'f', 'seed', 1, 'seed'],
            ['e', 'o', 'feed', 0, 'foed'],
            ['e', 'o', 'feed', 1, 'foed'],
            ['e', 'o', 'feed', 2, 'feod'],
        ];
    }
}
