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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Notification\QueryHandler;

use Notification;
use PrestaShop\PrestaShop\Adapter\Admin\NotificationsConfiguration;
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
     * @var array
     */
    protected $configuration;

    /**
     * @param NotificationsConfiguration $notificationsConfiguration
     */
    public function __construct(
        NotificationsConfiguration $notificationsConfiguration
    ) {
        $this->configuration = $notificationsConfiguration->getConfiguration();
    }

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
            $totalNotifications = 0;
            if ($this->isDisplayed($type)) {
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
            }
            $results[] = new NotificationsResult($type, $totalNotifications, $notificationsResult);
        }

        return new NotificationsResults($results);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    protected function isDisplayed(string $type): bool
    {
        switch ($type) {
            case 'customer':
                return $this->configuration['show_notifs_new_customers'] ?: false;
            case 'customer_message':
                return $this->configuration['show_notifs_new_messages'] ?: false;
            case 'order':
                return $this->configuration['show_notifs_new_orders'] ?: false;
        }

        return false;
    }
}
