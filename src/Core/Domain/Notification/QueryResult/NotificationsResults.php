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

namespace PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult;

/**
 * NotificationsResults is a collection of NotificationsResult
 */
class NotificationsResults
{
    /**
     * @var NotificationsResult[]
     */
    private $notifications = [];

    /**
     * NotificationsResults constructor.
     *
     * @param NotificationsResult[] $notifications
     */
    public function __construct(array $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * @return NotificationsResult[]
     */
    public function getNotificationsResults()
    {
        return $this->notifications;
    }

    public function getNotificationsResultsForJS()
    {
        $response = [];
        foreach ($this->getNotificationsResults() as $element) {
            $notifications = [];
            foreach ($element->getNotifications() as $notification) {
                $notifications[] = [
                    'id_order' => $notification->getOrderId(),
                    'id_customer' => $notification->getCustomerId(),
                    'id_customer_message' => $notification->getCustomerMessageId(),
                    'id_customer_thread' => $notification->getCustomerThreadId(),
                    'total_paid' => $notification->getTotalPaid(),
                    'carrier' => $notification->getCarrier(),
                    'iso_code' => $notification->getIsoCode(),
                    'company' => $notification->getCompany(),
                    'status' => $notification->getStatus(),
                    'customer_name' => $notification->getCustomerName(),
                    'date_add' => $notification->getDateAdd(),
                    'customer_view_url' => $notification->getCustomerViewUrl(),
                ];
            }
            $response[($element->getType()->getValue())] = [
                'total' => $element->getTotal(),
                'results' => $notifications,
            ];
        }

        return $response;
    }
}
