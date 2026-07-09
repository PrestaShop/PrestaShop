<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\EditBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\EditBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommandHandler]
final class EditBusinessEntityHandler implements EditBusinessEntityHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BusinessEntityRepository $businessEntityRepository,
        #[Autowire(service: 'prestashop.adapter.legacy.logger')]
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws BusinessEntityNotFoundException
     */
    public function handle(EditBusinessEntityCommand $command): void
    {
        $businessEntityId = $command->getBusinessEntityId()->getValue();

        $businessEntity = $this->businessEntityRepository->getBusinessEntityById($businessEntityId);

        if (null === $businessEntity) {
            throw new BusinessEntityNotFoundException(sprintf('Business entity with id %d was not found.', $businessEntityId));
        }

        $modifiedFields = $this->getModifiedFields($businessEntity, $command);

        $businessEntity->setName($command->getName());
        $businessEntity->setLegalName($command->getLegalName());
        $businessEntity->setExternalRef($command->getExternalRef());
        $businessEntity->setDeliveryAuthorized($command->isDeliveryAuthorized());
        $businessEntity->setStatus($command->getStatus());
        $businessEntity->setIdCustomerGroup($command->getCustomerGroupId());

        $this->em->flush();

        $message = 'Business entity updated successfully';
        if ([] !== $modifiedFields) {
            $message .= ' ' . json_encode($modifiedFields, JSON_THROW_ON_ERROR);
        }

        $this->logger->info(
            $message,
            [
                'object_type' => 'BusinessEntity',
                'object_id' => $businessEntityId,
            ]
        );
    }

    /**
     * @return array<string, array{old: mixed, new: mixed}>
     */
    private function getModifiedFields(BusinessEntity $businessEntity, EditBusinessEntityCommand $command): array
    {
        $modifiedFields = [];

        if ($businessEntity->getName() !== $command->getName()) {
            $modifiedFields['name'] = ['old' => $businessEntity->getName(), 'new' => $command->getName()];
        }

        if ($businessEntity->getLegalName() !== $command->getLegalName()) {
            $modifiedFields['legal_name'] = ['old' => $businessEntity->getLegalName(), 'new' => $command->getLegalName()];
        }

        if ($businessEntity->getExternalRef() !== $command->getExternalRef()) {
            $modifiedFields['external_ref'] = ['old' => $businessEntity->getExternalRef(), 'new' => $command->getExternalRef()];
        }

        if ($businessEntity->isDeliveryAuthorized() !== $command->isDeliveryAuthorized()) {
            $modifiedFields['delivery_authorized'] = ['old' => $businessEntity->isDeliveryAuthorized(), 'new' => $command->isDeliveryAuthorized()];
        }

        if ($businessEntity->getStatus() !== $command->getStatus()) {
            $modifiedFields['status'] = ['old' => $businessEntity->getStatus()->value, 'new' => $command->getStatus()->value];
        }

        if ($businessEntity->getIdCustomerGroup() !== $command->getCustomerGroupId()) {
            $modifiedFields['customer_group_id'] = ['old' => $businessEntity->getIdCustomerGroup(), 'new' => $command->getCustomerGroupId()];
        }

        return $modifiedFields;
    }
}
