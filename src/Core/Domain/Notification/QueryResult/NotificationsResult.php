<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Notification\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Notification\Exception\NotificationException;
use PrestaShop\PrestaShop\Core\Domain\Notification\ValueObject\Type;

/**
 * NotificationsResult contains a collection of Notifications as well as their type and the total
 */
class NotificationsResult
{
    /**
     * @var Type
     */
    private $type;

    /**
     * @var int
     */
    private $total;

    /**
     * @var NotificationResult[]
     */
    private $notifications = [];

    /**
     * NotificationsResult constructor.
     *
     * @param string $type
     * @param int $total
     * @param NotificationResult[] $notifications
     *
     * @throws NotificationException
     */
    public function __construct(string $type, int $total, array $notifications)
    {
        $this->type = new Type($type);
        $this->total = $total;
        $this->notifications = $notifications;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return NotificationResult[]
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }
}
