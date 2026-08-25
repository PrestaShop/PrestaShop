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
    public function testItHoldsTheProductIdentity(): void
    {
        $command = new AddProductToShipment(1, 2, 3, 4, 5);

        $this->assertSame(1, $command->getShipmentId()->getValue());
        $this->assertSame(2, $command->getProductId()->getValue());
        $this->assertSame(3, $command->getOrderId()->getValue());
        $this->assertNotNull($command->getCombinationId());
        $this->assertSame(4, $command->getCombinationId()->getValue());
        $this->assertNotNull($command->getCustomizationId());
        $this->assertSame(5, $command->getCustomizationId()->getValue());
    }

    public function testItHoldsNoCombinationNorCustomizationByDefault(): void
    {
        $command = new AddProductToShipment(1, 2, 3);

        $this->assertNull($command->getCombinationId());
        $this->assertNull($command->getCustomizationId());
    }
}
