<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Notification\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Notification\Query\GetNotificationLastElements;
use PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult\NotificationsResults;

/**
 * Interface for service that handles notifications last elements request
 */
interface GetNotificationLastElementsHandlerInterface
{
    /**
     * @param GetNotificationLastElements $query
     *
     * @return NotificationsResults
     */
    public function handle(GetNotificationLastElements $query): NotificationsResults;
}
