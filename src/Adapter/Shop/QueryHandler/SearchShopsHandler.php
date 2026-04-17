<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\SearchShops;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler\SearchShopsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\FoundShop;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\FoundShopGroup;
use PrestaShopBundle\Entity\Repository\ShopGroupRepository;
use PrestaShopBundle\Entity\Repository\ShopRepository;

/**
 * Responsible for getting shops for a given search term.
 */
#[AsQueryHandler]
final class SearchShopsHandler implements SearchShopsHandlerInterface
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var ShopGroupRepository
     */
    private $shopGroupRepository;

    /**
     * SearchShopsHandler constructor.
     *
     * @param ShopRepository $shopRepository
     * @param ShopGroupRepository $shopGroupRepository
     */
    public function __construct(ShopRepository $shopRepository, ShopGroupRepository $shopGroupRepository)
    {
        $this->shopRepository = $shopRepository;
        $this->shopGroupRepository = $shopGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SearchShops $query): array
    {
        $searchTerm = $query->getSearchTerm();
        $shopGroupList = $this->shopGroupRepository->findBySearchTerm($searchTerm);
        $shopList = $this->shopRepository->findBySearchTerm($searchTerm);
        $result = [];

        foreach ($shopGroupList as $shopGroup) {
            if (!$shopGroup->getShops()->isEmpty()) {
                $result[] = new FoundShopGroup(
                    $shopGroup->getId(),
                    $shopGroup->getColor(),
                    $shopGroup->getName()
                );
            }
        }

        foreach ($shopList as $shop) {
            if (!$shop->hasMainUrl()) {
                continue;
            }

            $result[] = new FoundShop(
                $shop->getId(),
                $shop->getColor() ?? '',
                $shop->getName(),
                $shop->getShopGroup()->getId(),
                $shop->getShopGroup()->getName(),
                $shop->getShopGroup()->getColor()
            );
        }

        return $result;
    }
}
