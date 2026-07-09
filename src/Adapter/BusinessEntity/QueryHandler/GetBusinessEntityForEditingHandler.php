<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForEditing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryHandler\GetBusinessEntityForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\EditableBusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;

#[AsQueryHandler]
final class GetBusinessEntityForEditingHandler implements GetBusinessEntityForEditingHandlerInterface
{
    public function __construct(
        private readonly BusinessEntityRepository $businessEntityRepository,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function handle(GetBusinessEntityForEditing $query): EditableBusinessEntity
    {
        $businessEntityId = $query->getBusinessEntityId()->getValue();

        $shopIds = $this->shopContext->isAllShopContext() ? null : $this->shopContext->getAssociatedShopIds();
        $businessEntity = $this->businessEntityRepository->getBusinessEntityById($businessEntityId, $shopIds);

        if (null === $businessEntity) {
            throw new BusinessEntityNotFoundException(sprintf('Business entity with id %d was not found.', $businessEntityId));
        }

        return new EditableBusinessEntity(
            $businessEntity->getId(),
            $businessEntity->getName(),
            $businessEntity->getLegalName(),
            $businessEntity->getExternalRef(),
            $businessEntity->isDeliveryAuthorized(),
            $businessEntity->getStatus(),
            $businessEntity->getIdCustomerGroup(),
            $businessEntity->getIdShop(),
        );
    }
}
