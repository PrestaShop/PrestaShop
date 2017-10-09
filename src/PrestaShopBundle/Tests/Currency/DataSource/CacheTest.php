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

namespace PrestaShopBundle\Tests\Currency\DataSource;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Currency\DataSource\Cache as CacheCurrencyDataSource;
use PrestaShopBundle\Currency\Exception\InvalidArgumentException;

class CacheTest extends TestCase
{
    /**
     * CLDR currency repository
     *
     * @var CacheCurrencyDataSource
     */
    protected $dataSource;

    /**
     * Sets up a data source that will be used by tests
     * This data source reads data from CLDR reference data files
     */
    public function setUp()
    {
        $this->dataSource = new CacheCurrencyDataSource('fr-FR');
    }

    /**
     * Given any integer id
     * When requesting to the data source a currency having this id
     * Then the result should be null (because reference CLDR data sources have no clue about internal currencies ids)
     */
    public function testGetById()
    {
        $this->assertNull($this->dataSource->getCurrencyById(1));
    }

    /**
     * Given an invalid currency id (wrong type)
     * When requesting to the data source a currency having this id
     * Then an InvalidArgumentException should be raised
     *
     * @expectedException InvalidArgumentException
     */
    public function testGetByIdWithWrongParameter()
    {
        $this->dataSource->getCurrencyById('foobar');
    }

    /**
     * Given a valid locale code (IETF tag) and a valid currency ISO code
     * When setting the used locale tag, then requesting currency data by its ISO code
     * Then the data source should return the expected currency data
     *
     * @param $currencyCode
     * @param $expectedData
     *
     * @dataProvider provideValidCurrencyData
     */
    public function testGetByIsoCode($localeCode, $currencyCode, $expectedData)
    {
        $this->dataSource->setLocaleCode($localeCode);
        $currencyData = $this->dataSource->getCurrencyByIsoCode($currencyCode);

        foreach ($expectedData as $property => $value) {
            // TODO : should be Currency instances, not plain array data
            // TODO object comparison
            $this->assertSame($value, $currencyData[$property]);
        }
    }

    /**
     * Provides valid currency data, by locale code and currency code.
     *
     * @return array
     */
    public function provideValidCurrencyData()
    {
        return array(
            // Here, no currency data in fr_FR.xml. Everything comes from fr.xml
            'fr-FR - EUR' => array(
                'localeCode'   => 'fr-FR',
                'currencyCode' => 'EUR',
                'expectedData' => array(
                    'isoCode'     => 'EUR',
                    'displayName' => array(
                        'default' => 'euro',
                        'one'     => 'euro',
                        'other'   => 'euros',
                    ),
                    'symbol'      => array(
                        'default' => '€',
                        'narrow'  => '€',
                    ),
                ),
            ),
            // Here, data comes from both en.xml and en-DK.xml files (no overriding)
            'en-DK - DKK' => array(
                'localeCode'   => 'en-DK',
                'currencyCode' => 'DKK',
                'expectedData' => array(
                    'isoCode'     => 'DKK',
                    'displayName' => array( // from en.xml
                        'default' => 'Danish Krone',
                        'one'     => 'Danish krone',
                        'other'   => 'Danish kroner',
                    ),
                    'symbol'      => array( // from en-DK.xml
                        'default' => 'kr.',
                    ),
                ),
            ),
            // In this one, default symbol from fo.xml ("kr") is overridden by fo-DK.xml ("kr.")
            // Narrow symbol stays unchanged from fo.xml
            'fo-DK - DKK' => array(
                'localeCode'   => 'fo-DK',
                'currencyCode' => 'DKK',
                'expectedData' => array(
                    'isoCode'     => 'DKK',
                    'displayName' => array( // from en.xml
                        'default' => 'donsk króna',
                        'one'     => 'donsk króna',
                        'other'   => 'danskar krónur',
                    ),
                    'symbol'      => array( // from en-DK.xml
                        'default' => 'kr.',
                        'narrow'  => 'kr',
                    ),
                ),
            ),
        );
    }
}
