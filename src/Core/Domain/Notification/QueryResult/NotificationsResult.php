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
