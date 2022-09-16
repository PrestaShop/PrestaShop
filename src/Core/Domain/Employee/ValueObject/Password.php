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
