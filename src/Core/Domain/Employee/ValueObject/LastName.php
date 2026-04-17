<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;

/**
 * Stores employee's last name
 */
class LastName
{
    /**
     * @var int Maximum allowed length for last name
     */
    public const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @param string $lastName
     */
    public function __construct($lastName)
    {
        $this->assertLastNameDoesNotExceedAllowedLength($lastName);
        $this->assertLastNameIsValid($lastName);

        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @throws EmployeeConstraintException
     */
    private function assertLastNameIsValid($lastName)
    {
        $matchesLastNamePattern = preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', stripslashes($lastName));

        if (!$matchesLastNamePattern) {
            throw new EmployeeConstraintException(sprintf('Employee last name %s is invalid', var_export($lastName, true)), EmployeeConstraintException::INVALID_LAST_NAME);
        }
    }

    /**
     * @param string $lastName
     *
     * @throws EmployeeConstraintException
     */
    private function assertLastNameDoesNotExceedAllowedLength($lastName)
    {
        $lastName = html_entity_decode($lastName, ENT_COMPAT, 'UTF-8');

        if (self::MAX_LENGTH < mb_strlen($lastName, 'UTF-8')) {
            throw new EmployeeConstraintException(sprintf('Employee last name is too long. Max allowed length is %s', self::MAX_LENGTH), EmployeeConstraintException::INVALID_LAST_NAME);
        }
    }
}
