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

use Configuration;
use Customer;
use CustomerMessage;
use CustomerThread;
use Language;
use Mail;
use Order;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\CommandHandler\AddOrderCustomerMessageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception\CannotSendEmailException;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception\CustomerMessageException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use Symfony\Component\Translation\TranslatorInterface;
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

    /**
     * @var int
     */
    private $contextEmployeeId;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     * @param int $contextShopId
     * @param int $contextLanguageId
     * @param int $contextEmployeeId
     */
    public function __construct(
        TranslatorInterface $translator,
        int $contextShopId,
        int $contextLanguageId,
        int $contextEmployeeId
    ) {
        $this->contextShopId = $contextShopId;
        $this->contextLanguageId = $contextLanguageId;
        $this->contextEmployeeId = $contextEmployeeId;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CustomerMessageException
     * @throws OrderNotFoundException
     */
    public function handle(AddOrderCustomerMessageCommand $command): void
    {
        $order = new Order($command->getOrderId()->getValue());

        if (0 >= $order->id) {
            throw new OrderNotFoundException(
                $command->getOrderId(),
                "Order with id {$command->getOrderId()->getValue()} was not found"
            );
        }

        $customer = new Customer($order->id_customer);

        if (0 >= $customer->id) {
            throw new CustomerMessageException(
                "Associated order customer with id {$command->getOrderId()->getValue()} was not found",
                CustomerMessageException::ORDER_CUSTOMER_NOT_FOUND
            );
        }

        $customerServiceThreadId = CustomerThread::getIdCustomerThreadByEmailAndIdOrder(
            $customer->email,
            $order->id
        );

        if (!$customerServiceThreadId) {
            try {
                $customerServiceThreadId = $this->createCustomerMessageThread($order);
            } catch (\PrestaShopException $e) {
                throw new CustomerMessageException(
                    'An unexpected error occurred when creating customer message thread',
                    0,
                    $e
                );
            }
        }

        try {
            $this->createMessage($customerServiceThreadId, $command);
        } catch (\PrestaShopException $e) {
            throw new CustomerMessageException(
                'An unexpected error occurred when creating customer message',
                0,
                $e
            );
        }

        $failedMailSentMessage = 'An unexpected error occurred when sending the email';

        try {
            $isSent = $this->sendMail($customer, $order, $command);

            if (!$isSent) {
                throw new CannotSendEmailException($failedMailSentMessage);
            }
        } catch (\PrestaShopException $e) {
            throw new CannotSendEmailException(
                $failedMailSentMessage,
                0,
                $e
            );
        }
    }

    /**
     * Creates customer message thread which groups customer message in an order group.
     *
     * @param Order $order
     *
     * @return int
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createCustomerMessageThread(Order $order): int
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

    /**
     * Creates actual message.
     *
     * @param int $customerServiceThreadId
     * @param AddOrderCustomerMessageCommand $command
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createMessage(int $customerServiceThreadId, AddOrderCustomerMessageCommand $command): void
    {
        $customerMessage = new CustomerMessage();
        $customerMessage->id_customer_thread = $customerServiceThreadId;
        $customerMessage->id_employee = $this->contextEmployeeId;
        $customerMessage->message = $command->getMessage();
        $customerMessage->private = $command->isPrivate();
        $customerMessage->add();
    }

    /**
     * Sends email to customer
     *
     * @param Customer $customer
     * @param Order $order
     * @param AddOrderCustomerMessageCommand $command
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function sendMail(Customer $customer, Order $order, AddOrderCustomerMessageCommand $command): bool
    {
        if ($command->isPrivate()) {
            return true;
        }

        $message = $command->getMessage();

        if (Configuration::get('PS_MAIL_TYPE', null, null, $order->id_shop) != Mail::TYPE_TEXT) {
            $message = Tools::nl2br($command->getMessage());
        }

        $orderLanguage = new Language((int) $order->id_lang);
        $varsTpl = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{id_order}' => $order->id,
            '{order_name}' => $order->getUniqReference(),
            '{message}' => $message,
        );

        return @Mail::Send(
            (int) $order->id_lang,
            'order_merchant_comment',
            $this->translator->trans(
                'New message regarding your order',
                [],
                'Emails.Subject',
                $orderLanguage->locale
            ),
            $varsTpl,
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            true,
            (int) $order->id_shop
        );
    }
}
