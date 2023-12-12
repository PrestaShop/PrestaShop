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

namespace Tests\Integration\ApiPlatform;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\ValueObject\CreatedApiAccess;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopBundle\ApiPlatform\DomainSerializer;
use PrestaShopBundle\ApiPlatform\Resources\CustomerGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DomainSerializerTest extends KernelTestCase
{
    /**
     * @dataProvider getExpectedDenormalizedData
     */
    public function testDenormalize($dataToDenormalize, $denormalizedObject, ?array $normalizationMapping = []): void
    {
        $serializer = self::getContainer()->get(DomainSerializer::class);
        self::assertEquals($denormalizedObject, $serializer->denormalize($dataToDenormalize, get_class($denormalizedObject), null, [DomainSerializer::NORMALIZATION_MAPPING => $normalizationMapping]));
    }

    public function getExpectedDenormalizedData()
    {
        yield [
            [
                'localizedNames' => [
                    1 => 'test1',
                    2 => 'test2',
                ],
                'reductionPercent' => 10.3,
                'displayPriceTaxExcluded' => true,
                'showPrice' => true,
                'shopIds' => [1],
            ],
            new AddCustomerGroupCommand(
                [
                    1 => 'test1',
                    2 => 'test2',
                ],
                new DecimalNumber('10.3'),
                true,
                true,
                [1]
            ),
        ];

        $editCartRuleCommand = new EditCartRuleCommand(1);
        $editCartRuleCommand->setDescription('test description');
        $editCartRuleCommand->setCode('test code');
        $editCartRuleCommand->setMinimumAmount('10', 1, true, true);
        $editCartRuleCommand->setCustomerId(1);
        $editCartRuleCommand->setLocalizedNames([1 => 'test1', 2 => 'test2']);
        $editCartRuleCommand->setHighlightInCart(true);
        $editCartRuleCommand->setAllowPartialUse(true);
        $editCartRuleCommand->setPriority(1);
        $editCartRuleCommand->setActive(true);
        $editCartRuleCommand->setValidityDateRange(new \DateTimeImmutable('2023-08-23'), new \DateTimeImmutable('2023-08-25'));
        $editCartRuleCommand->setTotalQuantity(100);
        $editCartRuleCommand->setQuantityPerUser(1);
        $editCartRuleCommand->setCartRuleAction(new CartRuleAction(true));
        yield [
            [
                'cartRuleId' => 1,
                'description' => 'test description',
                'code' => 'test code',
                'minimumAmount' => ['minimumAmount' => '10', 'currencyId' => 1, 'taxIncluded' => true, 'shippingIncluded' => true],
                'customerId' => 1,
                'localizedNames' => [
                    1 => 'test1',
                    2 => 'test2',
                ],
                'highlightInCart' => true,
                'allowPartialUse' => true,
                'priority' => 1,
                'active' => true,
                'validityDateRange' => ['validFrom' => '2023-08-23', 'validTo' => '2023-08-25'],
                'totalQuantity' => 100,
                'quantityPerUser' => 1,
                'cartRuleAction' => ['freeShipping' => true],
                //TODO: handle cartRuleAction with complex discount handle by business rules
                // 'cartRuleAction' => ['freeShipping' => true, 'giftProduct' => ['productId': 1], 'discount' => ['amountDiscount' => ['amount' => 10]]]...
            ],
            $editCartRuleCommand,
        ];

        yield 'null value returns an empty object' => [
            null,
            new CustomerGroup(),
        ];

        $customerGroupQuery = new GetCustomerGroupForEditing(51);
        yield 'value object with wrong parameter converted via mapping' => [
            [
                'groupId' => 51,
            ],
            $customerGroupQuery,
            [
                '[groupId]' => '[customerGroupId]',
            ],
        ];

        $customerGroupQuery = new GetCustomerGroupForEditing(51);
        yield 'value object with proper parameter, extra mapping for normalization should ignore absent data and not override it with null' => [
            [
                'customerGroupId' => 51,
            ],
            $customerGroupQuery,
            [
                '[id]' => '[customerGroupId]',
                '[reduction]' => '[reductionPercent]',
            ],
        ];

        $customerGroupQuery = new GetCustomerGroupForEditing(51);
        yield 'value object with wrong parameter plus extra mapping for normalization' => [
            [
                'groupId' => 51,
            ],
            $customerGroupQuery,
            [
                '[groupId]' => '[customerGroupId]',
                '[id]' => '[customerGroupId]',
                '[reduction]' => '[reductionPercent]',
            ],
        ];
    }

    /**
     * @dataProvider getNormalizationData
     */
    public function testNormalize($dataToNormalize, $expectedNormalizedData, ?array $normalizationMapping = []): void
    {
        $serializer = self::getContainer()->get(DomainSerializer::class);
        self::assertEquals($expectedNormalizedData, $serializer->normalize($dataToNormalize, null, [DomainSerializer::NORMALIZATION_MAPPING => $normalizationMapping]));
    }

    public function getNormalizationData(): iterable
    {
        $createdApiAccess = new CreatedApiAccess(42, 'my_secret');
        yield 'normalize command result that contains a ValueObject' => [
            $createdApiAccess,
            [
                'apiAccessId' => 42,
                'secret' => 'my_secret',
            ],
        ];

        $groupId = new GroupId(42);
        yield 'normalize GroupId value object' => [
            $groupId,
            [
                'groupId' => 42,
            ],
        ];

        $productId = new ProductId(42);
        yield 'normalize ProductId value object' => [
            $productId,
            [
                'productId' => 42,
            ],
        ];

        $editableCustomerGroup = new EditableCustomerGroup(
            42,
            [
                1 => 'Group',
                2 => 'Groupe',
            ],
            new DecimalNumber('10.67'),
            false,
            true,
            [
                1,
            ],
        );
        yield 'normalize object with displayPriceTaxExcluded that is a getter not starting by get' => [
            $editableCustomerGroup,
            [
                'id' => 42,
                'localizedNames' => [
                    1 => 'Group',
                    2 => 'Groupe',
                ],
                'reduction' => 10.67,
                'displayPriceTaxExcluded' => false,
                'showPrice' => true,
                'shopIds' => [
                    1,
                ],
            ],
        ];

        yield 'normalize object with displayPriceTaxExcluded that is a getter not starting by get and with extra mapping' => [
            $editableCustomerGroup,
            [
                'id' => 42,
                'localizedNames' => [
                    1 => 'Group',
                    2 => 'Groupe',
                ],
                'reduction' => 10.67,
                'reductionPercent' => 10.67,
                'displayPriceTaxExcluded' => false,
                'showPrice' => true,
                'shopIds' => [
                    1,
                ],
            ],
            [
                '[reduction]' => '[reductionPercent]',
            ],
        ];
    }
}
