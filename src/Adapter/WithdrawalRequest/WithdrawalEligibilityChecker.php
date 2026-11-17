<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\WithdrawalRequest;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\WithdrawalRequest\Repository\OrderWithdrawalDataRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryResult\OrderWithdrawalEligibility;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Service\WithdrawalDeadlineCalculator;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalIneligibilityReason;
use PrestaShopBundle\Entity\Repository\WithdrawalRequestRepository;

/**
 * Decides whether the consumer can still withdraw from an order through the withdrawal function.
 *
 * Shared by the eligibility query and the submit command, so the page that shows the button and the handler
 * that records the statement can never disagree.
 */
final class WithdrawalEligibilityChecker
{
    public const CONFIGURATION_ENABLED = 'PS_WITHDRAWAL_FUNCTION';
    public const CONFIGURATION_PERIOD = 'PS_WITHDRAWAL_PERIOD';

    public function __construct(
        private readonly OrderWithdrawalDataRepository $orderWithdrawalDataRepository,
        private readonly WithdrawalRequestRepository $withdrawalRequestRepository,
        private readonly ShopConfigurationInterface $configuration,
        private readonly WithdrawalDeadlineCalculator $deadlineCalculator,
    ) {
    }

    /**
     * @throws OrderNotFoundException
     */
    public function check(OrderId $orderId, DateTimeImmutable $now): OrderWithdrawalEligibility
    {
        $order = $this->orderWithdrawalDataRepository->getOrderData($orderId);
        $shopConstraint = ShopConstraint::shop($order['id_shop']);

        $periodDays = (int) $this->configuration->get(
            self::CONFIGURATION_PERIOD,
            WithdrawalDeadlineCalculator::DEFAULT_PERIOD_DAYS,
            $shopConstraint
        );
        if (0 >= $periodDays) {
            $periodDays = WithdrawalDeadlineCalculator::DEFAULT_PERIOD_DAYS;
        }

        $deliveredAt = $this->orderWithdrawalDataRepository->findDeliveryDate(
            $orderId,
            (int) $this->configuration->get('PS_OS_DELIVERED', 0, $shopConstraint)
        );
        $deadline = $this->deadlineCalculator->getDeadline($deliveredAt, $periodDays);

        $existingRequest = $this->withdrawalRequestRepository->findOneByOrderId($order['id_order']);
        if (null !== $existingRequest) {
            return new OrderWithdrawalEligibility(
                $order['id_order'],
                WithdrawalIneligibilityReason::ALREADY_REQUESTED,
                $deadline,
                $existingRequest->getId(),
                $existingRequest->getSubmittedAt()
            );
        }

        $reason = null;
        if (!$this->configuration->get(self::CONFIGURATION_ENABLED, false, $shopConstraint)) {
            $reason = WithdrawalIneligibilityReason::FEATURE_DISABLED;
        } elseif ($this->isCancelled($order['current_state'], $shopConstraint)) {
            $reason = WithdrawalIneligibilityReason::ORDER_CANCELLED;
        } elseif (null !== $deadline && $now > $deadline) {
            $reason = WithdrawalIneligibilityReason::PERIOD_EXPIRED;
        }

        return new OrderWithdrawalEligibility($order['id_order'], $reason, $deadline);
    }

    private function isCancelled(int $currentState, ShopConstraint $shopConstraint): bool
    {
        foreach (['PS_OS_CANCELED', 'PS_OS_ERROR'] as $stateConfigurationKey) {
            if ($currentState === (int) $this->configuration->get($stateConfigurationKey, 0, $shopConstraint)) {
                return true;
            }
        }

        return false;
    }
}
