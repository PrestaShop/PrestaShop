<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\EditBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\EditBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\CannotUpdateBusinessEntityException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Throwable;

#[AsCommandHandler]
final class EditBusinessEntityHandler implements EditBusinessEntityHandlerInterface
{
    public function __construct(
        private readonly BusinessEntityRepository $businessEntityRepository,
        private readonly ShopContext $shopContext,
        #[Autowire(service: 'prestashop.adapter.legacy.logger')]
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws BusinessEntityNotFoundException
     * @throws CannotUpdateBusinessEntityException
     */
    public function handle(EditBusinessEntityCommand $command): void
    {
        $businessEntityId = $command->getBusinessEntityId()->getValue();

        $shopIds = $this->shopContext->isAllShopContext() ? null : $this->shopContext->getAssociatedShopIds();
        $businessEntity = $this->businessEntityRepository->findById($businessEntityId, $shopIds);

        if (null === $businessEntity) {
            throw new BusinessEntityNotFoundException(sprintf('Business entity with id %d was not found.', $businessEntityId));
        }

        $modifiedFields = $this->getModifiedFields($businessEntity, $command);

        if (null !== $command->getName()) {
            $businessEntity->setName($command->getName());
        }

        if (null !== $command->getLegalName()) {
            $businessEntity->setLegalName($command->getLegalName());
        }

        if ($command->hasExternalRef()) {
            $businessEntity->setExternalRef($command->getExternalRef());
        }

        if (null !== $command->getDeliveryAuthorized()) {
            $businessEntity->setDeliveryAuthorized($command->getDeliveryAuthorized());
        }

        if (null !== $command->getStatus()) {
            $businessEntity->setStatus($command->getStatus());
        }

        if (null !== $command->getCustomerGroupId()) {
            $businessEntity->setIdCustomerGroup($command->getCustomerGroupId());
        }

        try {
            $this->businessEntityRepository->save($businessEntity);
        } catch (Throwable $e) {
            throw new CannotUpdateBusinessEntityException('Could not update business entity', 0, $e);
        }

        $this->logger->info(
            $this->formatLogMessage($modifiedFields),
            [
                'object_type' => 'BusinessEntity',
                'object_id' => $businessEntityId,
                // Without this the legacy logger drops an entry identical to a previous one
                // (PrestaShopLogger::addLog() only inserts if $allowDuplicate || !isPresent()),
                // which would silently lose repeated edits from the audit trail.
                'allow_duplicate' => true,
            ]
        );
    }

    /**
     * @param array<string, array{old: mixed, new: mixed}> $modifiedFields
     */
    private function formatLogMessage(array $modifiedFields): string
    {
        $baseMessage = 'Business entity updated successfully';

        if ([] === $modifiedFields) {
            return $baseMessage;
        }

        $encodedFields = json_encode($modifiedFields, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);

        return false === $encodedFields ? $baseMessage : $baseMessage . ' ' . $encodedFields;
    }

    /**
     * @return array<string, array{old: mixed, new: mixed}>
     */
    private function getModifiedFields(BusinessEntity $businessEntity, EditBusinessEntityCommand $command): array
    {
        $modifiedFields = [];

        if (null !== $command->getName() && $businessEntity->getName() !== $command->getName()) {
            $modifiedFields['name'] = ['old' => $businessEntity->getName(), 'new' => $command->getName()];
        }

        if (null !== $command->getLegalName() && $businessEntity->getLegalName() !== $command->getLegalName()) {
            $modifiedFields['legal_name'] = ['old' => $businessEntity->getLegalName(), 'new' => $command->getLegalName()];
        }

        if ($command->hasExternalRef() && $businessEntity->getExternalRef() !== $command->getExternalRef()) {
            $modifiedFields['external_ref'] = ['old' => $businessEntity->getExternalRef(), 'new' => $command->getExternalRef()];
        }

        if (null !== $command->getDeliveryAuthorized() && $businessEntity->isDeliveryAuthorized() !== $command->getDeliveryAuthorized()) {
            $modifiedFields['delivery_authorized'] = ['old' => $businessEntity->isDeliveryAuthorized(), 'new' => $command->getDeliveryAuthorized()];
        }

        if (null !== $command->getStatus() && $businessEntity->getStatus() !== $command->getStatus()) {
            $modifiedFields['status'] = ['old' => $businessEntity->getStatus()->value, 'new' => $command->getStatus()->value];
        }

        if (null !== $command->getCustomerGroupId() && $businessEntity->getIdCustomerGroup() !== $command->getCustomerGroupId()) {
            $modifiedFields['customer_group_id'] = ['old' => $businessEntity->getIdCustomerGroup(), 'new' => $command->getCustomerGroupId()];
        }

        return $modifiedFields;
    }
}
