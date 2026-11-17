<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\WithdrawalRequest;

use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Hook;
use PrestaShop\PrestaShop\Adapter\WithdrawalRequest\Repository\OrderWithdrawalDataRepository;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\SubmitWithdrawalRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\CannotSaveWithdrawalRequestException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\OrderNotEligibleForWithdrawalException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalIneligibilityReason;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalRequestId;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalRequestStatus;
use PrestaShopBundle\Entity\Repository\WithdrawalRequestRepository;
use PrestaShopBundle\Entity\WithdrawalRequest;
use Throwable;

/**
 * Records the withdrawal statement of a consumer, then sends the acknowledgement of receipt on a durable medium
 * (email) and notifies the merchant.
 *
 * It backs {@see CommandHandler\SubmitWithdrawalRequestHandler},
 * and is also used directly by the front office withdrawal page: the legacy front office container has no command
 * bus. That is also why the hook goes through the legacy Hook class.
 *
 * Checking that the person submitting the statement is allowed to act on the order (logged in owner, or guest
 * who proved the order reference and email) is the caller's job, like for the guest tracking page.
 */
final class WithdrawalRequestSubmitter
{
    public function __construct(
        private readonly WithdrawalEligibilityChecker $eligibilityChecker,
        private readonly OrderWithdrawalDataRepository $orderWithdrawalDataRepository,
        private readonly WithdrawalRequestRepository $withdrawalRequestRepository,
        private readonly WithdrawalRequestNotifier $notifier,
    ) {
    }

    /**
     * @throws OrderNotFoundException
     * @throws OrderNotEligibleForWithdrawalException
     * @throws CannotSaveWithdrawalRequestException
     */
    public function submit(SubmitWithdrawalRequestCommand $command): WithdrawalRequestId
    {
        // A statement submitted before the end of the period is on time (Directive 2011/83/EU, article 11a.4)
        $eligibility = $this->eligibilityChecker->check($command->getOrderId(), $command->getSubmittedAt());
        $ineligibilityReason = $eligibility->getIneligibilityReason();
        if (null !== $ineligibilityReason) {
            throw new OrderNotEligibleForWithdrawalException(
                $ineligibilityReason,
                sprintf('Order #%d cannot be withdrawn from: %s', $command->getOrderId()->getValue(), $ineligibilityReason->value)
            );
        }

        $order = $this->orderWithdrawalDataRepository->getOrderData($command->getOrderId());
        $message = trim($command->getMessage());

        $withdrawalRequest = (new WithdrawalRequest())
            ->setShopId($order['id_shop'])
            ->setOrderId($order['id_order'])
            ->setCustomerId($order['id_customer'])
            ->setOrderReference($order['reference'])
            ->setFirstname(trim($command->getFirstname()))
            ->setLastname(trim($command->getLastname()))
            ->setEmail($command->getEmail()->getValue())
            ->setMessage('' === $message ? null : $message)
            ->setStatus(WithdrawalRequestStatus::PENDING->value)
            ->setSubmittedAt($command->getSubmittedAt());

        try {
            $this->withdrawalRequestRepository->save($withdrawalRequest);
        } catch (UniqueConstraintViolationException $e) {
            // Two submissions for the same order raced, the first one won
            throw new OrderNotEligibleForWithdrawalException(
                WithdrawalIneligibilityReason::ALREADY_REQUESTED,
                sprintf('A withdrawal request already exists for order #%d', $order['id_order']),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new CannotSaveWithdrawalRequestException(
                sprintf('Could not save the withdrawal request of order #%d', $order['id_order']),
                0,
                $e
            );
        }

        if ($this->notifier->sendAcknowledgement($withdrawalRequest, $order['id_lang'])) {
            $withdrawalRequest->setAcknowledgedAt(new DateTimeImmutable());
            $this->withdrawalRequestRepository->save($withdrawalRequest);
        }
        $this->notifier->notifyMerchant($withdrawalRequest);

        Hook::exec('actionWithdrawalRequestSubmitted', [
            'id_withdrawal_request' => $withdrawalRequest->getId(),
            'id_order' => $withdrawalRequest->getOrderId(),
            'withdrawal_request' => $withdrawalRequest,
        ]);

        return new WithdrawalRequestId($withdrawalRequest->getId());
    }
}
