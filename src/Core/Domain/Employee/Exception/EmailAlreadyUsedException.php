<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Exception;

use Exception;

/**
 * Thrown when employee's email is already used by another employee.
 */
class EmailAlreadyUsedException extends EmployeeException
{
    /**
     * @var string
     */
    private $email;

    /**
     * @param string $email the email that's being used
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($email, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
