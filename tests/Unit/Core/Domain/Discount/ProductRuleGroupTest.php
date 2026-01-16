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

namespace Core\Domain\Discount;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRule;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroupType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleType;

class ProductRuleGroupTest extends TestCase
{
    /**
     * @dataProvider getProductRuleGroupArrayData
     */
    public function testFromArray(array $data, ProductRuleGroup $expectedProductRuleGroup): void
    {
        $this->assertEquals($expectedProductRuleGroup, ProductRuleGroup::fromArray($data));
    }

    public static function getProductRuleGroupArrayData(): iterable
    {
        yield 'product rule group, default group type' => [
            [
                'quantity' => 5,
                'rules' => [
                    [
                        'type' => ProductRuleType::PRODUCTS->value,
                        'itemIds' => [1, 3, 5],
                    ],
                ],
            ],
            new ProductRuleGroup(
                5,
                [
                    new ProductRule(
                        ProductRuleType::PRODUCTS,
                        [1, 3, 5],
                    ),
                ],
                ProductRuleGroupType::AT_LEAST_ONE_PRODUCT_RULE,
            ),
        ];

        yield 'category rule group, all product rules type' => [
            [
                'quantity' => 5,
                'rules' => [
                    [
                        'type' => ProductRuleType::CATEGORIES->value,
                        'itemIds' => [1, 3, 5],
                    ],
                ],
                'type' => ProductRuleGroupType::ALL_PRODUCT_RULES->value,
            ],
            new ProductRuleGroup(
                5,
                [
                    new ProductRule(
                        ProductRuleType::CATEGORIES,
                        [1, 3, 5],
                    ),
                ],
                ProductRuleGroupType::ALL_PRODUCT_RULES,
            ),
        ];

        yield 'multiple rule groups' => [
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
            new ProductRuleGroup(
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
            ),
        ];
    }
}
