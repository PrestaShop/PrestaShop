<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
