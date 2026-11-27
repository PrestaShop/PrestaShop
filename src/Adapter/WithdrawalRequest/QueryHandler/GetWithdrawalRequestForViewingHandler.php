<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\WithdrawalRequest\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Query\GetWithdrawalRequestForViewing;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryHandler\GetWithdrawalRequestForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryResult\WithdrawalRequestForViewing;
use PrestaShopBundle\Entity\Repository\WithdrawalRequestRepository;

#[AsQueryHandler]
final class GetWithdrawalRequestForViewingHandler implements GetWithdrawalRequestForViewingHandlerInterface
{
    public function __construct(
        private readonly WithdrawalRequestRepository $withdrawalRequestRepository,
    ) {
    }

    /**
     * @throws WithdrawalRequestNotFoundException
     */
    public function handle(GetWithdrawalRequestForViewing $query): WithdrawalRequestForViewing
    {
        $withdrawalRequestId = $query->getWithdrawalRequestId()->getValue();
        $withdrawalRequest = $this->withdrawalRequestRepository->findById($withdrawalRequestId);
        if (null === $withdrawalRequest) {
            throw new WithdrawalRequestNotFoundException(sprintf('Withdrawal request #%d was not found', $withdrawalRequestId));
        }

        return new WithdrawalRequestForViewing(
            $withdrawalRequest->getId(),
            $withdrawalRequest->getShopId(),
            $withdrawalRequest->getOrderId(),
            $withdrawalRequest->getCustomerId(),
            $withdrawalRequest->getOrderReference(),
            $withdrawalRequest->getFirstname(),
            $withdrawalRequest->getLastname(),
            $withdrawalRequest->getEmail(),
            (string) $withdrawalRequest->getMessage(),
            $withdrawalRequest->getStatus(),
            $withdrawalRequest->getSubmittedAt(),
            $withdrawalRequest->getAcknowledgedAt()
        );
    }
}
