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

namespace PrestaShopBundle\Tests\Currency;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Currency\Repository as CurrencyRepository;

class RepositoryTest extends TestCase
{
    /** @var CurrencyRepository */
    protected $repository;

    public function setUp()
    {
        $dataSource = $this->getMock('PrestaShopBundle\Currency\Repository\DataSourceInterface');
        $dataSource->method('getByIsoCode')
            ->willReturnMap($this->provideCurrencyDataMapByIsoCode());
        $this->repository = new CurrencyRepository([$dataSource]);
    }

    /**
     * @param string $currencyCode           The valid currency ISO code
     * @param int    $expectedNumericIsoCode The expected numeric ISO code
     *
     * @dataProvider provideValidCurrencyCodes
     */
    public function testGetByCurrencyCodeWhenCodeExists($currencyCode, $expectedNumericIsoCode)
    {
        $currency = $this->repository->getCurrencyByIsoCode($currencyCode);
        $this->assertInstanceOf('PrestaShopBundle\Currency\Currency', $currency);
        $this->assertSame($expectedNumericIsoCode, $currency->getNumericIsoCode());
    }

    public function testGetByCurrencyCodeWhenCodeIsUnknown()
    {
        $currency = $this->repository->getCurrencyByIsoCode('random_invalid_code');
        $this->assertNull($currency);
    }

    public function provideValidCurrencyCodes()
    {
        return array(
            'Euro'      => array('EUR', 978),
            'US Dollar' => array('USD', 840),
            'Pound'     => array('GBP', 826),
        );
    }

    public function provideCurrencyDataMapByIsoCode()
    {
        return array(
            array(
                'EUR',
                array(
                    'isoCode'          => 'EUR',
                    'numericIsoCode'   => 978,
                    'decimalDigits'    => 2,
                    'localizedNames'   => array('fr_FR' => 'euro', 'en_US' => 'euro', 'en_UK' => 'euro'),
                    'localizedSymbols' => array('fr_FR' => '€', 'en_US' => '€', 'en_UK' => '€'),
                ),
            ),
            array(
                'USD',
                array(
                    'isoCode'          => 'USD',
                    'numericIsoCode'   => 840,
                    'decimalDigits'    => 2,
                    'localizedNames'   => array('fr_FR' => 'dollar', 'en_US' => 'dollar', 'en_UK' => 'dollar'),
                    'localizedSymbols' => array('fr_FR' => '$', 'en_US' => '$', 'en_UK' => '$'),
                ),
            ),
            array(
                'GBP',
                array(
                    'isoCode'          => 'GBP',
                    'numericIsoCode'   => 826,
                    'decimalDigits'    => 2,
                    'localizedNames'   => array('fr_FR' => 'livre', 'en_US' => 'pound', 'en_UK' => 'pound'),
                    'localizedSymbols' => array('fr_FR' => '£', 'en_US' => '£', 'en_UK' => '£'),
                ),
            ),
        );
    }
}
