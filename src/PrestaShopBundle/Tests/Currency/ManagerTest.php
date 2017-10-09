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

use MyProject\Proxies\__CG__\stdClass;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Currency\Manager;
use PrestaShopBundle\Currency\Repository\Installed\InstalledRepositoryInterface;
use PrestaShopBundle\Currency\Repository\Reference\ReferenceRepositoryInterface;

class ManagerTest extends TestCase
{
    const STUB_CURRENCY_ID   = 999;
    const STUB_CURRENCY_CODE = 'FOO';

    /**
     * @var Manager
     */
    protected $manager;
    protected $mockedCurrency;

    public function setUp()
    {
        // No behavior to be mocked. We just need to test if this very object is retrieved at the end.
        $this->mockedCurrency        = (object)array('id' => self::STUB_CURRENCY_ID);
        $installedCurrencyRepository = $this->getMock(
            'PrestaShopBundle\Currency\Repository\Installed\InstalledRepositoryInterface'
        );
        $installedCurrencyRepository->method('getCurrencyById')
            ->with($this->equalTo(self::STUB_CURRENCY_ID))
            ->willReturn($this->mockedCurrency);
        $installedCurrencyRepository->method('getCurrencyByISoCode')
            ->with($this->equalTo(self::STUB_CURRENCY_ID))
            ->willReturn($this->mockedCurrency);

        $referenceCurrencyRepository = $this->getMock(
            'PrestaShopBundle\Currency\Repository\Reference\ReferenceRepositoryInterface'
        );

        /** @var InstalledRepositoryInterface $installedCurrencyRepository */
        /** @var ReferenceRepositoryInterface $referenceCurrencyRepository */
        $this->manager = new Manager($installedCurrencyRepository, $referenceCurrencyRepository);
    }

    /**
     * Given a valid currency id
     * When asking a currency (with this id) to the currency manager
     * Then it should return the expected Currency
     *
     * This test is about the manager being able to use the good method of the good repository to retrieve the wanted
     * currency.
     */
    public function testGetCurrencyById()
    {
        $currency = $this->manager->getCurrencyById(self::STUB_CURRENCY_ID);
        /** @var stdClass $currency */
        $this->assertSame(self::STUB_CURRENCY_ID, $currency->id);
    }

    public function testGetCurrencyByIsoCode()
    {
        $currency = $this->manager->getCurrencyByIsoCode(self::STUB_CURRENCY_CODE);
        /** @var stdClass $currency */
        $this->assertSame(self::STUB_CURRENCY_CODE, $currency->isoCode);
    }
}
