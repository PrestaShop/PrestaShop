<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
