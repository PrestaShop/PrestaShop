<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\WithdrawalRequest\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\UpdateWithdrawalRequestStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\CommandHandler\UpdateWithdrawalRequestStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\CannotSaveWithdrawalRequestException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Entity\Repository\WithdrawalRequestRepository;
use Throwable;

#[AsCommandHandler]
final class UpdateWithdrawalRequestStatusHandler implements UpdateWithdrawalRequestStatusHandlerInterface
{
    public function __construct(
        private readonly WithdrawalRequestRepository $withdrawalRequestRepository,
        private readonly HookDispatcherInterface $hookDispatcher,
    ) {
    }

    /**
     * @throws WithdrawalRequestNotFoundException
     * @throws CannotSaveWithdrawalRequestException
     */
    public function handle(UpdateWithdrawalRequestStatusCommand $command): void
    {
        $withdrawalRequestId = $command->getWithdrawalRequestId()->getValue();
        $withdrawalRequest = $this->withdrawalRequestRepository->findById($withdrawalRequestId);
        if (null === $withdrawalRequest) {
            throw new WithdrawalRequestNotFoundException(
                sprintf('Withdrawal request #%d was not found', $withdrawalRequestId)
            );
        }

        $previousStatus = $withdrawalRequest->getStatus();
        if ($previousStatus === $command->getStatus()->value) {
            return;
        }

        $withdrawalRequest->setStatus($command->getStatus()->value);

        try {
            $this->withdrawalRequestRepository->save($withdrawalRequest);
        } catch (Throwable $e) {
            throw new CannotSaveWithdrawalRequestException(
                sprintf('Could not update the status of withdrawal request #%d', $withdrawalRequestId),
                0,
                $e
            );
        }

        $this->hookDispatcher->dispatchWithParameters('actionWithdrawalRequestStatusUpdate', [
            'id_withdrawal_request' => $withdrawalRequestId,
            'id_order' => $withdrawalRequest->getOrderId(),
            'previous_status' => $previousStatus,
            'new_status' => $withdrawalRequest->getStatus(),
        ]);
    }
}
