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

namespace Tests\Unit\Adapter\Product\Combination\Update\Filler;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler\PricesFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;

class PricesFillerTest extends CombinationFillerTestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     *
     * @param Combination $combination
     * @param UpdateCombinationCommand $command
     * @param array $expectedUpdatableProperties
     * @param Combination $expectedProduct
     */
    public function testFillsUpdatableProperties(
        Combination $combination,
        UpdateCombinationCommand $command,
        array $expectedUpdatableProperties,
        Combination $expectedProduct
    ): void {
        $this->fillUpdatableProperties(
            $this->getFiller(),
            $combination,
            $command,
            $expectedUpdatableProperties,
            $expectedProduct
        );
    }

    /**
     * @return iterable
     */
    public function getDataToTestUpdatablePropertiesFilling(): iterable
    {
        $command = $this->getEmptyCommand()
            ->setWholesalePrice('4.99')
            ->setImpactOnPrice('45.99')
        ;
        $expectedCombination = $this->mockDefaultCombination();
        $expectedCombination->wholesale_price = 4.99;
        $expectedCombination->price = 45.99;

        yield [
            $this->mockDefaultCombination(),
            $command,
            [
                'price',
                'wholesale_price',
            ],
            $expectedCombination,
        ];

        $command = $this->getEmptyCommand()
            ->setEcotax('0.3')
            ->setImpactOnUnitPrice('10')
        ;
        $expectedCombination = $this->mockDefaultCombination();
        $expectedCombination->ecotax = 0.3;
        $expectedCombination->unit_price_impact = 10.0;
        yield [
            $this->mockDefaultCombination(),
            $command,
            [
                'ecotax',
                'unit_price_impact',
            ],
            $expectedCombination,
        ];
    }

    /**
     * @return PricesFiller
     */
    private function getFiller(): PricesFiller
    {
        return new PricesFiller();
    }
}
