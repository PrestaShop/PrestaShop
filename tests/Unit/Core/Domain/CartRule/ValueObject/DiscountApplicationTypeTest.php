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

namespace Tests\Unit\Core\Domain\CartRule\ValueObject;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;

class DiscountApplicationTypeTest extends TestCase
{
    /**
     * @dataProvider getDataToBuildDiscountApplicationType
     *
     * @param string $type
     * @param int|null $productId
     *
     * @return void
     */
    public function testItBuildsDiscountApplicationType(string $type, ?int $productId = null): void
    {
        $discountApplicationType = new DiscountApplicationType($type, $productId);
        Assert::assertSame($type, $discountApplicationType->getType());
        if (null === $productId) {
            Assert::assertNull($discountApplicationType->getProductId());
        } else {
            Assert::assertSame($productId, $discountApplicationType->getProductId()->getValue());
        }
    }

    /**
     * @dataProvider getInvalidData
     *
     * @param string $type
     * @param int|null $productId
     * @param string $expectedException
     * @param int $expectedCode
     *
     * @return void
     */
    public function testItThrowsExceptionWhenInvalidDataIsProvided(
        string $type,
        ?int $productId,
        string $expectedException,
        int $expectedCode
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionCode($expectedCode);

        new DiscountApplicationType($type, $productId);
    }

    public function getDataToBuildDiscountApplicationType(): iterable
    {
        yield [
            DiscountApplicationType::ORDER_WITHOUT_SHIPPING,
        ];

        yield [
            DiscountApplicationType::SELECTED_PRODUCTS,
        ];

        yield [
            DiscountApplicationType::CHEAPEST_PRODUCT,
        ];

        yield [
            DiscountApplicationType::SPECIFIC_PRODUCT,
            12,
        ];
    }

    public function getInvalidData(): iterable
    {
        yield [
            'random',
            null,
            CartRuleConstraintException::class,
            CartRuleConstraintException::INVALID_DISCOUNT_APPLICATION_TYPE,
        ];

        yield [
            DiscountApplicationType::SPECIFIC_PRODUCT,
            null,
            CartRuleConstraintException::class,
            CartRuleConstraintException::MISSING_DISCOUNT_APPLICATION_PRODUCT,
        ];
    }
}
