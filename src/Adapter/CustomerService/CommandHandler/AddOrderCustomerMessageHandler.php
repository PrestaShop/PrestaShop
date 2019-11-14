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

namespace PrestaShop\PrestaShop\Adapter\CustomerService\CommandHandler;

use Customer;
use CustomerThread;
use Order;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\CommandHandler\AddOrderCustomerMessageHandlerInterface;
use Tools;

final class AddOrderCustomerMessageHandler implements AddOrderCustomerMessageHandlerInterface
{
    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var int
     */
    private $contextLanguageId;

    public function __construct(int $contextShopId, int $contextLanguageId)
    {
        $this->contextShopId = $contextShopId;
        $this->contextLanguageId = $contextLanguageId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddOrderCustomerMessageCommand $command)
    {
        $order = new Order($command->getOrderId()->getValue());
    }

    private function createCustomerMessageThread(Order $order)
    {
        $orderCustomer = new Customer($order->id_customer);

        $customerThread = new CustomerThread();
        $customerThread->id_contact = 0;
        $customerThread->id_customer = (int) $order->id_customer;
        $customerThread->id_shop = $this->contextShopId;
        $customerThread->id_order = $order->id;
        $customerThread->id_lang = $this->contextLanguageId;
        $customerThread->email = $orderCustomer->email;
        $customerThread->status = 'open';
        $customerThread->token = Tools::passwdGen(12);
        $customerThread->add();

        return $customerThread->id;
    }
}
