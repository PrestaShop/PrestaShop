<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                    'customer_thread_view_url' => $notification->getCustomerThreadViewUrl(),
                    'order_view_url' => $notification->getOrderViewUrl(),
                ];
            }
            $response[$element->getType()->getValue()] = [
                'total' => $element->getTotal(),
                'results' => $notifications,
            ];
        }

        return $response;
    }
}
