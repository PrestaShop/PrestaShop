<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\Combination\FeatureValue\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Command\SetCombinationFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidProductFeatureValuesFormatException;

class SetCombinationFeatureValuesCommandTest extends TestCase
{
    /**
     * @dataProvider getValidInput
     *
     * @param int $combinationId
     * @param array $featureValues
     */
    public function testValidInput(int $combinationId, array $featureValues)
    {
        $command = new SetCombinationFeatureValuesCommand($combinationId, $featureValues);
        $this->assertNotNull($command);
    }

    /**
     * @dataProvider getInvalidInput
     *
     * @param int $combinationId
     * @param array $featureValues
     * @param string $expectedException
     */
    public function testInvalidInput(int $combinationId, array $featureValues, string $expectedException)
    {
        $this->expectException($expectedException);
        new SetCombinationFeatureValuesCommand($combinationId, $featureValues);
    }

    public function getInvalidInput()
    {
        yield [
            42,
            [
                [
                    'feature_value_id' => 51,
                ],
            ],
            InvalidProductFeatureValuesFormatException::class,
        ];

        yield [
            42,
            [
                [
                    'feature_id' => 51,
                ],
            ],
            InvalidProductFeatureValuesFormatException::class,
        ];

        yield [
            -42,
            [
                [
                    'feature_id' => 51,
                    'feature_value_id' => 51,
                ],
            ],
            CombinationConstraintException::class,
        ];

        yield [
            42,
            [
                [
                    'feature_id' => 0,
                    'feature_value_id' => 51,
                ],
            ],
            InvalidProductFeatureValuesFormatException::class,
        ];
    }

    public function getValidInput()
    {
        yield [
            42,
            [
                [
                    'feature_id' => 51,
                    'feature_value_id' => 51,
                ],
            ],
        ];

        yield [
            42,
            [
                [
                    'feature_id' => 51,
                    'custom_values' => [
                        1 => 'value',
                    ],
                ],
            ],
        ];

        yield [
            42,
            [
                [
                    'feature_id' => 51,
                    'feature_value_id' => 51,
                    'custom_values' => [
                        1 => 'value',
                    ],
                ],
            ],
        ];

        yield [
            42,
            [
                [
                    'feature_id' => 51,
                    'feature_value_id' => 51,
                ],
                [
                    'feature_id' => 51,
                    'custom_values' => [
                        1 => 'value',
                    ],
                ],
                [
                    'feature_id' => 51,
                    'feature_value_id' => 51,
                    'custom_values' => [
                        1 => 'value',
                    ],
                ],
            ],
        ];
    }
}
