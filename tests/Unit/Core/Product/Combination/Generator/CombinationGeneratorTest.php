<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Product\Combination\Generator;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Combination\Generator\CombinationGenerator;

class CombinationGeneratorTest extends TestCase
{
    /**
     * @var CombinationGenerator
     */
    private $combinationGenerator;

    public function setUp(): void
    {
        $this->combinationGenerator = new CombinationGenerator();
    }

    /**
     * @dataProvider getValidData
     *
     * @param array $valuesByGroup
     * @param array $expectedCombinations
     */
    public function testGenerateCombinationsFromValidData(array $valuesByGroup, array $expectedCombinations)
    {
        $yieldedCombinations = $this->combinationGenerator->generate($valuesByGroup);
        $generatedCombinations = [];

        foreach ($yieldedCombinations as $yieldedCombination) {
            $generatedCombinations[] = $yieldedCombination;
        }

        $this->assertEquals($expectedCombinations, $generatedCombinations);
    }

    /**
     * @return Generator
     */
    public function getValidData(): Generator
    {
        yield [
            [
                1 => [2, 3, 4],
                2 => [5, 6],
                3 => [7, 8],
            ],
            [
                [1 => 2, 2 => 5, 3 => 7],
                [1 => 2, 2 => 5, 3 => 8],
                [1 => 2, 2 => 6, 3 => 7],
                [1 => 2, 2 => 6, 3 => 8],
                [1 => 3, 2 => 5, 3 => 7],
                [1 => 3, 2 => 5, 3 => 8],
                [1 => 3, 2 => 6, 3 => 7],
                [1 => 3, 2 => 6, 3 => 8],
                [1 => 4, 2 => 5, 3 => 7],
                [1 => 4, 2 => 5, 3 => 8],
                [1 => 4, 2 => 6, 3 => 7],
                [1 => 4, 2 => 6, 3 => 8],
            ],
        ];
    }
}
