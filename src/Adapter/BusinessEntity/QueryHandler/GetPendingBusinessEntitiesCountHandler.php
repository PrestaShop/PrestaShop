<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetPendingBusinessEntitiesCount;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryHandler\GetPendingBusinessEntitiesCountHandlerInterface;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;

#[AsQueryHandler]
final class GetPendingBusinessEntitiesCountHandler implements GetPendingBusinessEntitiesCountHandlerInterface
{
    public function __construct(
        private readonly BusinessEntityRepository $businessEntityRepository,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function handle(GetPendingBusinessEntitiesCount $query): int
    {
        $shopIds = $this->shopContext->isAllShopContext()
            ? null
            : $this->shopContext->getAssociatedShopIds();

        return $this->businessEntityRepository->getPendingCount($shopIds);
    }
}
