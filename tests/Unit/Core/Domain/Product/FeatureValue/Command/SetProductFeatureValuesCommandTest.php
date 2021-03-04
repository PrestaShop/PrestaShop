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
