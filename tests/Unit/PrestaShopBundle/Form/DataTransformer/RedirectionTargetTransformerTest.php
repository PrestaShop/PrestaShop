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

namespace Tests\Unit\PrestaShopBundle\Form\DataTransformer;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\QueryResult\RedirectTargetInformation;
use PrestaShopBundle\Form\Admin\Sell\Product\DataTransformer\RedirectionTargetTransformer;

class RedirectionTargetTransformerTest extends TestCase
{
    /**
     * @dataProvider getTransformValues
     *
     * @param array|null $input
     * @param array|null $expectedResult
     */
    public function testTransform(?array $input, ?array $expectedResult): void
    {
        $transformer = new RedirectionTargetTransformer();
        $transformed = $transformer->transform($input);
        $this->assertEquals($expectedResult, $transformed);
    }

    public function getTransformValues(): Generator
    {
        yield [
            [
                'unknown' => 42,
            ],
            [
                'unknown' => 42,
            ],
        ];

        yield [
            [
                'type' => 'category',
                'unknown' => 42,
            ],
            [
                'type' => 'category',
                'unknown' => 42,
            ],
        ];

        yield [
            [
                'type' => 'not_found',
            ],
            [
                'type' => 'not_found',
            ],
        ];

        yield [
            [
                'target' => 42,
            ],
            [
                'target' => [42],
            ],
        ];

        yield [
            [
                'type' => 'category',
                'target' => 42,
            ],
            [
                'type' => 'category',
                'target' => [42],
            ],
        ];

        yield [
            [
                'type' => 'product',
                'target' => 42,
            ],
            [
                'type' => 'product',
                'target' => [42],
            ],
        ];

        yield [
            [
                'target' => '42',
            ],
            [
                'target' => [42],
            ],
        ];

        yield [
            [],
            [],
        ];

        yield [
            null,
            null,
        ];

        $redirectTarget = new RedirectTargetInformation(
            42,
            RedirectTargetInformation::PRODUCT_TYPE,
            'Product 1',
            'path/to/img.jpg'
        );
        yield [
            [
                'type' => 'product',
                'target' => $redirectTarget,
            ],
            [
                'type' => 'product',
                'target' => [$redirectTarget],
            ],
        ];

        $targetArray = [
            'id' => $redirectTarget->getId(),
            'name' => $redirectTarget->getName(),
            'image' => $redirectTarget->getImage(),
        ];
        yield [
            [
                'type' => 'product',
                'target' => $targetArray,
            ],
            [
                'type' => 'product',
                'target' => [$targetArray],
            ],
        ];
    }

    /**
     * @dataProvider getReverseTransformValues
     *
     * @param array|null $input
     * @param array|null $expectedResult
     */
    public function testReverseTransform(?array $input, ?array $expectedResult): void
    {
        $transformer = new RedirectionTargetTransformer();
        $transformed = $transformer->reverseTransform($input);
        $this->assertEquals($expectedResult, $transformed);
    }

    public function getReverseTransformValues(): Generator
    {
        yield [
            [
                'target' => [42],
            ],
            [
                'target' => 42,
            ],
        ];

        yield [
            [
                'target' => ['42'],
            ],
            [
                'target' => 42,
            ],
        ];

        yield [
            [],
            [],
        ];

        yield [
            [
                'type' => 'product',
                'target' => [42],
            ],
            [
                'type' => 'product',
                'target' => 42,
            ],
        ];

        yield [
            [
                'unknown' => 'product',
                'target' => [42],
            ],
            [
                'unknown' => 'product',
                'target' => 42,
            ],
        ];

        yield [
            [
                'unknown' => 'product',
                'target' => [
                    51 => 42,
                ],
            ],
            [
                'unknown' => 'product',
                'target' => 42,
            ],
        ];

        yield [
            [
                'unknown' => 'product',
                'target' => [
                    'index' => 42,
                ],
            ],
            [
                'unknown' => 'product',
                'target' => 42,
            ],
        ];

        yield [
            [
                'unknown' => 'product',
                'target' => [
                    51 => 42,
                    64 => 43,
                ],
            ],
            [
                'unknown' => 'product',
                'target' => 42,
            ],
        ];

        yield [
            [
                'unknown' => 'product',
                'target' => [42, 67],
            ],
            [
                'unknown' => 'product',
                'target' => 42,
            ],
        ];

        yield [
            [
                'unknown' => 'product',
                'target' => [
                    'index' => 42,
                    'another_index' => 54,
                ],
            ],
            [
                'unknown' => 'product',
                'target' => 42,
            ],
        ];

        yield [
            [
                'type' => 'now_found',
            ],
            [
                'type' => 'now_found',
            ],
        ];

        yield [
            [
                'otherData' => 'plop',
                42,
            ],
            [
                'otherData' => 'plop',
                42,
            ],
        ];

        yield [
            null,
            null,
        ];

        $redirectTarget = new RedirectTargetInformation(
            42,
            RedirectTargetInformation::PRODUCT_TYPE,
            'Product 1',
            'path/to/img.jpg'
        );
        yield [
            [
                'type' => 'product',
                'target' => [$redirectTarget],
            ],
            [
                'type' => 'product',
                'target' => $redirectTarget,
            ],
        ];

        $targetArray = [
            'id' => $redirectTarget->getId(),
            'name' => $redirectTarget->getName(),
            'image' => $redirectTarget->getImage(),
        ];
        yield [
            [
                'type' => 'product',
                'target' => [$targetArray],
            ],
            [
                'type' => 'product',
                'target' => $targetArray,
            ],
        ];
    }
}
