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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Address;
use Cart;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderInvoiceAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ChangeOrderInvoiceAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Validate;

/**
 * @internal
 */
final class ChangeOrderInvoiceAddressHandler extends AbstractOrderHandler implements ChangeOrderInvoiceAddressHandlerInterface
{
    /**
     * @var OrderAmountUpdater
     */
    private $orderAmountUpdater;

    /**
     * @var ShopConfigurationInterface
     */
    private $shopConfiguration;

    /**
     * @param OrderAmountUpdater $orderAmountUpdater
     * @param ShopConfigurationInterface $shopConfiguration
     */
    public function __construct(
        OrderAmountUpdater $orderAmountUpdater,
        ShopConfigurationInterface $shopConfiguration
    ) {
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->shopConfiguration = $shopConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ChangeOrderInvoiceAddressCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());
        $address = new Address($command->getNewInvoiceAddressId()->getValue());

        $cart = Cart::getCartByOrderId($order->id);

        if (!Validate::isLoadedObject($address)) {
            throw new OrderException('New invoice address is not valid');
        }

        $cart->id_address_invoice = $address->id;
        $cart->update();

        $order->id_address_invoice = $address->id;
        $this->orderAmountUpdater->update($order, $cart);

        // Update OrderDetails tax if the address is the delivery address
        if ($this->shopConfiguration->get('PS_TAX_ADDRESS_TYPE', null, $this->getOrderShopConstraint($order)) === 'id_address_invoice') {
            $this->updateOrderDetailsTax($order, $cart, new Address($order->id_address_invoice));
        }
    }
}
