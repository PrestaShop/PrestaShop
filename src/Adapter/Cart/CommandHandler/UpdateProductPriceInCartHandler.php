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
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductPriceInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateProductPriceInCartHandlerInterface;
use SpecificPrice;

/**
 * Updates product price in cart using SpecificPrice.
 *
 * @internal
 */
#[AsCommandHandler]
final class UpdateProductPriceInCartHandler extends AbstractCartHandler implements UpdateProductPriceInCartHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductPriceInCartCommand $command)
    {
        $cart = $this->getCart($command->getCartId());

        $this->deleteSpecificPriceIfExists($command);

        $specificPrice = new SpecificPrice();
        $specificPrice->id_cart = (int) $cart->id;
        $specificPrice->id_shop = 0;
        $specificPrice->id_shop_group = 0;
        $specificPrice->id_currency = 0;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->id_customer = (int) $cart->id_customer;
        $specificPrice->id_product = (int) $command->getProductId()->getValue();
        $specificPrice->id_product_attribute = (int) $command->getCombinationId();
        $specificPrice->price = $command->getPrice();
        $specificPrice->from_quantity = 1;
        $specificPrice->reduction = 0;
        $specificPrice->reduction_type = 'amount';
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';

        $specificPrice->add();
    }

    /**
     * Deletes specific price for cart & product if it already exists.
     *
     * @param UpdateProductPriceInCartCommand $command
     */
    private function deleteSpecificPriceIfExists(UpdateProductPriceInCartCommand $command)
    {
        SpecificPrice::deleteByIdCart(
            $command->getCartId()->getValue(),
            $command->getProductId()->getValue(),
            $command->getCombinationId()
        );
    }
}
