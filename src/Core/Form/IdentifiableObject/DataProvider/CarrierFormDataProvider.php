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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\EditableCarrier;

class CarrierFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus
    ) {
    }

    public function getData($id)
    {
        /** @var EditableCarrier $carrier */
        $carrier = $this->queryBus->handle(new GetCarrierForEditing((int) $id));

        return [
            'general_settings' => [
                'name' => $carrier->getName(),
                'localized_delay' => $carrier->getLocalizedDelay(),
                'active' => $carrier->isActive(),
                'grade' => $carrier->getGrade(),
                'group_access' => $carrier->getAssociatedGroupIds(),
                'logo_preview' => $carrier->getLogoPath(),
                'tracking_url' => $carrier->getTrackingUrl(),
            ],
            'shipping_settings' => [
                'has_additional_handling_fee' => $carrier->hasAdditionalHandlingFee(),
                'is_free' => $carrier->isFree(),
                'shipping_method' => $carrier->getShippingMethod(),
                'id_tax_rule_group' => $carrier->getIdTaxRuleGroup(),
                'range_behavior' => $carrier->getRangeBehavior(),
            ],
            'size_weight_settings' => [
                'max_width' => $carrier->getMaxWidth(),
                'max_height' => $carrier->getMaxHeight(),
                'max_depth' => $carrier->getMaxDepth(),
                'max_weight' => $carrier->getMaxWeight(),
            ],
        ];
    }

    public function getDefaultData()
    {
        return [
            'general_settings' => [
                'grade' => 0,
            ],
        ];
    }
}
