<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Product\Generator;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Generator\CombinationGenerator;
use PrestaShop\PrestaShop\Core\Product\Generator\GeneratedCombination;

class CombinationGeneratorTest extends TestCase
{
    /**
     * @var CombinationGenerator
     */
    private $combinationGenerator;

    public function setUp()
    {
        $this->combinationGenerator = new CombinationGenerator();
    }

    /**
     * @dataProvider getValidData
     *
     * @param array $valuesByGroup
     * @param array $expectedCombinations
     */
    public function testGenerateBulkCombinationsFromValidData(array $valuesByGroup, array $expectedCombinations)
    {
        $this->assertEquals($expectedCombinations, $this->combinationGenerator->bulkGenerate($valuesByGroup));
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
                new GeneratedCombination([2, 5, 7]),
                new GeneratedCombination([2, 5, 8]),
                new GeneratedCombination([2, 6, 7]),
                new GeneratedCombination([2, 6, 8]),
                new GeneratedCombination([3, 5, 7]),
                new GeneratedCombination([3, 5, 8]),
                new GeneratedCombination([3, 6, 7]),
                new GeneratedCombination([3, 6, 8]),
                new GeneratedCombination([4, 5, 7]),
                new GeneratedCombination([4, 5, 8]),
                new GeneratedCombination([4, 6, 7]),
                new GeneratedCombination([4, 6, 8]),
            ],
        ];
    }
}
