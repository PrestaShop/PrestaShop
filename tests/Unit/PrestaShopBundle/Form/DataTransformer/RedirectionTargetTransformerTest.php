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

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\Admin\Sell\Product\DataTransformer\RedirectionTargetTransformer;

class RedirectionTargetTransformerTest extends TestCase
{
    /**
     * @dataProvider getTransformValues
     *
     * @param string|int $input
     * @param array|null $expectedResult
     */
    public function testTransform($input, ?array $expectedResult)
    {
        $transformer = new RedirectionTargetTransformer();
        $transformed = $transformer->transform($input);
        $this->assertEquals($expectedResult, $transformed);
    }

    public function getTransformValues()
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
    }

    /**
     * @dataProvider getReverseTransformValues
     *
     * @param mixed $input
     * @param array|null $expectedResult
     */
    public function testReverseTransform($input, ?array $expectedResult)
    {
        $transformer = new RedirectionTargetTransformer();
        $transformed = $transformer->reverseTransform($input);
        $this->assertEquals($expectedResult, $transformed);
    }

    public function getReverseTransformValues()
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
    }
}
