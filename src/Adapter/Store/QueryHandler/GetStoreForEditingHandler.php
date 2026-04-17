<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Store\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Store\Query\GetStoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryHandler\GetStoreForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryResult\StoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\Repository\StoreRepository;
use PrestaShopException;

#[AsQueryHandler]
class GetStoreForEditingHandler implements GetStoreForEditingHandlerInterface
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
     *
     * @throws StoreException
     */
    public function handle(GetStoreForEditing $query): StoreForEditing
    {
        try {
            $store = $this->storeRepository->get($query->getStoreId());

            if (0 >= $store->id) {
                throw new StoreNotFoundException(sprintf('Store object with id %d was not found', $query->getStoreId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new StoreException(sprintf('An unexpected error occurred when retrieving store with id %d', $query->getStoreId()->getValue()), 0, $e);
        }

        return new StoreForEditing($store->id, $store->active);
    }
}
