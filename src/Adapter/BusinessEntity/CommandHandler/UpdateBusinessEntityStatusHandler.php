<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler;

use DateTime;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\UpdateBusinessEntityStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\UpdateBusinessEntityStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\CannotUpdateBusinessEntityException;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Throwable;

#[AsCommandHandler]
final class UpdateBusinessEntityStatusHandler implements UpdateBusinessEntityStatusHandlerInterface
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
    public function handle(UpdateBusinessEntityStatusCommand $command): void
    {
        $businessEntityId = $command->getBusinessEntityId()->getValue();

        $shopIds = $this->shopContext->isAllShopContext() ? null : $this->shopContext->getAssociatedShopIds();
        $businessEntity = $this->businessEntityRepository->findById($businessEntityId, $shopIds);

        if (null === $businessEntity) {
            throw new BusinessEntityNotFoundException(sprintf('Business entity with id %d was not found.', $businessEntityId));
        }

        $previousStatus = $businessEntity->getStatus();
        $businessEntity->setStatus($command->getStatus());
        // Resubmitting the same status leaves an empty changeset, so PreUpdate never fires. This
        // only touches the entity to make the changeset non-empty: the stored value still comes
        // from the PreUpdate callback, which overwrites it on flush.
        $businessEntity->setUpdatedAt(new DateTime());

        try {
            $this->businessEntityRepository->save($businessEntity);
        } catch (Throwable $e) {
            throw new CannotUpdateBusinessEntityException('Could not update business entity status', 0, $e);
        }

        $this->logger->info(
            $this->formatLogMessage($previousStatus, $command),
            [
                'object_type' => 'BusinessEntity',
                'object_id' => $businessEntityId,
                // Without this the legacy logger drops an entry identical to a previous one
                // (PrestaShopLogger::addLog() only inserts if $allowDuplicate || !isPresent()),
                // which would silently lose a status set back to a value it already held.
                'allow_duplicate' => true,
            ]
        );
    }

    private function formatLogMessage(BusinessEntityStatus $previousStatus, UpdateBusinessEntityStatusCommand $command): string
    {
        $baseMessage = 'Business entity status changed successfully';

        $details = [
            'status' => [
                'old' => $previousStatus->value,
                'new' => $command->getStatus()->value,
            ],
        ];

        $comment = trim((string) $command->getComment());

        if ('' !== $comment) {
            $details['comment'] = $comment;
        }

        $encodedDetails = json_encode($details, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);

        return false === $encodedDetails ? $baseMessage : $baseMessage . ' ' . $encodedDetails;
    }
}
