<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkDeleteStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler\BulkDeleteStoreHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotDeleteStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;

/**
 * Handles command that deletes stores
 */
#[AsCommandHandler]
class BulkDeleteStoreHandler implements BulkDeleteStoreHandlerInterface
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
    public function handle(BulkDeleteStoreCommand $command): void
    {
        foreach ($command->getStoreIds() as $storeId) {
            try {
                $this->storeRepository->delete($storeId);
            } catch (CannotDeleteStoreException $e) {
                throw new CannotDeleteStoreException(
                    sprintf(
                        'Error occurred when trying to bulk delete stores. [%s]',
                        $e->getMessage()
                    ),
                    CannotDeleteStoreException::FAILED_BULK_DELETE
                );
            }
        }
    }
}
