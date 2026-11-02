<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\TradeCredit\CommandHandler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\TradeCredit\CommandHandler\SetCustomerTradeCreditHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Command\SetCustomerTradeCreditCommand;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditBalanceChangedException;
use PrestaShopBundle\Entity\CustomerTradeCreditOperation;
use PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository;

class SetCustomerTradeCreditHandlerTest extends TestCase
{
    private const CUSTOMER_ID = 1;
    private const EMPLOYEE_ID = 7;

    private CustomerTradeCreditRepository&MockObject $repository;

    private SetCustomerTradeCreditHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CustomerTradeCreditRepository::class);
        $this->repository->method('transactional')->willReturnCallback(fn (callable $callable) => $callable());

        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnMap([['PS_TRADE_CREDIT_DEFAULT_AMOUNT', '50000']]);

        $this->handler = new SetCustomerTradeCreditHandler($this->repository, $configuration);
    }

    public function testWritesAdjustmentWithDelta(): void
    {
        $this->repository->method('lockBalance')->willReturn('50000.000000');
        $this->repository->expects($this->once())->method('updateBalance')->with(self::CUSTOMER_ID, '60000.00');
        $this->repository->expects($this->never())->method('createBalance');
        $this->repository->expects($this->once())->method('addOperation')->with($this->callback(function (array $row): bool {
            return CustomerTradeCreditOperation::TYPE_ADJUSTMENT === $row['type']
                && '10000.00' === $row['amount']
                && '60000.00' === $row['balance_after']
                && self::EMPLOYEE_ID === $row['id_employee']
                && str_starts_with($row['reference'], 'adjustment:');
        }))->willReturn(true);

        $this->handler->handle($this->command('60000', '50000'));
    }

    public function testNoChangeWritesNothing(): void
    {
        $this->repository->method('lockBalance')->willReturn('50000.000000');
        $this->repository->expects($this->never())->method('updateBalance');
        $this->repository->expects($this->never())->method('createBalance');
        $this->repository->expects($this->never())->method('addOperation');

        $this->handler->handle($this->command('50000.00', '50000'));
    }

    public function testNotGrantedComparesAgainstDefault(): void
    {
        $this->repository->method('lockBalance')->willReturn(null);
        $this->repository->expects($this->never())->method('grantIfMissing');
        $this->repository->expects($this->once())->method('createBalance')->with(self::CUSTOMER_ID, '20000.00');
        $this->repository->expects($this->once())->method('addOperation')->with($this->callback(
            fn (array $row): bool => '-30000.00' === $row['amount'] && '20000.00' === $row['balance_after']
        ))->willReturn(true);

        $this->handler->handle($this->command('20000', '50000'));
    }

    public function testBalanceChangedThrows(): void
    {
        $this->repository->method('lockBalance')->willReturn('40000.000000');
        $this->repository->expects($this->never())->method('updateBalance');
        $this->repository->expects($this->never())->method('addOperation');

        $this->expectException(TradeCreditBalanceChangedException::class);

        $this->handler->handle($this->command('60000', '50000'));
    }

    private function command(string $amount, string $expectedAmount): SetCustomerTradeCreditCommand
    {
        return new SetCustomerTradeCreditCommand(self::CUSTOMER_ID, $amount, $expectedAmount, self::EMPLOYEE_ID);
    }
}
