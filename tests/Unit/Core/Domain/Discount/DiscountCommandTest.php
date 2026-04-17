<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Core\Domain\Discount;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRule;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroupType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;

class DiscountCommandTest extends TestCase
{
    /**
     * @dataProvider getAddProductConditionsData
     */
    public function testAddDiscountProductConditions(array $productConditions, AddDiscountCommand $expectedCommand): void
    {
        $command = new AddDiscountCommand(DiscountType::CART_LEVEL, [1 => 'name']);
        $command->setProductConditions($productConditions);
        $this->assertEquals($expectedCommand, $command);
    }

    public static function getAddProductConditionsData(): iterable
    {
        $productRuleGroup = new ProductRuleGroup(
            5,
            [
                new ProductRule(
                    ProductRuleType::PRODUCTS,
                    [1, 3, 5],
                ),
            ],
            ProductRuleGroupType::AT_LEAST_ONE_PRODUCT_RULE,
        );
        $command = new AddDiscountCommand(DiscountType::CART_LEVEL, [1 => 'name']);
        $command->setProductConditions([$productRuleGroup]);

        yield 'object conditions' => [
            [$productRuleGroup],
            $command,
        ];

        yield 'array conditions' => [
            [
                [
                    'quantity' => 5,
                    'rules' => [
                        [
                            'type' => ProductRuleType::PRODUCTS->value,
                            'itemIds' => [1, 3, 5],
                        ],
                    ],
                ],
            ],
            $command,
        ];

        $productRuleGroup2 = new ProductRuleGroup(
            15,
            [
                new ProductRule(
                    ProductRuleType::COMBINATIONS,
                    [1, 3, 5],
                ),
                new ProductRule(
                    ProductRuleType::ATTRIBUTES,
                    [16, 18],
                ),
                new ProductRule(
                    ProductRuleType::FEATURES,
                    [11, 21],
                ),
                new ProductRule(
                    ProductRuleType::SUPPLIERS,
                    [78, 65],
                ),
                new ProductRule(
                    ProductRuleType::MANUFACTURERS,
                    [56, 43],
                ),
            ],
            ProductRuleGroupType::ALL_PRODUCT_RULES,
        );

        $command = new AddDiscountCommand(DiscountType::CART_LEVEL, [1 => 'name']);
        $command->setProductConditions([$productRuleGroup, $productRuleGroup2]);

        yield 'object conditions with multiple rules' => [
            [$productRuleGroup, $productRuleGroup2],
            $command,
        ];

        yield 'array conditions with multiple rules' => [
            [
                [
                    'quantity' => 5,
                    'rules' => [
                        [
                            'type' => ProductRuleType::PRODUCTS->value,
                            'itemIds' => [1, 3, 5],
                        ],
                    ],
                ],
                [
                    'quantity' => 15,
                    'rules' => [
                        [
                            'type' => ProductRuleType::COMBINATIONS->value,
                            'itemIds' => [1, 3, 5],
                        ],
                        [
                            'type' => ProductRuleType::ATTRIBUTES->value,
                            'itemIds' => [16, 18],
                        ],
                        [
                            'type' => ProductRuleType::FEATURES->value,
                            'itemIds' => [11, 21],
                        ],
                        [
                            'type' => ProductRuleType::SUPPLIERS->value,
                            'itemIds' => [78, 65],
                        ],
                        [
                            'type' => ProductRuleType::MANUFACTURERS->value,
                            'itemIds' => [56, 43],
                        ],
                    ],
                    'type' => ProductRuleGroupType::ALL_PRODUCT_RULES->value,
                ],
            ],
            $command,
        ];
    }

    /**
     * @dataProvider getUpdateProductConditionsData
     */
    public function testUpdateProductConditions(array $productConditions, UpdateDiscountCommand $expectedCommand): void
    {
        $command = new UpdateDiscountCommand(42);
        $command->setProductConditions($productConditions);
        $this->assertEquals($expectedCommand, $command);
    }

    public static function getUpdateProductConditionsData(): iterable
    {
        $productRuleGroup = new ProductRuleGroup(
            5,
            [
                new ProductRule(
                    ProductRuleType::PRODUCTS,
                    [1, 3, 5],
                ),
            ],
            ProductRuleGroupType::AT_LEAST_ONE_PRODUCT_RULE,
        );
        $command = new UpdateDiscountCommand(42);
        $command->setProductConditions([$productRuleGroup]);

        yield 'object conditions' => [
            [$productRuleGroup],
            $command,
        ];

        yield 'array conditions' => [
            [
                [
                    'quantity' => 5,
                    'rules' => [
                        [
                            'type' => ProductRuleType::PRODUCTS->value,
                            'itemIds' => [1, 3, 5],
                        ],
                    ],
                ],
            ],
            $command,
        ];

        $productRuleGroup2 = new ProductRuleGroup(
            15,
            [
                new ProductRule(
                    ProductRuleType::COMBINATIONS,
                    [1, 3, 5],
                ),
                new ProductRule(
                    ProductRuleType::ATTRIBUTES,
                    [16, 18],
                ),
                new ProductRule(
                    ProductRuleType::FEATURES,
                    [11, 21],
                ),
                new ProductRule(
                    ProductRuleType::SUPPLIERS,
                    [78, 65],
                ),
                new ProductRule(
                    ProductRuleType::MANUFACTURERS,
                    [56, 43],
                ),
            ],
            ProductRuleGroupType::ALL_PRODUCT_RULES,
        );

        $command = new UpdateDiscountCommand(42);
        $command->setProductConditions([$productRuleGroup, $productRuleGroup2]);

        yield 'object conditions with multiple rules' => [
            [$productRuleGroup, $productRuleGroup2],
            $command,
        ];

        yield 'array conditions with multiple rules' => [
            [
                [
                    'quantity' => 5,
                    'rules' => [
                        [
                            'type' => ProductRuleType::PRODUCTS->value,
                            'itemIds' => [1, 3, 5],
                        ],
                    ],
                ],
                [
                    'quantity' => 15,
                    'rules' => [
                        [
                            'type' => ProductRuleType::COMBINATIONS->value,
                            'itemIds' => [1, 3, 5],
                        ],
                        [
                            'type' => ProductRuleType::ATTRIBUTES->value,
                            'itemIds' => [16, 18],
                        ],
                        [
                            'type' => ProductRuleType::FEATURES->value,
                            'itemIds' => [11, 21],
                        ],
                        [
                            'type' => ProductRuleType::SUPPLIERS->value,
                            'itemIds' => [78, 65],
                        ],
                        [
                            'type' => ProductRuleType::MANUFACTURERS->value,
                            'itemIds' => [56, 43],
                        ],
                    ],
                    'type' => ProductRuleGroupType::ALL_PRODUCT_RULES->value,
                ],
            ],
            $command,
        ];
    }

    /**
     * @dataProvider getInvalidProductConditions
     */
    public function testInvalidAddProductConditions(array $productConditions, string $expectedExceptionMessage): void
    {
        $this->expectException(DiscountConstraintException::class);
        $this->expectExceptionCode(DiscountConstraintException::INVALID_PRODUCTS_CONDITIONS);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $command = new AddDiscountCommand(DiscountType::CART_LEVEL, [1 => 'name']);
        $command->setProductConditions($productConditions);
    }

    /**
     * @dataProvider getInvalidProductConditions
     */
    public function testInvalidUpdateProductConditions(array $productConditions, string $expectedExceptionMessage): void
    {
        $this->expectException(DiscountConstraintException::class);
        $this->expectExceptionCode(DiscountConstraintException::INVALID_PRODUCTS_CONDITIONS);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $command = new UpdateDiscountCommand(42);
        $command->setProductConditions($productConditions);
    }

    public static function getInvalidProductConditions(): iterable
    {
        yield 'invalid type in array' => [
            [
                new ProductRule(ProductRuleType::COMBINATIONS, [1]),
            ],
            sprintf('Product conditions must be an array of %s', ProductRuleGroup::class),
        ];

        yield 'invalid quantity' => [
            [
                new ProductRuleGroup(0, []),
            ],
            'Product conditions quantity must be strictly positive',
        ];

        yield 'empty rules' => [
            [
                new ProductRuleGroup(1, []),
            ],
            'Product conditions rules cannot be empty',
        ];

        yield 'empty items' => [
            [
                new ProductRuleGroup(1, [new ProductRule(ProductRuleType::COMBINATIONS, [])]),
            ],
            'Product conditions rule items cannot be empty',
        ];

        yield 'item IDs must be integers' => [
            [
                new ProductRuleGroup(1, [new ProductRule(ProductRuleType::COMBINATIONS, ['1'])]),
            ],
            'Product conditions rule item ID must be an integer',
        ];

        yield 'item IDs cannot be zero' => [
            [
                new ProductRuleGroup(1, [new ProductRule(ProductRuleType::COMBINATIONS, [0])]),
            ],
            'Product conditions rule item ID must be strictly positive',
        ];

        yield 'item IDs cannot be negative' => [
            [
                new ProductRuleGroup(1, [new ProductRule(ProductRuleType::COMBINATIONS, [-1])]),
            ],
            'Product conditions rule item ID must be strictly positive',
        ];
    }
}
