<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Security\Command;

use PrestaShop\PrestaShop\Core\Domain\Security\ValueObject\EmployeeSessionId;

/**
 * Class DeleteEmployeeSessionCommand is a command to delete employee session by given id.
 */
class DeleteEmployeeSessionCommand
{
    /**
     * @var EmployeeSessionId
     */
    private $sessionId;

    /**
     * @param int $sessionId
     */
    public function __construct(int $sessionId)
    {
        $this->sessionId = new EmployeeSessionId($sessionId);
    }

    /**
     * @return EmployeeSessionId
     */
    public function getEmployeeSessionId(): EmployeeSessionId
    {
        return $this->sessionId;
    }
}
