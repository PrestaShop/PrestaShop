<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Shipment\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentStatus;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShipmentStatusTest extends TestCase
{
    public function testEveryCaseHasABadgeTypeTheGridCanRender(): void
    {
        // Values allowed by BadgeColumn::configureOptions()
        $allowedBadgeTypes = ['success', 'info', 'danger', 'warning', 'light-info'];

        foreach (ShipmentStatus::cases() as $status) {
            $this->assertContains($status->getBadgeType(), $allowedBadgeTypes, $status->value);
        }
    }

    public function testEveryCaseIsTranslated(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        foreach (ShipmentStatus::cases() as $status) {
            $this->assertNotSame('', $status->trans($translator), $status->value);
        }
    }

    public function testSqlExpressionEvaluatesTheMostAdvancedStateFirst(): void
    {
        $expression = ShipmentStatus::getSqlExpression();

        $positions = [];
        foreach (ShipmentStatus::cases() as $status) {
            $position = strpos($expression, sprintf("'%s'", $status->value));
            $this->assertNotFalse($position, sprintf('Status "%s" is missing from the SQL expression', $status->value));
            $positions[] = $position;
        }

        $sorted = $positions;
        sort($sorted);

        // ShipmentStatus cases are declared from the most to the least advanced state: a cancelled
        // shipment carrying a shipped_at date must still be reported as cancelled.
        $this->assertSame($sorted, $positions);
    }

    public function testSqlExpressionReadsTheTimestampsOfTheGivenTableAlias(): void
    {
        $expression = ShipmentStatus::getSqlExpression('sh');

        foreach (['cancelled_at', 'delivered_at', 'shipped_at', 'packed_at'] as $column) {
            $this->assertStringContainsString(sprintf('sh.%s', $column), $expression);
        }
    }
}
