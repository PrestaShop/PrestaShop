<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\ToggleStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler\ToggleStoreStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotToggleStoreStatusException;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;

/**
 * Handles command that toggle store status
 */
#[AsCommandHandler]
class ToggleStoreStatusHandler implements ToggleStoreStatusHandlerInterface
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
    public function handle(ToggleStoreStatusCommand $command): void
    {
        $store = $this->storeRepository->get($command->getStoreId());
        $store->active = !$store->active;
        $this->storeRepository->partialUpdate(
            $store,
            ['active'],
            CannotToggleStoreStatusException::SINGLE_TOGGLE
        );
    }
}
