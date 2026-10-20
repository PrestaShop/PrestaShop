<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Entity\Enum;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The enum owns the status to badge type mapping, so both the grid column and the
 * detail page badge read it from a single place.
 */
class BusinessEntityStatusTest extends TestCase
{
    /**
     * @dataProvider provideStatusesAndBadgeTypes
     */
    public function testItMapsEveryStatusToItsBadgeType(BusinessEntityStatus $status, string $expected): void
    {
        $this->assertSame($expected, $status->badgeType());
    }

    public static function provideStatusesAndBadgeTypes(): iterable
    {
        yield 'pending' => [BusinessEntityStatus::PENDING, 'info'];
        yield 'active' => [BusinessEntityStatus::ACTIVE, 'success'];
        yield 'inactive' => [BusinessEntityStatus::INACTIVE, 'light-info'];
        yield 'rejected' => [BusinessEntityStatus::REJECTED, 'danger'];
    }

    /**
     * @dataProvider provideStatusesAndLabels
     */
    public function testItTranslatesEveryStatusToItsLabel(BusinessEntityStatus $status, string $expected): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $this->assertSame($expected, $status->trans($translator));
    }

    public static function provideStatusesAndLabels(): iterable
    {
        yield 'pending' => [BusinessEntityStatus::PENDING, 'Pending'];
        yield 'active' => [BusinessEntityStatus::ACTIVE, 'Active'];
        yield 'inactive' => [BusinessEntityStatus::INACTIVE, 'Inactive'];
        yield 'rejected' => [BusinessEntityStatus::REJECTED, 'Rejected'];
    }

    public function testEveryCaseHasABadgeType(): void
    {
        // badgeType() has no default arm on purpose: adding a case without extending the match
        // must break here rather than reach BadgeColumn with an empty type.
        foreach (BusinessEntityStatus::cases() as $status) {
            $this->assertNotSame('', $status->badgeType(), sprintf('%s has no badge type', $status->value));
        }
    }
}
