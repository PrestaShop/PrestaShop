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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace LegacyTests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use Tools;

class ToolsCoreTest extends TestCase
{
    const PS_ROUND_UP = 0;
    const PS_ROUND_DOWN = 1;
    const PS_ROUND_HALF_UP = 2;
    const PS_ROUND_HALF_DOWN = 3;
    const PS_ROUND_HALF_EVEN = 4;
    const PS_ROUND_HALF_ODD = 5;

    protected function setUp()
    {
        $_POST = [];
        $_GET = [];
        Tools::resetRequest();

        if (!defined('PS_ROUND_UP')) {
            define('PS_ROUND_UP', self::PS_ROUND_UP);
        }
        if (!defined('PS_ROUND_DOWN')) {
            define('PS_ROUND_DOWN', self::PS_ROUND_DOWN);
        }
        if (!defined('PS_ROUND_HALF_UP')) {
            define('PS_ROUND_HALF_UP', self::PS_ROUND_HALF_UP);
        }
        if (!defined('PS_ROUND_HALF_DOWN')) {
            define('PS_ROUND_HALF_DOWN', self::PS_ROUND_HALF_DOWN);
        }
        if (!defined('PS_ROUND_HALF_EVEN')) {
            define('PS_ROUND_HALF_EVEN', self::PS_ROUND_HALF_EVEN);
        }
        if (!defined('PS_ROUND_HALF_ODD')) {
            define('PS_ROUND_HALF_ODD', self::PS_ROUND_HALF_ODD);
        }
    }

    private function setPostAndGet(array $post = [], array $get = [])
    {
        $_POST = $post;
        $_GET = $get;

        return $this;
    }

    public function testGetValueBaseCase()
    {
        $this->setPostAndGet(['hello' => 'world']);
        $this->assertEquals('world', Tools::getValue('hello'));
    }

    public function testGetValueDefaultValueIsFalse()
    {
        $this->setPostAndGet();
        $this->assertFalse(Tools::getValue('hello'));
    }

    public function testGetValueUsesDefaultValue()
    {
        $this->setPostAndGet();
        $this->assertEquals('I AM DEFAULT', Tools::getValue('hello', 'I AM DEFAULT'));
    }

    public function testGetValuePrefersPost()
    {
        $this->setPostAndGet(['hello' => 'world'], ['hello' => 'cruel world']);
        $this->assertEquals('world', Tools::getValue('hello'));
    }

    public function testGetValueAcceptsOnlyTruthyStringsAsKeys()
    {
        $this->setPostAndGet([
            '' => true,
            ' ' => true,
            null => true,
        ]);

        $this->assertFalse(Tools::getValue('', true));
        $this->assertTrue(Tools::getValue(' '));
        $this->assertFalse(Tools::getValue(null, true));
    }

    public function getValueStripsNullCharsFromReturnedStringsExamples()
    {
        return [
            ["\0", ''],
            ["haxx\0r", 'haxxr'],
            ["haxx\0\0\0r", 'haxxr'],
            ['1234\5678', '1234\5678'],
        ];
    }

    /**
     * @dataProvider getValueStripsNullCharsFromReturnedStringsExamples
     */
    public function testGetValueStripsNullCharsFromReturnedStrings($rawString, $cleanedString)
    {
        /*
         * Check it cleans values stored in POST
         */
        $this->setPostAndGet(['rawString' => $rawString]);
        $this->assertEquals($cleanedString, Tools::getValue('rawString'));

        /*
         * Check it cleans values stored in GET
         */
        $this->setPostAndGet([], ['rawString' => $rawString]);
        $this->assertEquals($cleanedString, Tools::getValue('rawString'));

        /*
         * Check it cleans default values too
         */
        $this->setPostAndGet();
        $this->assertEquals($cleanedString, Tools::getValue('NON EXISTING KEY', $rawString));
    }

    public function spreadAmountExamples()
    {
        return [
            [
                // base case
                [['a' => 2], ['a' => 1]], // expected result
                1, 0,                                     // amount and precision
                [['a' => 1], ['a' => 1]], // source rows
                'a',                                         // sort column
            ],
            [
                // check with 1 decimal
                [['a' => 1.5], ['a' => 1.5]],
                1, 1,
                [['a' => 1], ['a' => 1]],
                'a',
            ],
            [
                // 2 decimals, but only one really needed
                [['a' => 1.5], ['a' => 1.5]],
                1, 2,
                [['a' => 1], ['a' => 1]],
                'a',
            ],
            [
                // check that the biggest "a" gets the adjustment
                [['a' => 3], ['a' => 1]],
                1, 0,
                [['a' => 1], ['a' => 2]],
                'a',
            ],
            [
                // check it works with amount > count($rows)
                [['a' => 4], ['a' => 2]],
                3, 0,
                [['a' => 1], ['a' => 2]],
                'a',
            ],
            [
                // 2 decimals
                [['a' => 2.01], ['a' => 1]],
                0.01, 2,
                [['a' => 1], ['a' => 2]],
                'a',
            ],
            [
                // 2 decimals, equal level of adjustment
                [['a' => 2.01], ['a' => 1.01]],
                0.02, 2,
                [['a' => 1], ['a' => 2]],
                'a',
            ],
            [
                // 2 decimals, different levels of adjustmnt
                [['a' => 2.02], ['a' => 1.01]],
                0.03, 2,
                [['a' => 1], ['a' => 2]],
                'a',
            ],
            [
                // check associative arrays are OK too
                [['a' => 2.01], ['a' => 1.01]],
                0.02, 2,
                ['z' => ['a' => 1], 'x' => ['a' => 2]],
                'a',
            ],
            [
                // check amount is rounded if it needs more precision than asked for
                [['a' => 2.02], ['a' => 1.01]],
                0.025, 2,
                [['a' => 1], ['a' => 2]],
                'a',
            ],
            [
                [['a' => 7.69], ['a' => 4.09], ['a' => 1.8]],
                -0.32, 2,
                [['a' => 7.8], ['a' => 4.2], ['a' => 1.9]],
                'a',
            ],
        ];
    }

    /**
     * @dataProvider dirProvider
     */
    public function testGetDirectories($path, $haveFiles)
    {
        $res1 = Tools::getDirectoriesWithGlob($path);
        $res2 = Tools::getDirectoriesWithReaddir($path);
        sort($res1);
        sort($res2);
        $this->assertEquals(
            $res1,
            $res2,
            'Results differ between getDirectoriesWithGlob and getDirectoriesWithReaddir for path ' . $path
        );

        $haveFilesTest = ($res1 !== []);

        $this->assertEquals($haveFiles, $haveFilesTest);
    }

    public function dirProvider()
    {
        return [[__DIR__, true], [__FILE__, false], ['dontexists', false]];
    }

    /**
     * @dataProvider spreadAmountExamples
     */
    public function testSpreadAmount($expectedRows, $amount, $precision, $rows, $column)
    {
        Tools::spreadAmount($amount, $precision, $rows, $column);
        $this->assertEquals(array_values($expectedRows), array_values($rows));
    }

    /**
     * @return array of example taken from the installation of PrestaShop
     */
    public function getCamelCaseExample()
    {
        return [
            ['address_format', 'addressFormat', false],
            ['attachment_lang', 'attachmentLang', false],
            ['attribute_group', 'attributeGroup', false],
            ['attribute_group_lang', 'attributeGroupLang', false],
            ['attribute_lang', 'attributeLang', false],
            ['carrier', 'carrier', false],
            ['carrier_group', 'carrierGroup', false],
            ['carrier_lang', 'carrierLang', false],
            ['carrier_tax_rules_group_shop', 'carrierTaxRulesGroupShop', false],
            ['carrier_zone', 'carrierZone', false],
            ['cart_product', 'cartProduct', false],
            ['cart_rule_lang', 'cartRuleLang', false],
            ['category_group', 'categoryGroup', false],
            ['category_lang', 'categoryLang', false],
            ['category_product', 'categoryProduct', false],
            ['cms_category', 'cmsCategory', false],
            ['cms_category_lang', 'cmsCategoryLang', false],
            ['cms_lang', 'cmsLang', false],
            ['cms_role', 'cmsRole', false],
            ['cms_role_lang', 'cmsRoleLang', false],
            ['configuration_kpi_lang', 'configurationKpiLang', false],
            ['configuration_lang', 'configurationLang', false],
            ['contact', 'contact', false],
            ['contact_lang', 'contactLang', false],
            ['country', 'country', false],
            ['country_lang', 'countryLang', false],
            ['customization_field_lang', 'customizationFieldLang', false],
            ['feature_lang', 'featureLang', false],
            ['feature_product', 'featureProduct', false],
            ['feature_value', 'featureValue', false],
            ['feature_value_lang', 'featureValueLang', false],
            ['gamificationtasks', 'gamificationtasks', false],
            ['gender_lang', 'genderLang', false],
            ['group_lang', 'groupLang', false],
            ['image_lang', 'imageLang', false],
            ['manufacturer_lang', 'manufacturerLang', false],
            ['meta_lang', 'metaLang', false],
            ['operating_system', 'operatingSystem', false],
            ['order_carrier', 'orderCarrier', false],
            ['order_detail', 'orderDetail', false],
            ['order_history', 'orderHistory', false],
            ['order_message', 'orderMessage', false],
            ['order_message_lang', 'orderMessageLang', false],
            ['order_return_state', 'orderReturnState', false],
            ['order_return_state_lang', 'orderReturnStateLang', false],
            ['order_state', 'orderState', false],
            ['order_state_lang', 'orderStateLang', false],
            ['product_attribute', 'productAttribute', false],
            ['product_attribute_combination', 'productAttributeCombination', false],
            ['product_attribute_image', 'productAttributeImage', false],
            ['product_lang', 'productLang', false],
            ['product_supplier', 'productSupplier', false],
            ['profile_lang', 'profileLang', false],
            ['quick_access', 'quickAccess', false],
            ['quick_access_lang', 'quickAccessLang', false],
            ['range_price', 'rangePrice', false],
            ['range_weight', 'rangeWeight', false],
            ['risk_lang', 'riskLang', false],
            ['search_engine', 'searchEngine', false],
            ['specific_price', 'specificPrice', false],
            ['stock_available', 'stockAvailable', false],
            ['stock_mvt_reason', 'stockMvtReason', false],
            ['stock_mvt_reason_lang', 'stockMvtReasonLang', false],
            ['store_lang', 'storeLang', false],
            ['supplier_lang', 'supplierLang', false],
            ['supply_order_state', 'supplyOrderState', false],
            ['supply_order_state_lang', 'supplyOrderStateLang', false],
            ['tab', 'tab', false],
            ['tax_lang', 'taxLang', false],
            ['warehouse', 'warehouse', false],
            ['web_browser', 'webBrowser', false],
            ['zone', 'zone', false],
            // True
            ['supplier_lang', 'SupplierLang', true],
            ['supply_order_state', 'SupplyOrderState', true],
            ['supply_order_state_lang', 'SupplyOrderStateLang', true],
            ['tab', 'Tab', true],
        ];
    }

    /**
     * @dataProvider getCamelCaseExample
     */
    public function testToCamelCase($source, $expected, $firstCharUpperCase)
    {
        $actual = Tools::toCamelCase($source, $firstCharUpperCase);
        $this->assertEquals($expected, $actual, "Expected ${source} to be ${expected} in camel case, got ${actual} instead.");
    }

    public static function tearDownAfterClass()
    {
        $_POST = [];
        $_GET = [];
    }

    /**
     * @dataProvider getStrReplaceFirstProvider
     */
    public function testStrReplaceFirst($search, $replace, $subject, $cur, $expected)
    {
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

    public function getStrReplaceFirstProvider()
    {
        return [
            ['s', 'f', 'seed', 0, 'feed'],
            ['s', 'f', 'seed', 1, 'seed'],
            ['e', 'o', 'feed', 0, 'foed'],
            ['e', 'o', 'feed', 1, 'foed'],
            ['e', 'o', 'feed', 2, 'feod'],
        ];
    }

    /**
     * @dataProvider getRoundingHelperSamples
     */
    public function testRoundHelper(float $expectedResult, float $value, int $mode)
    {
        $this->assertSame($expectedResult, Tools::round_helper($value, $mode));
    }

    /**
     * @dataProvider getMathRoundSamples
     */
    public function testMathRound(float $expectedResult, float $value, int $precision, int $mode)
    {
        $this->assertSame($expectedResult, Tools::math_round($value, $precision, $mode));
    }

    /**
     * @dataProvider getFloorfSamples
     */
    public function testFloorf(float $expectedResult, float $value, int $precision)
    {
        $this->assertSame($expectedResult, Tools::floorf($value, $precision));
    }

    /**
     * @dataProvider getCeilfSamples
     */
    public function testCeilf(float $expectedResult, float $value, int $precision)
    {
        $this->assertSame($expectedResult, Tools::ceilf($value, $precision));
    }

    public function getRoundingHelperSamples()
    {
        return [
            [25, 25.32, self::PS_ROUND_UP],
            [26, 25.52, self::PS_ROUND_UP],
            [25, 25.32, self::PS_ROUND_HALF_DOWN],
            [25, 25.50, self::PS_ROUND_HALF_DOWN],
            [25, 25.32, self::PS_ROUND_HALF_EVEN],
            [26, 25.50, self::PS_ROUND_HALF_EVEN],
            [25, 25.32, self::PS_ROUND_HALF_ODD],
            [25, 25.50, self::PS_ROUND_HALF_ODD],
            [26, 25.51, self::PS_ROUND_HALF_ODD],
            [25, 25.49, self::PS_ROUND_HALF_ODD],
        ];
    }

    public function getMathRoundSamples()
    {
        return [
            // 0 precision
            [25, 25.32, 0, self::PS_ROUND_UP],
            [26, 25.52, 0, self::PS_ROUND_UP],
            [25, 25.32, 0, self::PS_ROUND_HALF_DOWN],
            [25, 25.50, 0, self::PS_ROUND_HALF_DOWN],
            [25, 25.32, 0, self::PS_ROUND_HALF_EVEN],
            [26, 25.50, 0, self::PS_ROUND_HALF_EVEN],
            [25, 25.32, 0, self::PS_ROUND_HALF_ODD],
            [25, 25.50, 0, self::PS_ROUND_HALF_ODD],
            [26, 25.51, 0, self::PS_ROUND_HALF_ODD],
            [25, 25.49, 0, self::PS_ROUND_HALF_ODD],
            // 2 precision
            [25.32, 25.321, 2, self::PS_ROUND_UP],
            [25.53, 25.525, 2, self::PS_ROUND_UP],
            [25.32, 25.325, 2, self::PS_ROUND_HALF_DOWN],
            [25.5, 25.505, 2, self::PS_ROUND_HALF_DOWN],
            [25.32, 25.325, 2, self::PS_ROUND_HALF_EVEN],
            [25.5, 25.505, 2, self::PS_ROUND_HALF_EVEN],
            [25.33, 25.325, 2, self::PS_ROUND_HALF_ODD],
            [25.51, 25.505, 2, self::PS_ROUND_HALF_ODD],
            [25.51, 25.515, 2, self::PS_ROUND_HALF_ODD],
            [25.49, 25.495, 2, self::PS_ROUND_HALF_ODD],
        ];
    }

    public function getFloorfSamples()
    {
        return [
            [25, 25.32, 0],
            [25.3, 25.32, 1],
            [25.32, 25.32, 2],
        ];
    }

    public function getCeilfSamples()
    {
        return [
            [26, 25.32, 0],
            [25.4, 25.32, 1],
            [25.32, 25.32, 2],
            [25.33, 25.325, 2],
        ];
    }
}
