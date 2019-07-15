<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use Carrier;
use Configuration;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\AddCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShopException;
use RangePrice;

/**
 * Handles AddCarrierCommand using legacy object model
 */
final class AddCarrierHandler extends AbstractObjectModelHandler implements AddCarrierHandlerInterface
{
    /**
     * @param AddCarrierCommand $command
     *
     * @return CarrierId
     *
     * @throws CarrierException
     */
    public function handle(AddCarrierCommand $command)
    {
        $carrier = new Carrier();
        $this->fillLegacyCarrierWithData($carrier, $command);

        try {
            if (false === $carrier->validateFields(false) || false === $carrier->validateFieldsLang(false)) {
                throw new CarrierException('Carrier contains invalid field values');
            }

            if (!$carrier->add()) {
                throw new CarrierException(
                    sprintf('Failed to add new carrier "%s"', $command->getName())
                );
            }
            $this->addShopAssociation($carrier, $command);
        } catch (PrestaShopException $e) {
            throw new CarrierException(
                sprintf('Failed to add new carrier "%s"', $command->getName())
            );
        }

        return new CarrierId((int) $carrier->id);
    }

    private function fillLegacyCarrierWithData(Carrier $carrier, AddCarrierCommand $command)
    {
        $shippingMethod = $command->getShippingMethod()->getValue();

        /*
         * Backwards compatibility.
         * SHIPPING_METHOD_DEFAULT @deprecated 1.5.5
         */
        if (Carrier::SHIPPING_METHOD_DEFAULT === $shippingMethod) {
            $shippingMethod = ((int) Configuration::get('PS_SHIPPING_METHOD') ?
                ShippingMethod::SHIPPING_METHOD_WEIGHT : ShippingMethod::SHIPPING_METHOD_PRICE);
        }
        $carrier->shipping_method = $shippingMethod;

        foreach ($command->getLocalizedCarrierNames() as $langId => $carrierName) {
            $carrier->localized_name[$langId] = $carrierName;
        }
        foreach ($command->getLocalizedShippingDelays() as $langId => $shippingDelay) {
            $carrier->delay[$langId] = $shippingDelay;
        }
        $carrier->grade = $command->getSpeedGrade()->getValue();
        $carrier->url = $command->getTrackingUrl()->getValue();
        $carrier->shipping_handling = $command->isShippingCostIncluded();
        //@Todo: WIP. name validation? legacy RangePrice object creation etc.
    }
}
