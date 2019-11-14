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

namespace PrestaShop\PrestaShop\Adapter\CustomerThread\CommandHandler;

use Customer;
use CustomerThread;
use Order;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\AddOrderCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\AddOrderCustomerThreadHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use Tools;

/**
 * Handles adding customer thread which is related with order.
 */
final class AddOrderCustomerThreadHandler implements AddOrderCustomerThreadHandlerInterface
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
     * @throws CustomerServiceException
     */
    public function handle(AddOrderCustomerThreadCommand $command): CustomerThreadId
    {
        $order = new Order($command->getOrderId()->getValue());

        $orderCustomer = new Customer($order->id_customer);

        $customer_thread = new CustomerThread();
        $customer_thread->id_contact = 0;
        $customer_thread->id_customer = (int) $order->id_customer;
        $customer_thread->id_shop = $this->contextShopId;
        $customer_thread->id_order = $command->getOrderId()->getValue();
        $customer_thread->id_lang = $this->contextLanguageId;
        $customer_thread->email = $orderCustomer->email;
        $customer_thread->status = 'open';
        $customer_thread->token = Tools::passwdGen(12);
        $customer_thread->add();

        return new CustomerThreadId($customer_thread->id);
    }
}
