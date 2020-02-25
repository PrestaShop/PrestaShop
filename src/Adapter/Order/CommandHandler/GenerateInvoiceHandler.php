<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Configuration;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\CommandHandler\GenerateOrderInvoiceHandlerInterface;

/**
 * @internal
 */
final class GenerateInvoiceHandler extends AbstractOrderHandler implements GenerateOrderInvoiceHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GenerateInvoiceCommand $command)
    {
        $order = $this->getOrderObject($command->getOrderId());

        if (!Configuration::get('PS_INVOICE', null, null, $order->id_shop)) {
            throw new OrderException('Invoice management has been disabled.');
        }

        if ($order->hasInvoice()) {
            throw new OrderException('This order already has an invoice.');
        }

        $order->setInvoice(true);
    }
}
