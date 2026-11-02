<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TradeCredit\CommandHandler;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Command\SetCustomerTradeCreditCommand;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\CommandHandler\SetCustomerTradeCreditHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditBalanceChangedException;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\TradeCreditAvailabilityChecker;
use PrestaShopBundle\Entity\CustomerTradeCreditOperation;
use PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository;

#[AsCommandHandler]
final class SetCustomerTradeCreditHandler implements SetCustomerTradeCreditHandlerInterface
{
    public function __construct(
        private readonly CustomerTradeCreditRepository $repository,
        private readonly ConfigurationInterface $configuration
    ) {
    }

    public function handle(SetCustomerTradeCreditCommand $command): void
    {
        $customerId = $command->getCustomerId()->getValue();
        $amount = $this->format($command->getAmount()->getValue());
        $expected = $this->format($command->getExpectedAmount());

        try {
            $this->repository->transactional(function () use ($customerId, $amount, $expected, $command): void {
                $balance = $this->repository->lockBalance($customerId);
                $current = $this->format(new DecimalNumber(
                    $balance ?? (string) $this->configuration->get('PS_TRADE_CREDIT_DEFAULT_AMOUNT')
                ));

                if ($current !== $expected) {
                    throw new TradeCreditBalanceChangedException(sprintf('Trade credit of customer %d changed from %s to %s.', $customerId, $expected, $current));
                }

                if ($amount === $current) {
                    return;
                }

                if (null === $balance) {
                    $this->repository->createBalance($customerId, $amount);
                } else {
                    $this->repository->updateBalance($customerId, $amount);
                }

                $this->repository->addOperation([
                    'id_customer' => $customerId,
                    'type' => CustomerTradeCreditOperation::TYPE_ADJUSTMENT,
                    'amount' => $this->format((new DecimalNumber($amount))->minus(new DecimalNumber($current))),
                    'balance_after' => $amount,
                    'id_employee' => $command->getEmployeeId() ?: null,
                    'reference' => 'adjustment:' . bin2hex(random_bytes(8)),
                ]);
            });
        } catch (UniqueConstraintViolationException $e) {
            // The credit was granted by an order between reading and writing the balance.
            throw new TradeCreditBalanceChangedException(sprintf('Trade credit of customer %d was granted in the meantime.', $customerId), 0, $e);
        }
    }

    private function format(DecimalNumber $amount): string
    {
        return $amount->toPrecision(TradeCreditAvailabilityChecker::PRECISION, Rounding::ROUND_HALF_UP);
    }
}
