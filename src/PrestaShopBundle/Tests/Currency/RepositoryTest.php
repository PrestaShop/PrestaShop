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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Currency\Repository as CurrencyRepository;

class RepositoryTest extends TestCase
{
    /** @var CurrencyRepository */
    protected $repository;

    public function setUp()
    {
        $dataSource = $this->getMock('PrestaShopBundle\Currency\DataSource\DataSourceInterface');
        $dataSource->method('getCurrencyByIsoCode')
            ->willReturnMap($this->provideCurrencyDataMapByIsoCode());
        $this->repository = new CurrencyRepository([$dataSource]);
    }

    /**
     * Given a valid currency code
     * When requesting a currency (with this code) to the repository
     * It should return the expected Currency instance (with expected data)
     *
     * @param string $currencyCode           The valid currency ISO code
     * @param int    $expectedNumericIsoCode The expected numeric ISO code
     *
     * @dataProvider provideValidCurrencyCodes
     */
    public function testItReturnsCurrencyWithValidCode($currencyCode, $expectedNumericIsoCode)
    {
        $currency = $this->repository->getCurrencyByIsoCode($currencyCode);
        $this->assertInstanceOf('PrestaShopBundle\Currency\Currency', $currency);
        $this->assertSame($expectedNumericIsoCode, $currency->getNumericIsoCode());
    }

    /**
     * Given an invalid currency code
     * When requesting a currency (with this code) to the repository
     * It should throw an InvalidArgumentException
     *
     * @expectedException InvalidArgumentException
     */
    public function testItFailsWithUnknownCurrencyCode()
    {
        $this->repository->getCurrencyByIsoCode('random_invalid_code');
    }

    /**
     * Given a valid currency id
     * When requesting a currency (with this id) to the repository
     * It should return the expected Currency instance (with expected data)
     *
     * @param int $currencyId             The valid currency id
     * @param int $expectedNumericIsoCode The expected numeric ISO code
     *
     * @dataProvider provideValidCurrencyIds
     */
    public function testItReturnsCurrencyWithValidId($currencyId, $expectedNumericIsoCode)
    {
        // TODO when database data source is implemented

//        $currency = $this->repository->getCurrency($currencyId);
//        $this->assertInstanceOf('PrestaShopBundle\Currency\Currency', $currency);
//        $this->assertSame($expectedNumericIsoCode, $currency->getNumericIsoCode());
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * Given an invalid currency id
     * When requesting a currency (with this id) to the repository
     * It should throw an InvalidArgumentException
     *
     * @expectedException InvalidArgumentException
     */
    public function testItFailsWithUnknownCurrencyId()
    {
        $this->repository->getCurrency(0);
    }

    public function provideValidCurrencyCodes()
    {
        return array(
            'Euro'      => array('EUR', 978),
            'US Dollar' => array('USD', 840),
            'Pound'     => array('GBP', 826),
        );
    }


    public function provideValidCurrencyIds()
    {
        // TODO when database data source is implemented

        return array(
//            'Euro'      => array('EUR', 978),
//            'US Dollar' => array('USD', 840),
//            'Pound'     => array('GBP', 826),
        );
    }

    public function provideCurrencyDataMapByIsoCode()
    {
        return array(
            array(
                'EUR',
                array(
                    'isoCode'        => 'EUR',
                    'displayName'    => 'euro',
                    'symbol'         => '€',
                    'numericIsoCode' => 978,
                    'decimalDigits'  => 2,
                ),
            ),
            array(
                'USD',
                array(
                    'isoCode'        => 'USD',
                    'displayName'    => 'dollar',
                    'symbol'         => '$',
                    'numericIsoCode' => 840,
                    'decimalDigits'  => 2,
                ),
            ),
            array(
                'GBP',
                array(
                    'isoCode'        => 'GBP',
                    'displayName'    => 'livre',
                    'symbol'         => '£',
                    'numericIsoCode' => 826,
                    'decimalDigits'  => 2,
                ),
            ),
        );
    }
}
