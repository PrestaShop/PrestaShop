<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
