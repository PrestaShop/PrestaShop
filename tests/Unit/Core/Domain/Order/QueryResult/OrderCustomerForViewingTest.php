<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        $createdAt = new DateTimeImmutable();

        $instance = new OrderCustomerForViewing(
            0,
            'a',
            'b',
            'c',
            'd',
            $createdAt,
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
        self::assertSame($createdAt, $instance->getAccountRegistrationDate());
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
        $createdAt = new DateTimeImmutable();

        $instance = new OrderCustomerForViewing(
            0,
            'a',
            'b',
            'c',
            'd',
            $createdAt,
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
        self::assertSame($createdAt, $instance->getAccountRegistrationDate());
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
        $createdAt = new DateTimeImmutable();

        $instance = new OrderCustomerForViewing(
            0,
            'a',
            'b',
            'c',
            'd',
            $createdAt,
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
        self::assertSame($createdAt, $instance->getAccountRegistrationDate());
        self::assertSame('e', $instance->getTotalSpentSinceRegistration());
        self::assertSame(1, $instance->getValidOrdersPlaced());
        self::assertSame('f', $instance->getPrivateNote());
        self::assertSame(true, $instance->isGuest());
        self::assertSame(2, $instance->getLanguageId());
        self::assertSame('g', $instance->getApe());
        self::assertSame('h', $instance->getSiret());
    }
}
