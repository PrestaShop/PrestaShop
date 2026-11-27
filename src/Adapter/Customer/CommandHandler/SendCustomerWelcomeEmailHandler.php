<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use Exception;
use LogicException;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerWelcomeMailer;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SendCustomerWelcomeEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\SendCustomerWelcomeEmailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\CustomerAccountOrigin;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CannotSendCustomerWelcomeEmailException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerWelcomeEmailConstraintException;

#[AsCommandHandler]
final class SendCustomerWelcomeEmailHandler implements SendCustomerWelcomeEmailHandlerInterface
{
    public function __construct(
        private readonly CustomerRepository $repository,
        private readonly CustomerWelcomeMailer $mailer,
    ) {
    }

    /**
     * @throws CannotSendCustomerWelcomeEmailException
     * @throws CustomerWelcomeEmailConstraintException
     * @throws CustomerNotFoundException
     * @throws LogicException if the given origin is not supported
     */
    public function handle(SendCustomerWelcomeEmailCommand $command): void
    {
        if (CustomerAccountOrigin::BACK_OFFICE !== $command->origin) {
            throw new LogicException('Not implemented');
        }

        $customer = $this->repository->get($command->customerId);

        $this->assertCustomerHasAnActiveAccount($customer);

        if (!$command->origin->wasPasswordSetByCustomer() && !$customer->hasRecentResetPasswordToken()) {
            $this->generateResetToken($customer);
        }

        try {
            $result = $this->mailer->send($customer, $command->origin);
        } catch (Exception $e) {
            $result = false;
        }

        if (!$result) {
            throw new CannotSendCustomerWelcomeEmailException(\sprintf('Failed to send the welcome email to customer #%d.', $customer->id), 0, $e ?? null);
        }
    }

    /**
     * @throws CustomerWelcomeEmailConstraintException
     */
    private function assertCustomerHasAnActiveAccount(Customer $customer): void
    {
        if ($customer->deleted) {
            throw new CustomerWelcomeEmailConstraintException(\sprintf('Cannot send a welcome email to a deleted customer #%d.', $customer->id), CustomerWelcomeEmailConstraintException::DELETED_CUSTOMER);
        }

        if (!$customer->active) {
            throw new CustomerWelcomeEmailConstraintException(\sprintf('Cannot send a welcome email to an inactive customer #%d.', $customer->id), CustomerWelcomeEmailConstraintException::INACTIVE_CUSTOMER);
        }

        if ($customer->isGuest()) {
            throw new CustomerWelcomeEmailConstraintException(\sprintf('Cannot send a welcome email to a guest customer #%d.', $customer->id), CustomerWelcomeEmailConstraintException::GUEST_CUSTOMER);
        }
    }

    /**
     * @throws CannotSendCustomerWelcomeEmailException
     */
    private function generateResetToken(Customer $customer): void
    {
        $customer->stampResetPasswordToken();

        try {
            $result = $customer->update();
        } catch (Exception $e) {
            $result = false;
        }

        if (!$result) {
            throw new CannotSendCustomerWelcomeEmailException(\sprintf('Failed to store the password reset token of customer #%d.', $customer->id), 0, $e ?? null);
        }
    }
}
