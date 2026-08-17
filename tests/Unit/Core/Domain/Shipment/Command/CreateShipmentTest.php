<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Shipment\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\CreateShipment;

class CreateShipmentTest extends TestCase
{
    public function testCombinationIdIsBuiltFromTheCombinationArgument(): void
    {
        $command = new CreateShipment(1, 2, 3, 7, 42);

        $this->assertNotNull($command->getProductCombinationId());
        $this->assertSame(42, $command->getProductCombinationId()->getValue());
        $this->assertSame(7, $command->getQuantity());
    }

    /**
     * @dataProvider provideEmptyCombinationIds
     */
    public function testNoCombinationIsBuiltWhenNoneIsProvided(?int $combinationId): void
    {
        $command = new CreateShipment(1, 2, 3, 7, $combinationId);

        $this->assertNull($command->getProductCombinationId());
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
