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

namespace Tests\Core\Domain\Order\QueryResult;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCustomerForViewing;

class OrderCustomerForViewingTest extends TestCase
{
    public function testConstruct(): void
    {
        $mockCreatedAt = $this->createMock(DateTimeImmutable::class);

        $instance = new OrderCustomerForViewing(
            0,
            'a',
            'b',
            'c',
            'd',
            $mockCreatedAt,
            'e',
            1,
            'f',
            true,
            2
        );

        self::assertSame(0, $instance->getId());
        self::assertSame('a', $instance->getFirstName());
        self::assertSame('b', $instance->getLastName());
        self::assertSame('c', $instance->getGender());
        self::assertSame('d', $instance->getEmail());
        self::assertSame($mockCreatedAt, $instance->getAccountRegistrationDate());
        self::assertSame('e', $instance->getTotalSpentSinceRegistration());
        self::assertSame(1, $instance->getValidOrdersPlaced());
        self::assertSame('f', $instance->getPrivateNote());
        self::assertSame(true, $instance->isGuest());
        self::assertSame(2, $instance->getLanguageId());
        self::assertSame('', $instance->getApe());
        self::assertSame('', $instance->getSiret());
    }

    public function testConstructWithApe(): void
    {
        $mockCreatedAt = $this->createMock(DateTimeImmutable::class);

        $instance = new OrderCustomerForViewing(
            0,
            'a',
            'b',
            'c',
            'd',
            $mockCreatedAt,
            'e',
            1,
            'f',
            true,
            2,
            'g'
        );

        self::assertSame(0, $instance->getId());
        self::assertSame('a', $instance->getFirstName());
        self::assertSame('b', $instance->getLastName());
        self::assertSame('c', $instance->getGender());
        self::assertSame('d', $instance->getEmail());
        self::assertSame($mockCreatedAt, $instance->getAccountRegistrationDate());
        self::assertSame('e', $instance->getTotalSpentSinceRegistration());
        self::assertSame(1, $instance->getValidOrdersPlaced());
        self::assertSame('f', $instance->getPrivateNote());
        self::assertSame(true, $instance->isGuest());
        self::assertSame(2, $instance->getLanguageId());
        self::assertSame('g', $instance->getApe());
        self::assertSame('', $instance->getSiret());
    }

    public function testConstructWithSiret(): void
    {
        $mockCreatedAt = $this->createMock(DateTimeImmutable::class);

        $instance = new OrderCustomerForViewing(
            0,
            'a',
            'b',
            'c',
            'd',
            $mockCreatedAt,
            'e',
            1,
            'f',
            true,
            2,
            'g',
            'h'
        );

        self::assertSame(0, $instance->getId());
        self::assertSame('a', $instance->getFirstName());
        self::assertSame('b', $instance->getLastName());
        self::assertSame('c', $instance->getGender());
        self::assertSame('d', $instance->getEmail());
        self::assertSame($mockCreatedAt, $instance->getAccountRegistrationDate());
        self::assertSame('e', $instance->getTotalSpentSinceRegistration());
        self::assertSame(1, $instance->getValidOrdersPlaced());
        self::assertSame('f', $instance->getPrivateNote());
        self::assertSame(true, $instance->isGuest());
        self::assertSame(2, $instance->getLanguageId());
        self::assertSame('g', $instance->getApe());
        self::assertSame('h', $instance->getSiret());
    }
}
