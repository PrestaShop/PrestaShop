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

namespace PrestaShopBundle\Tests\Currency\Repository;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Currency\Repository\MixedRepository;

class MixedRepositoryTest extends TestCase
{
    const STUB_FOO = 'foo';
    const STUB_BAR = 'bar';
    const STUB_INT = 1;

    /**
     * @var MixedRepository
     */
    protected $mixedRepository;

    protected $mockedReferenceCurrency;
    protected $mockedInstalledCurrency;

    public function setUp()
    {
        $this->mixedRepository = new MixedRepository(
            $this->mockInstalledCurrencyRepository(),
            $this->mockReferenceCurrencyRepository()
        );
    }

    /**
     * Given a valid currency code
     * When asking for a reference currency with this iso code
     * Then the expected currency should be retrieved
     */
    public function testGetReferenceCurrencyByIsoCode()
    {
        $retrievedCurrency = $this->mixedRepository->getReferenceCurrencyByIsoCode(self::STUB_FOO, self::STUB_BAR);

        $this->assertSame($this->mockedReferenceCurrency, $retrievedCurrency);
    }

    /**
     * Given a valid (installed) currency id
     * When asking for this currency with this id
     * Then the expected currency should be retrieved
     */
    public function testGetInstalledCurrencyById()
    {
        $retrievedCurrency = $this->mixedRepository->getInstalledCurrencyById(self::STUB_INT);

        $this->assertSame($this->mockedInstalledCurrency, $retrievedCurrency);
    }

    /**
     * Given a valid currency
     * When asking the mixed repository to add (install) this currency
     * Then this currency should be passed to the good repository for saving
     */
    public function testAddInstalledCurrency()
    {
        $installedRepo = $this->mockInstalledCurrencyRepository();
        $installedRepo->expects($this->once())
            ->method('addInstalledCurrency')
            ->with($this->isInstanceOf('PrestaShopBundle\Currency\Currency'));
        $this->mixedRepository = new MixedRepository(
            $installedRepo,
            $this->mockReferenceCurrencyRepository()
        );

        $this->mixedRepository->addInstalledCurrency($this->mockedInstalledCurrency);
    }

    /**
     * Given a valid installed currency
     * When asking the mixed repository to update this (installed) currency
     * Then this currency should be passed to the good repository for update
     */
    public function testUpdateInstalledCurrency()
    {
        $installedRepo = $this->mockInstalledCurrencyRepository();
        $installedRepo->expects($this->once())
            ->method('updateInstalledCurrency')
            ->with($this->isInstanceOf('PrestaShopBundle\Currency\Currency'));
        $this->mixedRepository = new MixedRepository(
            $installedRepo,
            $this->mockReferenceCurrencyRepository()
        );

        $this->mixedRepository->updateInstalledCurrency($this->mockedInstalledCurrency);
    }

    /**
     * Given a valid installed currency
     * When asking the mixed repository to delete (uninstall) this currency
     * Then this currency should be passed to the good repository for deletion
     */
    public function testDeleteInstalledCurrency()
    {
        $installedRepo = $this->mockInstalledCurrencyRepository();
        $installedRepo->expects($this->once())
            ->method('deleteInstalledCurrency')
            ->with($this->isInstanceOf('PrestaShopBundle\Currency\Currency'));
        $this->mixedRepository = new MixedRepository(
            $installedRepo,
            $this->mockReferenceCurrencyRepository()
        );

        $this->mixedRepository->deleteInstalledCurrency($this->mockedInstalledCurrency);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockInstalledCurrencyRepository()
    {
        $installedCurrencyRepository   = $this->getMock(
            'PrestaShopBundle\Currency\Repository\Installed\InstalledRepositoryInterface'
        );
        $this->mockedInstalledCurrency = $this->getMockBuilder('PrestaShopBundle\Currency\Currency')
            ->disableOriginalConstructor()
            ->getMock();
        $installedCurrencyRepository->method('getInstalledCurrencyById')
            ->with(self::STUB_INT)
            ->willReturn($this->mockedInstalledCurrency);

        return $installedCurrencyRepository;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockReferenceCurrencyRepository()
    {
        $referenceCurrencyRepository   = $this->getMock(
            'PrestaShopBundle\Currency\Repository\Reference\ReferenceRepositoryInterface'
        );
        $this->mockedReferenceCurrency = $this->getMockBuilder('PrestaShopBundle\Currency\Currency')
            ->disableOriginalConstructor()
            ->getMock();
        $referenceCurrencyRepository->method('getReferenceCurrencyByIsoCode')
            ->with(self::STUB_FOO, self::STUB_BAR)
            ->willReturn($this->mockedReferenceCurrency);

        return $referenceCurrencyRepository;
    }
}
