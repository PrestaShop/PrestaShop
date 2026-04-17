<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Notification\Query;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

/**
 * This Query return the last Notifications elements
 */
class GetNotificationLastElements
{
    /**
     * @var EmployeeId
     */
    private $employeeId;

    /**
     * GetNotificationLastElements constructor.
     *
     * @param int $employeeId
     *
     * @throws InvalidEmployeeIdException
     */
    public function __construct(int $employeeId)
    {
        $this->employeeId = new EmployeeId($employeeId);
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId(): EmployeeId
    {
        return $this->employeeId;
    }
}
