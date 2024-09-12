<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
