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
use Employee;
use Order;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler\GetCustomerThreadForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
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

        $employees = Employee::getEmployees();
        $messages = $this->getCustomerThreadMessages($query->getCustomerThreadId());

        $nextCustomerThreadId = $this->getNextCustomerThreadId($query->getCustomerThreadId());

        $contacts = Contact::getContacts($this->context->language->id);

        if ($customerThread->id_customer) {
            $customer = new Customer($customerThread->id_customer);
            $orders = Order::getCustomerOrders($customer->id);

            if ($orders && count($orders)) {
                $totalOk = 0;
                $ordersOk = [];

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

            $products = $customer->getBoughtProducts();
            if ($products && count($products)) {
                foreach ($products as $key => $product) {
                    $products[$key]['date_add'] = Tools::displayDate($product['date_add'], null, true);
                }
            }
        }

        return new CustomerThreadView();
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
                        ]
                    );
                }
            }
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

            $timeline[$message['date_add']][] = array(
                'arrow' => 'left',
                'background_color' => '',
                'icon' => 'icon-envelope',
                'content' => $content,
                'date' => $message['date_add'],
            );
        }

        $order = new Order((int) $orderId);
        if (Validate::isLoadedObject($order)) {
            $order_history = $order->getHistory($this->context->language->id);
            foreach ($order_history as $history) {
                $link_order = $this->context->link->getAdminLink('AdminOrders') . '&vieworder&id_order=' . (int) $order->id;

                $content = '<a class="badge" target="_blank" href="' . Tools::safeOutput($link_order) . '">' . $this->trans('Order', array(), 'Admin.Global') . ' #' . (int) $order->id . '</a><br/><br/>';

                $content .= '<span>' . $this->trans('Status:', array(), 'Admin.Catalog.Feature') . ' ' . $history['ostate_name'] . '</span>';

                $timeline[$history['date_add']][] = array(
                    'arrow' => 'right',
                    'alt' => true,
                    'background_color' => $history['color'],
                    'icon' => 'icon-credit-card',
                    'content' => $content,
                    'date' => $history['date_add'],
                    'see_more_link' => $link_order,
                );
            }
        }

        krsort($timeline);

        return $timeline;
    }
}
