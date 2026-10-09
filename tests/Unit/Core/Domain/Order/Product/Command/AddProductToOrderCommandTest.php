<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Order\Product\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;

class AddProductToOrderCommandTest extends TestCase
{
    public function testConstructorMatchesWithNewInvoiceFactory(): void
    {
        $direct = new AddProductToOrderCommand(1, 2, 3, '10.00', '8.00', 4, null, true);
        $factory = AddProductToOrderCommand::withNewInvoice(1, 2, 3, '10.00', '8.00', 4, true);

        $this->assertEquals($factory->getOrderId()->getValue(), $direct->getOrderId()->getValue());
        $this->assertEquals($factory->getProductId()->getValue(), $direct->getProductId()->getValue());
        $this->assertEquals($factory->getCombinationId()->getValue(), $direct->getCombinationId()->getValue());
        $this->assertSame(
            $factory->getProductPriceTaxIncluded()->__toString(),
            $direct->getProductPriceTaxIncluded()->__toString()
        );
        $this->assertSame(
            $factory->getProductPriceTaxExcluded()->__toString(),
            $direct->getProductPriceTaxExcluded()->__toString()
        );
        $this->assertSame($factory->getProductQuantity(), $direct->getProductQuantity());
        $this->assertSame($factory->getOrderInvoiceId(), $direct->getOrderInvoiceId());
        $this->assertNull($direct->getOrderInvoiceId());
        $this->assertSame($factory->hasFreeShipping(), $direct->hasFreeShipping());
        $this->assertTrue($direct->hasFreeShipping());
    }

    public function testConstructorMatchesToExistingInvoiceFactory(): void
    {
        $direct = new AddProductToOrderCommand(1, 2, 3, '10.00', '8.00', 4, 99);
        $factory = AddProductToOrderCommand::toExistingInvoice(1, 99, 2, 3, '10.00', '8.00', 4);

        $this->assertEquals($factory->getOrderId()->getValue(), $direct->getOrderId()->getValue());
        $this->assertEquals($factory->getProductId()->getValue(), $direct->getProductId()->getValue());
        $this->assertEquals($factory->getCombinationId()->getValue(), $direct->getCombinationId()->getValue());
        $this->assertSame(
            $factory->getProductPriceTaxIncluded()->__toString(),
            $direct->getProductPriceTaxIncluded()->__toString()
        );
        $this->assertSame(
            $factory->getProductPriceTaxExcluded()->__toString(),
            $direct->getProductPriceTaxExcluded()->__toString()
        );
        $this->assertSame($factory->getProductQuantity(), $direct->getProductQuantity());
        $this->assertSame(99, $direct->getOrderInvoiceId());
        $this->assertSame($factory->getOrderInvoiceId(), $direct->getOrderInvoiceId());
        $this->assertNull($direct->hasFreeShipping());
    }

    public function testConstructorWithoutOptionalArgs(): void
    {
        $instance = new AddProductToOrderCommand(1, 2, 0, '10.00', '8.00', 4);

        $this->assertEquals(1, $instance->getOrderId()->getValue());
        $this->assertEquals(2, $instance->getProductId()->getValue());
        $this->assertNull($instance->getCombinationId());
        $this->assertSame(4, $instance->getProductQuantity());
        $this->assertNull($instance->getOrderInvoiceId());
        $this->assertNull($instance->hasFreeShipping());
    }
}
