<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Notification\Command;

use PrestaShop\PrestaShop\Core\Domain\Notification\Exception\NotificationException;
use PrestaShop\PrestaShop\Core\Domain\Notification\ValueObject\Type;

/**
 * Updates the last notification element from a given type seen by the employee
 */
class UpdateEmployeeNotificationLastElementCommand
{
    /**
     * @var Type
     */
    private $type;

    /**
     * @param string $type
     *
     * @throws NotificationException
     */
    public function __construct(string $type)
    {
        $this->type = new Type($type);
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }
}
