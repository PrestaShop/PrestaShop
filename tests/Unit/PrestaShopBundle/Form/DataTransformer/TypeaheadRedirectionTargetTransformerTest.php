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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\Admin\Sell\Product\DataTransformer\TypeaheadRedirectionTargetTransformer;

class TypeaheadRedirectionTargetTransformerTest extends TestCase
{
    /**
     * @dataProvider getTransformValues
     *
     * @param string|int $input
     * @param array|null $expectedResult
     */
    public function testTransform($input, ?array $expectedResult)
    {
        $transformer = new TypeaheadRedirectionTargetTransformer();
        $transformed = $transformer->transform($input);
        $this->assertEquals($expectedResult, $transformed);
    }

    /**
     * @dataProvider getReverseTransformValues
     *
     * @param mixed $input
     * @param int|null $expectedResult
     */
    public function testReverseTransform($input, ?int $expectedResult)
    {
        $transformer = new TypeaheadRedirectionTargetTransformer();
        $transformed = $transformer->reverseTransform($input);
        $this->assertEquals($expectedResult, $transformed);
    }

    public function getReverseTransformValues()
    {
        yield [
            [
                'data' => [
                    42,
                ],
            ],
            42,
        ];

        yield [
            [
                'data' => [
                    '42',
                ],
            ],
            42,
        ];

        yield [
            [
            ],
            null,
        ];

        yield [
            [
                42,
            ],
            null,
        ];

        yield [
            [
                'otherData' => 'plop',
                42,
            ],
            null,
        ];

        yield [
            [
                'otherData' => 'plop',
                42,
                'data' => [
                    '42',
                ],
            ],
            42,
        ];
    }

    public function getTransformValues()
    {
        yield [
            42,
            [
                'data' => [
                    42,
                ],
            ],
        ];

        yield [
            '42',
            [
                'data' => [
                    42,
                ],
            ],
        ];

        yield [
            null,
            null,
        ];

        yield [
            '',
            [
                'data' => [
                    0,
                ],
            ],
        ];
    }
}
