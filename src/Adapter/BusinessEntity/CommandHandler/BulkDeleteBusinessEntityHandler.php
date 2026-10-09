<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\BulkDeleteBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\BulkDeleteBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BulkDeleteBusinessEntityException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\CannotDeleteBusinessEntityException;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Throwable;

#[AsCommandHandler]
final class BulkDeleteBusinessEntityHandler implements BulkDeleteBusinessEntityHandlerInterface
{
    public function __construct(
        private readonly BusinessEntityRepository $businessEntityRepository,
        private readonly ShopContext $shopContext,
        #[Autowire(service: 'prestashop.adapter.legacy.logger')]
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws BulkDeleteBusinessEntityException
     * @throws CannotDeleteBusinessEntityException
     */
    public function handle(BulkDeleteBusinessEntityCommand $command): void
    {
        $shopIds = $this->shopContext->isAllShopContext() ? null : $this->shopContext->getAssociatedShopIds();

        $ids = [];
        foreach ($command->getBusinessEntityIds() as $businessEntityId) {
            $ids[] = $businessEntityId->getValue();
        }

        $found = $this->businessEntityRepository->findByIds($ids, $shopIds);

        /** @var array<int, BusinessEntity> $businessEntities */
        $businessEntities = [];
        $notFound = [];

        foreach ($ids as $id) {
            if (!isset($found[$id])) {
                $notFound[] = new BusinessEntityNotFoundException(
                    sprintf('Business entity with id %d was not found.', $id)
                );

                continue;
            }

            $businessEntities[$id] = $found[$id];
        }

        if ([] !== $businessEntities) {
            // A soft delete is a field change on managed entities, so the whole selection is written
            // in one flush: a per-entity flush would close the EntityManager on the first failure and
            // make every remaining entity fail too.
            try {
                $this->businessEntityRepository->bulkDelete($businessEntities);
            } catch (Throwable $e) {
                throw new CannotDeleteBusinessEntityException('Could not delete business entity', 0, $e);
            }

            foreach (array_keys($businessEntities) as $id) {
                $this->logger->info(
                    'Business entity deleted successfully',
                    [
                        'object_type' => 'BusinessEntity',
                        'object_id' => $id,
                    ]
                );
            }
        }

        if ([] !== $notFound) {
            throw new BulkDeleteBusinessEntityException($notFound);
        }
    }
}
