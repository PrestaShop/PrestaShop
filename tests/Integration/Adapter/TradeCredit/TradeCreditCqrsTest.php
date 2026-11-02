<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\TradeCredit;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Command\SetCustomerTradeCreditCommand;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Query\GetCustomerTradeCredit;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryResult\CustomerTradeCreditSummary;
use PrestaShopBundle\Entity\CustomerTradeCreditOperation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class TradeCreditCqrsTest extends KernelTestCase
{
    private CommandBusInterface $commandBus;

    private CommandBusInterface $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');
        $this->queryBus = self::getContainer()->get('prestashop.core.query_bus');
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['customer_trade_credit', 'customer_trade_credit_operation']);
        parent::tearDown();
    }

    public function testSetThenGetReturnsBalanceAndOperation(): void
    {
        $this->commandBus->handle(new SetCustomerTradeCreditCommand(1, '60000', '50000', 1));

        /** @var CustomerTradeCreditSummary $summary */
        $summary = $this->queryBus->handle(new GetCustomerTradeCredit(1));

        $this->assertTrue($summary->isGranted());
        $this->assertTrue($summary->getBalance()->equals(new DecimalNumber('60000')));
        $this->assertCount(1, $summary->getOperations());
        $this->assertSame(CustomerTradeCreditOperation::TYPE_ADJUSTMENT, $summary->getOperations()[0]->getType());
        $this->assertTrue($summary->getOperations()[0]->getAmount()->equals(new DecimalNumber('10000')));
    }

    public function testGetForNotGrantedReturnsDefault(): void
    {
        /** @var CustomerTradeCreditSummary $summary */
        $summary = $this->queryBus->handle(new GetCustomerTradeCredit(1));

        $this->assertFalse($summary->isGranted());
        $this->assertNull($summary->getBalance());
        $this->assertTrue($summary->getDefaultAmount()->equals(new DecimalNumber('50000')));
        $this->assertSame('PLN', $summary->getCurrencyIso());
        $this->assertSame([], $summary->getOperations());
    }
}
