<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryHandler\GetCarrierForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\EditableCarrier;

/**
 * Handles query which gets carrier
 */
#[AsQueryHandler]
final class GetCarrierForEditingHandler implements GetCarrierForEditingHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCarrierForEditing $query): EditableCarrier
    {
        $carrier = $this->carrierRepository->get($query->getCarrierId());
        $zones = $this->carrierRepository->getAssociatedZones($query->getCarrierId());

        $logoPath = null;
        if (file_exists(_PS_SHIP_IMG_DIR_ . $query->getCarrierId()->getValue() . '.jpg')) {
            $logoPath = _THEME_SHIP_DIR_ . $query->getCarrierId()->getValue() . '.jpg';
        }

        return new EditableCarrier(
            $query->getCarrierId()->getValue(),
            $carrier->name,
            $carrier->grade,
            $carrier->url,
            $carrier->position,
            $carrier->active,
            $carrier->delay,
            $carrier->max_width,
            $carrier->max_height,
            $carrier->max_depth,
            $carrier->max_weight,
            $carrier->getAssociatedGroupIds(),
            $carrier->shipping_handling,
            $carrier->is_free,
            $carrier->shipping_method,
            $this->carrierRepository->getTaxRulesGroup($query->getCarrierId(), $query->getShopConstraint()),
            (int) $carrier->range_behavior,
            $carrier->getAssociatedShops(),
            $zones,
            $logoPath,
            $this->carrierRepository->getOrdersCount($query->getCarrierId()),
        );
    }
}
