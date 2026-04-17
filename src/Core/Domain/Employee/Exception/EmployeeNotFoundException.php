<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Exception;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

/**
 * Class EmployeeNotFoundException is thrown when employee cannot be found.
 */
class EmployeeNotFoundException extends EmployeeException
{
    /**
     * @var EmployeeId
     */
    private $employeeId;

    /**
     * @param EmployeeId $employeeId
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(EmployeeId $employeeId, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->employeeId = $employeeId;
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }
}
