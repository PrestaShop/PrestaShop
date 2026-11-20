<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use Exception;
use PrestaShop\PrestaShop\Adapter\Customer\PasswordSetupLinkMailer;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SendCustomerPasswordSetupLinkCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\SendCustomerPasswordSetupLinkHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CannotSendPasswordSetupLinkException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;

#[AsCommandHandler]
final class SendCustomerPasswordSetupLinkHandler implements SendCustomerPasswordSetupLinkHandlerInterface
{
    public function __construct(
        private readonly CustomerRepository $repository,
        private readonly PasswordSetupLinkMailer $mailer,
    ) {
    }

    /**
     * @throws CannotSendPasswordSetupLinkException
     * @throws CustomerNotFoundException
     */
    public function handle(SendCustomerPasswordSetupLinkCommand $command): void
    {
        $customer = $this->repository->get($command->customerId);
        $this->assertCustomerCanSetUpPassword($customer);

        if (!$customer->hasRecentResetPasswordToken()) {
            $this->generateResetToken($customer);
        }

        if (!$this->mailer->send($customer)) {
            throw new CannotSendPasswordSetupLinkException(\sprintf('Failed to send the password setup link to customer #%d.', $customer->id));
        }
    }

    /**
     * @throws CannotSendPasswordSetupLinkException
     */
    private function assertCustomerCanSetUpPassword(Customer $customer): void
    {
        if ($customer->deleted) {
            throw new CannotSendPasswordSetupLinkException(\sprintf('Customer #%d is deleted.', $customer->id), CannotSendPasswordSetupLinkException::DELETED_CUSTOMER);
        }

        if (!$customer->active) {
            throw new CannotSendPasswordSetupLinkException(\sprintf('Customer #%d is disabled.', $customer->id), CannotSendPasswordSetupLinkException::INACTIVE_CUSTOMER);
        }

        if ($customer->isGuest()) {
            throw new CannotSendPasswordSetupLinkException(\sprintf('Customer #%d is a guest.', $customer->id), CannotSendPasswordSetupLinkException::GUEST_CUSTOMER);
        }
    }

    private function generateResetToken(Customer $customer): void
    {
        $customer->stampResetPasswordToken();

        try {
            $result = $customer->update();
        } catch (Exception $e) {
            $result = false;
        }

        if (!$result) {
            throw new CannotSendPasswordSetupLinkException(\sprintf('Failed to store the password reset token of customer #%d.', $customer->id), 0, $e ?? null);
        }
    }
}
