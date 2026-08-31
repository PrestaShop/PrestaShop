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
    public function testItHoldsTheProductIdentity(): void
    {
        $command = new CreateShipment(1, 2, 3, 4, 5, 6);

        $this->assertSame(1, $command->getOrderId()->getValue());
        $this->assertSame(2, $command->getCarrierId()->getValue());
        $this->assertSame(3, $command->getProductId()->getValue());
        $this->assertSame(4, $command->getQuantity());
        $this->assertNotNull($command->getProductCombinationId());
        $this->assertSame(5, $command->getProductCombinationId()->getValue());
        $this->assertNotNull($command->getProductCustomizationId());
        $this->assertSame(6, $command->getProductCustomizationId()->getValue());
    }

    /**
     * The combination used to be built from the quantity, so a shipment created for the wrong combination went
     * unnoticed as long as both happened to be equal.
     */
    public function testItDoesNotConfuseTheCombinationWithTheQuantity(): void
    {
        $command = new CreateShipment(1, 2, 3, 4, 5);

        $this->assertNotNull($command->getProductCombinationId());
        $this->assertSame(5, $command->getProductCombinationId()->getValue());
    }

    public function testItHoldsNoCombinationNorCustomizationByDefault(): void
    {
        $command = new CreateShipment(1, 2, 3, 4);

        $this->assertNull($command->getProductCombinationId());
        $this->assertNull($command->getProductCustomizationId());
    }
}
