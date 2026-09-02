<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Notification\QueryHandler;

use Notification;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Notification\Query\GetNotificationLastElements;
use PrestaShop\PrestaShop\Core\Domain\Notification\QueryHandler\GetNotificationLastElementsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult\NotificationResult;
use PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult\NotificationsResult;
use PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult\NotificationsResults;

/**
 * Get employee last notification elements
 *
 * @internal
 */
#[AsQueryHandler]
final class GetNotificationLastElementsHandler implements GetNotificationLastElementsHandlerInterface
{
    /**
     * @param GetNotificationLastElements $query
     *
     * @return NotificationsResults
     *
     * {@inheritdoc}
     */
    public function handle(GetNotificationLastElements $query): NotificationsResults
    {
        $elements = (new Notification())->getActiveLastElements();
        $results = [];
        foreach ($elements as $type => $notifications) {
            $notificationsResult = [];
            $totalNotifications = $notifications['total'];
            foreach ($notifications['results'] as $notification) {
                $notificationsResult[] = new NotificationResult(
                    $notification['id_order'],
                    $notification['id_customer'],
                    $notification['customer_name'],
                    $notification['id_customer_message'],
                    $notification['id_customer_thread'],
                    $notification['customer_view_url'],
                    $notification['total_paid'],
                    $notification['carrier'],
                    $notification['iso_code'],
                    $notification['company'],
                    $notification['status'],
                    $notification['date_add'],
                    $notification['customer_thread_view_url'],
                    $notification['order_view_url']
                );
            }
            $results[] = new NotificationsResult($type, $totalNotifications, $notificationsResult);
        }

        return new NotificationsResults($results);
    }
}
