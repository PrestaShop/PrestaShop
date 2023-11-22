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

namespace Tests\Unit\PrestaShopBundle\ApiPlatform;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Module\APIResources\ApiPlatform\Resources\ApiAccess;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShopBundle\ApiPlatform\DomainSerializer;
use PrestaShopBundle\ApiPlatform\Normalizer\DateTimeImmutableDenormalizer;
use PrestaShopBundle\ApiPlatform\Normalizer\DecimalNumberDenormalizer;
use PrestaShopBundle\ApiPlatform\Normalizer\ObjectDenormalizer;

class DomainSerializerTest extends TestCase
{
    /**
     * @var DomainSerializer
     */
    private DomainSerializer $serializer;

    /**
     * Set up dependencies for HookStatusProvider
     */
    public function setUp(): void
    {
        $denormalizers = new \ArrayIterator([new DateTimeImmutableDenormalizer(), new DecimalNumberDenormalizer(), new ObjectDenormalizer()]);
        $this->serializer = new DomainSerializer($denormalizers);
    }

    /**
     * @dataProvider getExpectedDenormalizedData
     */
    public function testDenormalize($dataToDenormalize, $denormalizedObject): void
    {
        self::assertEquals($denormalizedObject, $this->serializer->denormalize($dataToDenormalize, get_class($denormalizedObject)));
    }

    public function getExpectedDenormalizedData()
    {
        $normalizedCreatedApiAccess = [
            'apiAccessId' => [
                'value' => 42,
            ],
            'secret' => 'my_secret',
        ];
        $apiResource = new ApiAccess();
        $apiResource->apiAccessId = 42;
        $apiResource->secret = 'my_secret';
        yield [
            $normalizedCreatedApiAccess,
            $apiResource,
        ];

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
    }

    /**
     * @dataProvider getNormalizationData
     */
    public function testNormalize($dataToNormalize, $expectedNormalizedData, ?array $normalizationMapping = []): void
    {
        self::assertEquals($expectedNormalizedData, $this->serializer->normalize($dataToNormalize, null, [DomainSerializer::NORMALIZATION_MAPPING => $normalizationMapping]));
    }

    public function getNormalizationData(): iterable
    {
        $groupId = new GroupId(42);
        yield 'normalize value object' => [
            $groupId,
            [
                'value' => 42,
            ],
        ];

        $groupId = new GroupId(42);
        yield 'normalize value object with mapping null' => [
            $groupId,
            [
                'value' => 42,
            ],
            null,
        ];

        $groupId = new GroupId(42);
        yield 'normalize value object with mapping for value' => [
            $groupId,
            [
                'value' => 42,
                'customerGroupId' => 42,
            ],
            [
                '[value]' => '[customerGroupId]',
            ],
        ];
    }
}
