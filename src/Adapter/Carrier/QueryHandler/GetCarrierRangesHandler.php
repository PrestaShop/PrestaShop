<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRangeRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierRanges;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryHandler\GetCarrierRangesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierRangesCollection;

/**
 * Handles query which gets carrier range
 */
#[AsQueryHandler]
final class GetCarrierRangesHandler implements GetCarrierRangesHandlerInterface
{
    public function __construct(
        private readonly CarrierRangeRepository $carrierRangeRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCarrierRanges $query): CarrierRangesCollection
    {
        // Get carriers ranges
        $results = $this->carrierRangeRepository->get(
            $query->getCarrierId(),
            $query->getShopConstraint()
        );

        // Return formatted ranges
        return new CarrierRangesCollection($results);
    }
}
