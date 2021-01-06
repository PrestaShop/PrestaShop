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

namespace PrestaShop\PrestaShop\Adapter\CustomerService\QueryHandler;

use Contact;
use Context;
use Currency;
use Customer;
use CustomerThread;
use DateTime;
use Employee;
use Order;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler\GetCustomerThreadForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerInformation;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadMessage;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadTimeline;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadTimelineItem;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadMessageType;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use Product;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * @internal
 */
final class GetCustomerThreadForViewingHandler implements GetCustomerThreadForViewingHandlerInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->translator = $context->getTranslator();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCustomerThreadForViewing $query)
    {
        $customerThread = $this->getCustomerThread($query->getCustomerThreadId());

        $messages = CustomerThread::getMessageCustomerThreads($query->getCustomerThreadId()->getValue());

        return new CustomerThreadView(
            $query->getCustomerThreadId(),
            new LanguageId((int) $customerThread->id_lang),
            $this->getAvailableActions($customerThread),
            $this->getCustomerInformation($customerThread),
            $this->getContactName($customerThread),
            $this->getCustomerThreadMessages($messages),
            $this->getTimeline($messages, $customerThread)
        );
    }

    /**
     * @param array $messages
     *
     * @return CustomerThreadMessage[]
     */
    private function getCustomerThreadMessages(array $messages)
    {
        $threadMessages = [];

        foreach ($messages as $key => $message) {
            $employeeImage = null;

            if ($message['id_employee']) {
                $employee = new Employee($message['id_employee']);
                $employeeImage = $employee->getImage();
            }

            $attachmentFile = null;

            if (!empty($message['file_name'])
                && file_exists(_PS_UPLOAD_DIR_ . $message['file_name'])
            ) {
                $attachmentFile = _THEME_PROD_PIC_DIR_ . $message['file_name'];
            }

            $productId = null;
            $productName = null;

            if ($message['id_product']) {
                $product = new Product((int) $message['id_product'], false, $this->context->language->id);

                if (Validate::isLoadedObject($product)) {
                    $productId = (int) $product->id;
                    $productName = $product->name;
                }
            }

            $type = $message['id_employee'] ?
                CustomerThreadMessageType::EMPLOYEE :
                CustomerThreadMessageType::CUSTOMER;

            $threadMessages[] = new CustomerThreadMessage(
                $type,
                $message['message'],
                $message['date_add'],
                $employeeImage,
                $message['employee_name'],
                $message['customer_name'],
                $attachmentFile,
                $productId,
                $productName
            );
        }

        return $threadMessages;
    }

    /**
     * @param CustomerThreadId $customerThreadId
     *
     * @return CustomerThread
     */
    private function getCustomerThread(CustomerThreadId $customerThreadId)
    {
        $customerThread = new CustomerThread($customerThreadId->getValue());

        if ($customerThread->id !== $customerThreadId->getValue()) {
            throw new CustomerThreadNotFoundException(sprintf('Customer thread with id "%s" was not found', $customerThreadId->getValue()));
        }

        return $customerThread;
    }

    /**
     * Get customer thread messages in timeline
     *
     * @param array $messages
     * @param CustomerThread $customerThread
     *
     * @return CustomerThreadTimeline
     */
    private function getTimeline(array $messages, CustomerThread $customerThread)
    {
        $timeline = [];

        foreach ($messages as $message) {
            $product = new Product((int) $message['id_product'], false, $this->context->language->id);

            $content = '';

            if (!$message['private']) {
                $content .= sprintf(
                    '%s <span class="badge badge-primary rounded">%s</span><br/>',
                    $this->translator->trans('Message to:', [], 'Admin.Catalog.Feature'),
                    !$message['id_employee'] ? $message['subject'] : $message['customer_name']
                );
            }

            if (Validate::isLoadedObject($product)) {
                $content .= sprintf(
                    '<br/>%s<span class="badge badge-primary-hover rounded">%s</span><br/><br/>',
                    $this->translator->trans('Product:', [], 'Admin.Catalog.Feature'),
                    $product->name
                );
            }

            $content .= Tools::safeOutput($message['message']);

            $timeline[$message['date_add']][] = [
                'arrow' => 'left',
                'background_color' => '',
                'icon' => 'email',
                'content' => $content,
                'date' => $message['date_add'],
                'related_order_id' => null,
            ];
        }

        $order = new Order((int) $customerThread->id_order);

        if (Validate::isLoadedObject($order)) {
            $order_history = $order->getHistory($this->context->language->id);
            foreach ($order_history as $history) {
                $link_order = $this->context->link->getAdminLink('AdminOrders', true, [], [
                    'vieworder' => 1,
                    'id_order' => (int) $order->id,
                ]);

                $content = sprintf(
                    '<a class="badge badge-primary rounded" target="_blank" href="%s">%s #%d</a><br/><br/>',
                    Tools::safeOutput($link_order),
                    $this->translator->trans('Order', [], 'Admin.Global'),
                    $order->id
                );

                $content .= sprintf(
                    '<span>%s %s</span>',
                    $this->translator->trans('Status:', [], 'Admin.Catalog.Feature'),
                    $history['ostate_name']
                );

                $timeline[$history['date_add']][] = [
                    'arrow' => 'right',
                    'alt' => true,
                    'background_color' => $history['color'],
                    'icon' => 'credit_card',
                    'content' => $content,
                    'date' => $history['date_add'],
                    'see_more_link' => $link_order,
                    'related_order_id' => (int) $order->id,
                ];
            }
        }

        krsort($timeline);

        $timelineItems = [];

        foreach ($timeline as $items) {
            foreach ($items as $item) {
                $timelineItems[] = new CustomerThreadTimelineItem(
                    $item['content'],
                    $item['icon'],
                    $item['arrow'],
                    $item['date'],
                    isset($item['background_color']) ? $item['background_color'] : null,
                    $item['related_order_id']
                );
            }
        }

        return new CustomerThreadTimeline($timelineItems);
    }

    /**
     * @param CustomerThread $thread
     *
     * @return array
     */
    private function getAvailableActions(CustomerThread $thread)
    {
        $actions = [];

        if ($thread->status !== CustomerThreadStatus::CLOSED) {
            $actions[CustomerThreadStatus::CLOSED] = [
                'label' => $this->translator->trans('Mark as "handled"', [], 'Admin.Catalog.Feature'),
                'value' => CustomerThreadStatus::CLOSED,
            ];
        } else {
            $actions[CustomerThreadStatus::OPEN] = [
                'label' => $this->translator->trans('Re-open', [], 'Admin.Catalog.Feature'),
                'value' => CustomerThreadStatus::OPEN,
            ];
        }

        if ($thread->status !== CustomerThreadStatus::PENDING_1) {
            $actions[CustomerThreadStatus::PENDING_1] = [
                'label' => $this->translator->trans(
                    'Mark as "pending 1" (will be answered later)',
                    [],
                    'Admin.Catalog.Feature'
                ),
                'value' => CustomerThreadStatus::PENDING_1,
            ];
        } else {
            $actions[CustomerThreadStatus::PENDING_1] = [
                'label' => $this->translator->trans('Disable pending status', [], 'Admin.Catalog.Feature'),
                'value' => CustomerThreadStatus::OPEN,
            ];
        }

        if ($thread->status !== CustomerThreadStatus::PENDING_2) {
            $actions[CustomerThreadStatus::PENDING_2] = [
                'label' => $this->translator->trans(
                    'Mark as "pending 2" (will be answered later)',
                    [],
                    'Admin.Catalog.Feature'
                ),
                'value' => CustomerThreadStatus::PENDING_2,
            ];
        } else {
            $actions[CustomerThreadStatus::PENDING_2] = [
                'label' => $this->translator->trans('Disable pending status', [], 'Admin.Catalog.Feature'),
                'value' => CustomerThreadStatus::OPEN,
            ];
        }

        return $actions;
    }

    /**
     * @param CustomerThread $thread
     *
     * @return CustomerInformation
     */
    private function getCustomerInformation(CustomerThread $thread)
    {
        if (!$thread->id_customer) {
            return CustomerInformation::withEmailOnly($thread->email);
        }

        $customer = new Customer($thread->id_customer);
        $orders = Order::getCustomerOrders($customer->id);

        $totalOk = 0;
        $ordersOk = [];

        if ($orders && count($orders)) {
            foreach ($orders as $key => $order) {
                if ($order['valid']) {
                    $ordersOk[] = $order;
                    $totalOk += $order['total_paid_real'] / $order['conversion_rate'];
                }

                $orders[$key]['date_add'] = Tools::displayDate($order['date_add']);
                $orders[$key]['total_paid_real'] = Tools::displayPrice(
                    $order['total_paid_real'],
                    new Currency((int) $order['id_currency'])
                );
            }
        }

        return new CustomerInformation(
            $customer->id,
            $customer->firstname,
            $customer->lastname,
            $thread->email,
            count($ordersOk),
            $totalOk ? Tools::displayPrice($totalOk, $this->context->currency) : $totalOk,
            (new DateTime($customer->date_add))->format($this->context->language->date_format_lite)
        );
    }

    /**
     * @param CustomerThread $thread
     *
     * @return string|null
     */
    private function getContactName(CustomerThread $thread)
    {
        $contacts = Contact::getContacts($this->context->language->id);

        foreach ($contacts as $contact) {
            if ((int) $contact['id_contact'] === (int) $thread->id_contact) {
                return $contact['name'];
            }
        }

        return null;
    }
}
