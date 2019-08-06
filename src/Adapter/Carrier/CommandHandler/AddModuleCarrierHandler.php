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
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddModuleCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\AddModuleCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShopException;

/**
 * Handles AddModuleCarrierCommand using legacy object model
 */
final class AddModuleCarrierHandler extends AbstractAddCarrierHandler implements AddModuleCarrierHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddModuleCarrierCommand $command): CarrierId
    {
        $carrier = new Carrier();
        $this->fillCarrierWithData($carrier, $command);

        try {
            $this->validateFields($carrier);

            if (false === $carrier->add()) {
                throw new CarrierException('Failed to add new carrier');
            }

            $this->associateWithShops($carrier, $command->getAssociatedShopIds());
            $carrier->setTaxRulesGroup($command->getTaxRulesGroupId());
            $carrier->setGroups($command->getAssociatedGroupIds());
            $this->addShippingRanges($carrier, $command->getBilling(), $command->getShippingRanges());
        } catch (PrestaShopException $e) {
            throw new CarrierException('An error occurred when trying to add new carrier with module');
        }

        return new CarrierId((int) $carrier->id);
    }

    /**
     * @param Carrier $carrier
     * @param AddModuleCarrierCommand $command
     */
    private function fillCarrierWithData(Carrier $carrier, AddModuleCarrierCommand $command)
    {
        $this->fillCarrierCommonFieldsWithData($carrier, $command);
        $carrier->is_module = true;
        $carrier->external_module_name = $command->getModuleName();
        $carrier->shipping_external = $command->doModuleCalculateShippingPrice();
        $carrier->need_range = $command->doModuleNeedCoreShippingPrice();
    }
}
