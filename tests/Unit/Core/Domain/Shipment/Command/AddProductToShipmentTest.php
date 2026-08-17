<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Shipment\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\AddProductToShipment;

class AddProductToShipmentTest extends TestCase
{
    public function testCombinationIdIsBuiltWhenProvided(): void
    {
        $command = new AddProductToShipment(1, 2, 3, 42);

        $this->assertNotNull($command->getCombinationId());
        $this->assertSame(42, $command->getCombinationId()->getValue());
    }

    /**
     * @dataProvider provideEmptyCombinationIds
     */
    public function testNoCombinationIsBuiltWhenNoneIsProvided(?int $combinationId): void
    {
        $command = new AddProductToShipment(1, 2, 3, $combinationId);

        $this->assertNull($command->getCombinationId());
    }

    /**
     * @return iterable<string, array{0: int|null}>
     */
    public static function provideEmptyCombinationIds(): iterable
    {
        yield 'null' => [null];
        yield 'zero' => [0];
    }
}
