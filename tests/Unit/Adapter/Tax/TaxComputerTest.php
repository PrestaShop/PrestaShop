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

declare(strict_types=1);

namespace Tests\Unit\Adapter\Tax;

use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

class TaxComputerTest extends TestCase
{
    /**
     * @var TaxRulesGroupId
     */
    private $taxRulesGroupId;

    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->taxRulesGroupId = new TaxRulesGroupId(42);
        $this->countryId = new CountryId(51);
    }

    /**
     * @dataProvider getComputePriceWithTaxesData
     *
     * @param float $taxRate
     * @param string $priceWithoutTaxes
     * @param string $priceWithTaxes
     */
    public function testComputePriceWithTaxes(float $taxRate, string $priceWithoutTaxes, string $priceWithTaxes): void
    {
        $repository = $this->mockRepository($taxRate);
        $computer = new TaxComputer($repository);
        $computedPrice = $computer->computePriceWithTaxes(
            new DecimalNumber($priceWithoutTaxes),
            $this->taxRulesGroupId,
            $this->countryId
        );

        $this->assertEquals(new DecimalNumber($priceWithTaxes), $computedPrice);
    }

    public function getComputePriceWithTaxesData(): Generator
    {
        yield [20.0, '10.00', '12.00'];
        yield [6.0, '10.00', '10.60'];
        yield [21.3, '10.00', '12.13'];
        yield [14.7, '8.00', '9.176'];
        yield [3.14769, '8.666', '8.9387788154'];
    }

    /**
     * @dataProvider getComputePriceWithoutTaxesData
     *
     * @param float $taxRate
     * @param string $priceWithoutTaxes
     * @param string $priceWithTaxes
     * @param int $precision
     */
    public function testComputePriceWithoutTaxes(
        float $taxRate,
        string $priceWithTaxes,
        string $priceWithoutTaxes,
        int $precision
    ): void {
        $repository = $this->mockRepository($taxRate);
        $computer = new TaxComputer($repository);
        $computedPrice = $computer->computePriceWithoutTaxes(
            new DecimalNumber($priceWithTaxes),
            $this->taxRulesGroupId,
            $this->countryId
        );

        $this->assertEquals($priceWithoutTaxes, $computedPrice->toPrecision($precision));
    }

    public function getComputePriceWithoutTaxesData(): Generator
    {
        yield [20.0, '10.00', '8.333333', 6];
        yield [20.0, '10.00', '8.3333', 4];
        yield [16.43, '8.333333333', '7.1573', 4];
        yield [16.43, '8.333333333', '7.157376', 6];
        yield [16.43, '8.333333', '7.157376000', 9];
        yield [16.43, '8.333333333', '7.157376000000', 12];
    }

    /**
     * @param float $countryTaxRate
     *
     * @return MockObject|TaxRulesGroupRepository
     */
    private function mockRepository(float $countryTaxRate)
    {
        $repositoryMock = $this->getMockBuilder(TaxRulesGroupRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryMock
            ->expects($this->once())
            ->method('getTaxRulesGroupDetails')
            ->with($this->equalTo($this->taxRulesGroupId))
            ->willReturn([
                'rates' => [
                    $this->countryId->getValue() => $countryTaxRate,
                ],
            ])
        ;

        return $repositoryMock;
    }
}
