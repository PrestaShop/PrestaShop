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
use PrestaShopBundle\Currency\Manager;
use stdClass;

class ManagerTest extends TestCase
{
    const CONTEXT_LOCALE_CODE            = 'fr-FR';
    const STUB_INSTALLED_CURRENCY_ID     = 1;
    const STUB_UNINSTALLED_CURRENCY_ID   = 999;
    const STUB_INSTALLED_CURRENCY_CODE   = 'FOO';
    const STUB_UNINSTALLED_CURRENCY_CODE = 'BAR';

    /**
     * @var Manager
     */
    protected $manager;
    protected $mockedCurrencyById;
    protected $mockedInstalledCurrencyByCode;
    protected $mockedUninstalledCurrencyByCode;

    public function setUp()
    {
        // No behavior to be mocked for currencies. We just need to test if this very objects are retrieved at the end.
        $this->mockedCurrencyById              = (object)array('id' => self::STUB_INSTALLED_CURRENCY_ID);
        $this->mockedInstalledCurrencyByCode   = (object)array('isoCode' => self::STUB_INSTALLED_CURRENCY_CODE);
        $this->mockedUninstalledCurrencyByCode = (object)array('isoCode' => self::STUB_UNINSTALLED_CURRENCY_CODE);

        $installedCurrencyRepository = $this->getMock(
            'PrestaShopBundle\Currency\Repository\Installed\InstalledRepositoryInterface'
        );
        $installedCurrencyRepository->method('getInstalledCurrencyById')
            ->willReturnMap(array(
                array(
                    self::STUB_INSTALLED_CURRENCY_ID, // When asking for a valid installed id
                    self::CONTEXT_LOCALE_CODE,
                    $this->mockedCurrencyById,        // then the wanted currency is returned
                ),
                array(
                    self::STUB_UNINSTALLED_CURRENCY_ID, // When asking for an unknown id
                    self::CONTEXT_LOCALE_CODE,
                    null,                               // Then null is returned
                ),
            ));
        $installedCurrencyRepository->method('getInstalledCurrencyByIsoCode')
            ->willReturnMap(array(
                array(
                    self::STUB_INSTALLED_CURRENCY_CODE,   // When asking for a valid installed code
                    self::CONTEXT_LOCALE_CODE,
                    $this->mockedInstalledCurrencyByCode, // then the wanted currency is returned
                ),
                array(
                    self::STUB_UNINSTALLED_CURRENCY_CODE, // When asking for an unknown code
                    self::CONTEXT_LOCALE_CODE,
                    null,                                 // Then null is returned
                ),
            ));

        $referenceCurrencyRepository = $this->getMock(
            'PrestaShopBundle\Currency\Repository\Reference\ReferenceRepositoryInterface'
        );
        $referenceCurrencyRepository->method('getReferenceCurrencyByIsoCode')
            ->with(self::STUB_UNINSTALLED_CURRENCY_CODE)// When asking for a valid currency code
            ->willReturn($this->mockedUninstalledCurrencyByCode); // A valid currency is returned from reference data

        $this->manager = new Manager($installedCurrencyRepository, $referenceCurrencyRepository);
    }

    /**
     * Given a valid currency id
     * When asking a currency (with this id) to the currency manager
     * Then it should return the expected Currency object
     *
     * This test is about the manager being able to use the good method from the good repository to retrieve the wanted
     * currency.
     */
    public function testGetCurrencyById()
    {
        $currency = $this->manager->getCurrencyById(
            self::STUB_INSTALLED_CURRENCY_ID,
            self::CONTEXT_LOCALE_CODE
        );
        /** @var stdClass $currency */
        $this->assertSame(self::STUB_INSTALLED_CURRENCY_ID, $currency->id);
    }

    /**
     * Given an unknown currency id
     * When asking a currency (with this id) to the currency manager
     * Then it should return null
     *
     * This test is about the manager being able to use the good method from the good repository to retrieve the wanted
     * currency.
     */
    public function testGetCurrencyByIdWhenNotInstalled()
    {
        $currency = $this->manager->getCurrencyById(
            self::STUB_UNINSTALLED_CURRENCY_ID,
            self::CONTEXT_LOCALE_CODE
        );
        /** @var stdClass $currency */
        $this->assertNull($currency);
    }

    /**
     * Given a valid currency code
     * When asking the currency manager a currency (with this iso code)
     * Then it should return the expected Currency object
     *
     * This test is about the manager being able to use the good method from the good repository to retrieve the wanted
     * currency.
     *
     * @param $currencyCode
     *
     * @dataProvider provideCurrencyCodes
     */
    public function testGetCurrencyByIsoCode($currencyCode)
    {
        $currency = $this->manager->getCurrencyByIsoCode(
            $currencyCode,
            self::CONTEXT_LOCALE_CODE
        );
        /** @var stdClass $currency */
        $this->assertSame($currencyCode, $currency->isoCode);
    }

    public function provideCurrencyCodes()
    {
        return array(
            array(self::STUB_INSTALLED_CURRENCY_CODE),
            array(self::STUB_UNINSTALLED_CURRENCY_CODE),
        );
    }
}
