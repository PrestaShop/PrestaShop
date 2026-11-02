<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TradeCredit\QueryHandler;

use Currency;
use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Query\GetCustomerTradeCredit;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryHandler\GetCustomerTradeCreditHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryResult\CustomerTradeCreditSummary;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryResult\TradeCreditOperation;
use PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository;

#[AsQueryHandler]
final class GetCustomerTradeCreditHandler implements GetCustomerTradeCreditHandlerInterface
{
    private const LAST_OPERATIONS_LIMIT = 10;

    public function __construct(
        private readonly CustomerTradeCreditRepository $repository,
        private readonly ConfigurationInterface $configuration
    ) {
    }

    public function handle(GetCustomerTradeCredit $query): CustomerTradeCreditSummary
    {
        $customerId = $query->getCustomerId()->getValue();
        $balance = $this->repository->findBalance($customerId);

        $operations = array_map(
            static fn (array $row): TradeCreditOperation => new TradeCreditOperation(
                new DateTimeImmutable($row['date_add']),
                $row['type'],
                new DecimalNumber((string) $row['amount']),
                new DecimalNumber((string) $row['balance_after']),
                null === $row['id_order'] ? null : (int) $row['id_order']
            ),
            $this->repository->findLastOperations($customerId, self::LAST_OPERATIONS_LIMIT)
        );

        $currencyIso = (string) $this->configuration->get('PS_TRADE_CREDIT_CURRENCY');

        return new CustomerTradeCreditSummary(
            null === $balance ? null : new DecimalNumber($balance),
            new DecimalNumber((string) $this->configuration->get('PS_TRADE_CREDIT_DEFAULT_AMOUNT')),
            $currencyIso,
            $operations,
            (int) Currency::getIdByIsoCode($currencyIso) > 0
        );
    }
}
