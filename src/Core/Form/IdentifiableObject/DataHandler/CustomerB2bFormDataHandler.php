<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use LogicException;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\DeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SendCustomerPasswordSetupLinkCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CannotSendPasswordSetupLinkException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerDeleteMethod;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command\AddCustomerB2bCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\PasswordSetupLinkNotSentException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\ValueObject\CustomerB2bId;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;
use PrestaShop\PrestaShop\Core\Security\OpenSsl\OpenSSL;
use PrestaShop\PrestaShop\Core\Security\PasswordGenerator;
use Psr\Log\LoggerInterface;
use Throwable;

final class CustomerB2bFormDataHandler implements FormDataHandlerInterface
{
    private const GENERATED_PASSWORD_LENGTH = 16;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly ShopContext $shopContext,
        private readonly DefaultGroupsProviderInterface $defaultGroupsProvider,
        private readonly LoggerInterface $logger,
        private readonly PasswordGenerator $passwordGenerator = new PasswordGenerator(new OpenSSL()),
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): int
    {
        $isNewCustomer = !$data['use_existing_customer'];
        $customerId = $isNewCustomer
            ? $this->addCustomer($data['customer'])
            : $this->getExistingCustomerId($data['existing_customer']);

        try {
            $command = new AddCustomerB2bCommand(
                $customerId,
                (int) $data['business_entity_id'],
                (int) $data['role_id'],
                $data['status'],
                $data['external_ref'],
            );

            /** @var CustomerB2bId $id */
            $id = $this->commandBus->handle($command);
        } catch (Throwable $e) {
            if ($isNewCustomer) {
                $this->deleteCustomer($customerId);
            }

            throw $e;
        }

        if ($isNewCustomer && $data['send_password_setup_link']) {
            $this->sendPasswordSetupLink($customerId, $id);
        }

        return $id->getValue();
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        throw new LogicException('Not implemented.'); // TODO: US3.3
    }

    /**
     * @param array<string, mixed> $customerData
     *
     * @return int new customer ID
     */
    private function addCustomer(array $customerData): int
    {
        $defaultGroupId = $this->defaultGroupsProvider->getGroups()->getCustomersGroup()->getId();

        /** @var CustomerId $customerId */
        $customerId = $this->commandBus->handle(new AddCustomerCommand(
            $customerData['first_name'],
            $customerData['last_name'],
            $customerData['email'],
            $this->passwordGenerator->generatePassword(self::GENERATED_PASSWORD_LENGTH, PasswordGenerator::PASSWORDGEN_FLAG_RANDOM),
            $defaultGroupId,
            [$defaultGroupId],
            $this->shopContext->getId(),
            $customerData['gender_id'] ?? null,
        ));

        return $customerId->getValue();
    }

    /**
     * @param array<int|string, array<string, mixed>> $data
     */
    private function getExistingCustomerId(array $data): int
    {
        $customer = reset($data);

        return (int) ($customer['id_customer'] ?? 0);
    }

    private function deleteCustomer(int $customerId): void
    {
        try {
            $this->commandBus->handle(new DeleteCustomerCommand($customerId, CustomerDeleteMethod::ALLOW_CUSTOMER_REGISTRATION));
        } catch (Throwable $e) {
            $this->logger->error('Failed to delete the customer after a B2B profile creation failure', [
                'object_type' => 'Customer',
                'object_id' => $customerId,
                'exception' => $e,
            ]);
        }
    }

    /**
     * @throws PasswordSetupLinkNotSentException
     */
    private function sendPasswordSetupLink(int $customerId, CustomerB2bId $id): void
    {
        try {
            $this->commandBus->handle(new SendCustomerPasswordSetupLinkCommand($customerId));
        } catch (CannotSendPasswordSetupLinkException $e) {
            $this->logger->error('Failed to send the password setup link to the new B2B customer', [
                'object_type' => 'CustomerB2b',
                'object_id' => $id->getValue(),
                'exception' => $e,
            ]);

            throw new PasswordSetupLinkNotSentException($id, $e);
        }
    }
}
