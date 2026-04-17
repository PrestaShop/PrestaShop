<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\FeatureValue\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidProductFeatureValuesFormatException;

class SetProductFeatureValuesCommandTest extends TestCase
{
    /**
     * @dataProvider getValidInput
     *
     * @param int $productId
     * @param array $featureValues
     */
    public function testValidInput(int $productId, array $featureValues)
    {
        $command = new SetProductFeatureValuesCommand($productId, $featureValues);
        $this->assertNotNull($command);
    }

    /**
     * @dataProvider getInvalidInput
     *
     * @param int $productId
     * @param array $featureValues
     * @param string $expectedException
     */
    public function testInvalidInput(int $productId, array $featureValues, string $expectedException)
    {
        $this->expectException($expectedException);
        new SetProductFeatureValuesCommand($productId, $featureValues);
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
            ProductConstraintException::class,
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
