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

namespace PrestaShop\PrestaShop\Adapter\Notification\QueryHandler;

use Notification;
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
        $elements = (new Notification())->getLastElements();
        $results = [];
        foreach ($elements as $type => $notifications) {
            $notificationsResult = [];
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
                    $notification['customer_thread_view_url']
                );
            }
            $results[] = new NotificationsResult($type, $notifications['total'], $notificationsResult);
        }

        return new NotificationsResults($results);
    }
}
