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

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartCarrierHandlerInterface;

/**
 * @internal
 */
final class UpdateCartCarrierHandler extends AbstractCartHandler implements UpdateCartCarrierHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCartCarrierCommand $command)
    {
        $cart = $this->getCart($command->getCartId());

        $cart->setDeliveryOption([
            $cart->id_address_delivery => $this->formatLegacyDeliveryOptionFromCarrierId($command->getNewCarrierId()),
        ]);

        $cart->update();
    }

    /**
     * Delivery option consists of deliveryAddress and carrierId.
     *
     * Legacy multishipping feature used comma separated carriers in delivery option (e.g. {'1':'6,7'}
     * Now that multishipping is gone - delivery option should consist of one carrier and one address.
     *
     * However the structure of deliveryOptions is still used with comma in legacy, so
     * this method provides assurance for deliveryOption structure until major refactoring
     *
     * @param int $carrierId
     *
     * @return string
     */
    private function formatLegacyDeliveryOptionFromCarrierId(int $carrierId): string
    {
        return sprintf('%s,', $carrierId);
    }
}
