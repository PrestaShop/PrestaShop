<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\PartialRefundFormDataHandler;

class PartialRefundFormDataHandlerTest extends TestCase
{
    /**
     * The "shipping_amount" field is an optional TextType: when left empty in the form,
     * Symfony submits null. The handler must not crash and must refund no shipping cost.
     *
     * @dataProvider provideEmptyShippingAmounts
     */
    public function testEmptyShippingAmountRefundsNoShippingCost(array $shippingData): void
    {
        $handledCommand = null;
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->expects($this->once())
            ->method('handle')
            ->willReturnCallback(function ($command) use (&$handledCommand) {
                $handledCommand = $command;
            });

        (new PartialRefundFormDataHandler($commandBus))->update(42, $this->buildFormData($shippingData));

        $this->assertInstanceOf(IssuePartialRefundCommand::class, $handledCommand);
        $this->assertTrue($handledCommand->getShippingCostRefundAmount()->equals(new DecimalNumber('0')));
    }

    public static function provideEmptyShippingAmounts(): iterable
    {
        yield 'null (field left empty)' => [['shipping_amount' => null]];
        yield 'key missing' => [[]];
    }

    public function testShippingAmountIsPassedToTheCommand(): void
    {
        $handledCommand = null;
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->expects($this->once())
            ->method('handle')
            ->willReturnCallback(function ($command) use (&$handledCommand) {
                $handledCommand = $command;
            });

        (new PartialRefundFormDataHandler($commandBus))->update(42, $this->buildFormData(['shipping_amount' => '3.90']));

        $this->assertTrue($handledCommand->getShippingCostRefundAmount()->equals(new DecimalNumber('3.90')));
    }

    private function buildFormData(array $shippingData): array
    {
        return $shippingData + [
            'products' => [],
            'restock' => false,
            'credit_slip' => true,
            'voucher' => false,
        ];
    }
}
