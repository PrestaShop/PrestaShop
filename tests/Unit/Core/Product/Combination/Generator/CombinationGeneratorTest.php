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
