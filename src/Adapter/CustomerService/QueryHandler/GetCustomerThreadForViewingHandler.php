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
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
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

        $this->context->cookie->{'customer_threadFilter_cl!id_contact'} = $customerThread->id_contact;

        $nextCustomerThreadId = $this->getNextCustomerThreadId($query->getCustomerThreadId());

        return new CustomerThreadView(
            $query->getCustomerThreadId(),
            $this->getAvailableActions($customerThread),
            $this->getCustomerInformation($customerThread),
            $this->getContactName($customerThread),
            $this->getCustomerThreadMessages($query->getCustomerThreadId())
        );
    }

    /**
     * @param CustomerThreadId $customerThreadId
     *
     * @return array
     */
    private function getCustomerThreadMessages(CustomerThreadId $customerThreadId)
    {
        $messages = CustomerThread::getMessageCustomerThreads($customerThreadId->getValue());

        foreach ($messages as $key => $message) {
            if ($message['id_employee']) {
                $employee = new Employee($message['id_employee']);
                $messages[$key]['employee_image'] = $employee->getImage();
            }

            if (isset($message['file_name']) && $message['file_name'] != '') {
                $messages[$key]['file_name'] = _THEME_PROD_PIC_DIR_ . $message['file_name'];
            } else {
                unset($messages[$key]['file_name']);
            }

            if ($message['id_product']) {
                $product = new Product((int) $message['id_product'], false, $this->context->language->id);

                if (Validate::isLoadedObject($product)) {
                    $messages[$key]['product_name'] = $product->name;
                    $messages[$key]['product_link'] = $this->context->link->getAdminLink('AdminProducts', true, [], [
                        'updateproduct' => 1,
                        'id_product' => (int) $product->id,
                    ]);
                }
            }

            $messages[$key]['type'] = $message['id_employee'] ? 'employee' : 'customer';
        }

        return $messages;
    }

    /**
     * @param CustomerThreadId $customerThreadId
     *
     * @return CustomerThreadId|null
     */
    private function getNextCustomerThreadId(CustomerThreadId $customerThreadId)
    {
        $nextCustomerThreadId = CustomerThread::getNextThread($customerThreadId->getValue());

        if (!$nextCustomerThreadId) {
            return null;
        }

        return new CustomerThreadId((int) $nextCustomerThreadId);
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
            throw new CustomerThreadNotFoundException(
                sprintf('Customer thread with id "%s" was not found', $customerThreadId->getValue())
            );
        }

        return $customerThread;
    }

    public function getTimeline(array $messages, $orderId)
    {
        $timeline = [];

        foreach ($messages as $message) {
            $product = new Product((int) $message['id_product'], false, $this->context->language->id);

            $content = '';

            if (!$message['private']) {
                $content .= sprintf(
                    '%s <span class="badge">%s</span><br/>',
                    $this->translator->trans('Message to: ', [], 'Admin.Catalog.Feature'),
                    !$message['id_employee'] ? $message['subject'] : $message['customer_name']
                );
            }

            if (Validate::isLoadedObject($product)) {
                $content .= sprintf(
                    '<br/>%s<span class="label label-info">%s</span><br/><br/>',
                    $this->translator->trans('Product: ', [], 'Admin.Catalog.Feature'),
                    $product->name
                );
            }

            $content .= Tools::safeOutput($message['message']);

            $timeline[$message['date_add']][] = [
                'arrow' => 'left',
                'background_color' => '',
                'icon' => 'icon-envelope',
                'content' => $content,
                'date' => $message['date_add'],
            ];
        }

        $order = new Order((int) $orderId);

        if (Validate::isLoadedObject($order)) {
            $order_history = $order->getHistory($this->context->language->id);
            foreach ($order_history as $history) {
                $link_order = $this->context->link->getAdminLink('AdminOrders', true, [], [
                    'vieworder' => 1,
                     'id_order' => (int) $order->id
                ]);

                $content = '<a class="badge" target="_blank" href="' . Tools::safeOutput($link_order) . '">' . $this->translator->trans('Order', [], 'Admin.Global') . ' #' . (int) $order->id . '</a><br/><br/>';

                $content .= '<span>' . $this->translator->trans('Status:', [], 'Admin.Catalog.Feature') . ' ' . $history['ostate_name'] . '</span>';

                $timeline[$history['date_add']][] = [
                    'arrow' => 'right',
                    'alt' => true,
                    'background_color' => $history['color'],
                    'icon' => 'icon-credit-card',
                    'content' => $content,
                    'date' => $history['date_add'],
                    'see_more_link' => $link_order,
                ];
            }
        }

        krsort($timeline);

        return $timeline;
    }

    /**
     * @param CustomerThread $thread
     *
     * @return array
     */
    private function getAvailableActions(CustomerThread $thread)
    {
        $actions = [];

        if ($thread->status !== 'closed') {
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

        if ($thread->status !== 'pending1') {
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
                'value' => CustomerThreadStatus::PENDING_1,
            ];
        }

        if ($thread->status !== 'pending2') {
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
                'value' => CustomerThreadStatus::PENDING_2,
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
            return new CustomerInformation(null, null, null, $thread->email, null, null, null);
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

        $contact = null;

        foreach ($contacts as $c) {
            if ($c['id_contact'] == $thread->id_contact) {
                $contact = $c['name'];

                break;
            }
        }

        return $contact;
    }
}
