<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarriersForProduct;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryHandler\GetCarriersForProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierSummary;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

#[AsQueryHandler]
final class GetCarriersForProductHandler implements GetCarriersForProductHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository,
        private readonly ShopContext $shopContext,
    ) {
    }

    /**
     * @return CarrierSummary[]
     */
    public function handle(GetCarriersForProduct $query)
    {
        $productId = $query->getProductId()->getValue();
        $productCarriers = $this->carrierRepository->findCarriersByProductIds([$productId], new ShopId($this->shopContext->getContextShopID()));
        $carriers = [];

        foreach ($productCarriers[$productId] as $productCarrier) {
            $carriers[] = new CarrierSummary($productCarrier['id_carrier'], $productCarrier['name']);
        }

        return $carriers;
    }
}
