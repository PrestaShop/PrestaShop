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

namespace Tests\Unit\Core\Product\Combination\NameBuilder;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilder;
use Tests\Resources\Translator\DummyTranslator;

class CombinationNameBuilderTest extends TestCase
{
    /**
     * @dataProvider getDataForTestItBuildsCombinationName
     *
     * @param CombinationAttributeInformation[] $combinationAttributesInfo
     * @param string $expected
     */
    public function testItBuildsCombinationName(array $combinationAttributesInfo, string $expected): void
    {
        $nameBuilder = new CombinationNameBuilder(
            new DummyTranslator(),
            ', ',
            '-'
        );

        $actual = $nameBuilder->buildName($combinationAttributesInfo);

        Assert::assertSame($expected, $actual);
    }

    /**
     * @dataProvider getDataForTestItBuildsCombinationFullName
     *
     * @param array $productNameAndCombinationAttributesInfo
     * @param string $expected
     */
    public function testItBuildsCombinationFullName(array $productNameAndCombinationAttributesInfo, string $expected): void
    {
        $nameBuilder = new CombinationNameBuilder(
            new DummyTranslator(),
            ', ',
            '-'
        );

        $actual = $nameBuilder->buildFullName(
            $productNameAndCombinationAttributesInfo['productName'],
            $productNameAndCombinationAttributesInfo['combinationAttributesInfo']
        );

        Assert::assertSame($expected, $actual);
    }

    /**
     * @return Generator
     */
    public function getDataForTestItBuildsCombinationName(): Generator
    {
        foreach ($this->getCombinationNameData() as $data) {
            $data = array_values($data)[0];
            yield $data;
        }
    }

    public function getCombinationNameData(): Generator
    {
        yield [
            'SGrey40x60' => [
                [
                    new CombinationAttributeInformation(
                        1,
                        'Size',
                        1,
                        'S'
                    ),
                    new CombinationAttributeInformation(
                        2,
                        'Color',
                        5,
                        'Grey'
                    ),
                    new CombinationAttributeInformation(
                        3,
                        'Dimension',
                        19,
                        '40x60cm'
                    ),
                ],
                'Size - S, Color - Grey, Dimension - 40x60cm',
            ],
        ];

        yield [
            'Grey40x60' => [
                [
                    new CombinationAttributeInformation(
                        2,
                        'Color',
                        5,
                        'Grey'
                    ),
                    new CombinationAttributeInformation(
                        3,
                        'Dimension',
                        19,
                        '40x60cm'
                    ),
                ],
                'Color - Grey, Dimension - 40x60cm',
            ],
        ];

        yield [
            'Grey' => [
                [
                    new CombinationAttributeInformation(
                        2,
                        'Color',
                        5,
                        'Grey'
                    ),
                ],
                'Color - Grey',
            ],
        ];
    }

    /**
     * @return Generator
     */
    public function getDataForTestItBuildsCombinationFullName(): Generator
    {
        $productType = [
            'SGrey40x60' => 'Shirt',
            'Grey40x60' => 'Frame',
            'Grey' => 'Mug',
        ];

        foreach ($this->getCombinationNameData() as $data) {
            foreach ($data as $combinationName => $expected) {
                yield [
                    [
                        'combinationAttributesInfo' => array_values($expected)[0],
                        'productName' => $productType[$combinationName],
                    ],
                    $productType[$combinationName] . ': ' . array_values($expected)[1],
                ];
            }
        }
    }
}
