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

namespace Tests\Core\Domain\Cart\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartAddress;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartProduct;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartRule;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartShipping;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartSummary;

class CartForOrderCreationTest extends TestCase
{
    public function testConstruct(): void
    {
        $mockSummary = $this->createMock(CartSummary::class);
        $mockShipping = $this->createMock(CartShipping::class);
        $cartProduct = $this->createMock(CartProduct::class);
        $cartAddress = $this->createMock(CartAddress::class);
        $cartRule = $this->createMock(CartRule::class);

        $products = [
            $cartProduct,
        ];

        $cartAddresses = [
            $cartAddress,
        ];

        $cartRules = [
            $cartRule,
        ];

        $instance = new CartForOrderCreation(
            1000,
            $products,
            2000,
            3000,
            $cartRules,
            $cartAddresses,
            $mockSummary,
            $mockShipping,
            4000
        );

        self::assertSame(1000, $instance->getCartId());
        self::assertContainsOnlyInstancesOf(CartProduct::class, $instance->getProducts());
        self::assertSame(2000, $instance->getCurrencyId());
        self::assertSame(3000, $instance->getLangId());
        self::assertContainsOnlyInstancesOf(CartRule::class, $instance->getCartRules());
        self::assertContainsOnlyInstancesOf(CartAddress::class, $instance->getAddresses());
        self::assertSame($mockSummary, $instance->getSummary());
        self::assertSame($mockShipping, $instance->getShipping());
        self::assertSame(4000, $instance->getCustomerId());
    }
}
