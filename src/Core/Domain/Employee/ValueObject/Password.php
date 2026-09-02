<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;
use ZxcvbnPhp\Zxcvbn;

/**
 * Stores employee's plain password.
 */
class Password
{
    /**
     * @var int Minimum required password length for employee
     */
    public const MIN_LENGTH = 8;

    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $minScore;

    /**
     * @var int
     */
    private $minLength;

    /**
     * @var int
     */
    private $maxLength;

    /**
     * @param string $password
     * @param int $minLength
     * @param int $maxLength
     * @param int $minScore
     */
    public function __construct(string $password, int $minLength, int $maxLength, int $minScore)
    {
        $this->password = $password;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->minScore = $minScore;

        $this->assertPasswordIsWithinAllowedLength($password);
        $this->assertPasswordScoreIsAllowed($password);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @throws EmployeeConstraintException
     */
    private function assertPasswordIsWithinAllowedLength(string $password): void
    {
        $length = mb_strlen($password, 'UTF-8');

        if ($this->minLength > $length || $length > $this->maxLength) {
            throw new EmployeeConstraintException(
                sprintf(
                    'Employee password length must be between %d and %d',
                    $this->minLength,
                    $this->maxLength
                ),
                EmployeeConstraintException::INVALID_PASSWORD
            );
        }
    }

    /**
     * @param string $password
     *
     * @throws EmployeeConstraintException
     */
    private function assertPasswordScoreIsAllowed(string $password): void
    {
        $zxcvbn = new Zxcvbn();
        $result = $zxcvbn->passwordStrength($password);
        if (isset($result['score']) && $result['score'] < $this->minScore) {
            throw new EmployeeConstraintException('Employee password is too weak', EmployeeConstraintException::INVALID_PASSWORD);
        }
    }
}
