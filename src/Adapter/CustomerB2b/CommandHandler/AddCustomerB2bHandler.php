<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerB2b\CommandHandler;

use Exception;
use PrestaShop\PrestaShop\Adapter\CustomerB2b\Validate\CustomerB2bValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\Exception\B2bRoleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command\AddCustomerB2bCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\CommandHandler\AddCustomerB2bHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CannotAddCustomerB2bException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bException;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\ValueObject\CustomerB2bId;
use PrestaShopBundle\Entity\B2B\BusinessEntityCustomerB2b;
use PrestaShopBundle\Entity\B2B\CustomerB2b;
use PrestaShopBundle\Entity\Repository\CustomerB2bRepository;
use Psr\Log\LoggerInterface;

#[AsCommandHandler]
final class AddCustomerB2bHandler implements AddCustomerB2bHandlerInterface
{
    public function __construct(
        private readonly CustomerB2bValidator $validator,
        private readonly CustomerB2bRepository $customerB2bRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws CustomerB2bException
     * @throws CustomerNotFoundException
     * @throws BusinessEntityNotFoundException
     * @throws B2bRoleNotFoundException
     */
    public function handle(AddCustomerB2bCommand $command): CustomerB2bId
    {
        $validated = $this->validator->validate($command);
        $customerId = $command->customerId->getValue();

        $association = (new BusinessEntityCustomerB2b())
            ->setBusinessEntity($validated->businessEntity)
            ->setB2bRole($validated->role)
            ->setIsDefault(true);

        $entity = (new CustomerB2b())
            ->setIdCustomer($customerId)
            ->setExternalRef($command->externalRef?->getValue())
            ->addBusinessEntityCustomerB2b($association);

        if (null !== $command->status) {
            $entity->setStatus($command->status);
        }

        try {
            $this->customerB2bRepository->save($entity);
        } catch (Exception $e) {
            $this->logger->error(\sprintf('Failed to save the B2B customer profile: %s', $e->getMessage()), [
                'object_type' => 'Customer',
                'object_id' => $customerId,
                'exception' => $e,
            ]);

            throw new CannotAddCustomerB2bException(\sprintf('Failed to save the B2B profile of customer #%d.', $customerId), CannotAddCustomerB2bException::FAILED_SAVE, $e);
        }

        $id = (int) $entity->getId();
        $this->logger->info('B2B customer created', [
            'object_type' => 'CustomerB2b',
            'object_id' => $id,
        ]);

        return new CustomerB2bId($id);
    }
}
