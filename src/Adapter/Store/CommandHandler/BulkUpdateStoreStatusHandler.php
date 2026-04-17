<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkUpdateStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler\BulkUpdateStoreStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotToggleStoreStatusException;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;

/**
 * Handles command that toggle store status
 */
#[AsCommandHandler]
class BulkUpdateStoreStatusHandler implements BulkUpdateStoreStatusHandlerInterface
{
    /**
     * @var StoreRepository
     */
    private $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkUpdateStoreStatusCommand $command): void
    {
        foreach ($command->getStoreIds() as $storeId) {
            $store = $this->storeRepository->get($storeId);
            $store->active = $command->getExpectedStatus();
            $this->storeRepository->partialUpdate(
                $store,
                ['active'],
                CannotToggleStoreStatusException::BULK_TOGGLE
            );
        }
    }
}
