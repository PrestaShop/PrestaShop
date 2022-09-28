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

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Exception\PasswordConstraintException;
use ZxcvbnPhp\Zxcvbn;

/**
 * Stores customer's plain password
 */
class Password
{
    /**
     * @deprecated since 8.0.0 use PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH
     *
     * @var int Minimum required password length
     */
    public const MIN_LENGTH = 8;

    /**
     * @deprecated since 8.0.0 use PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH
     *
     * @var int Maximum allowed password length
     *
     * It's limited to 72 chars because of PASSWORD_BCRYPT algorithm
     * used in password_hash() function.
     */
    public const MAX_LENGTH = 72;

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
    public function __construct(string $password, int $minLength = 8, int $maxLength = 72, int $minScore = 0)
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
     */
    private function assertPasswordIsWithinAllowedLength(string $password): void
    {
        $length = mb_strlen($password, 'UTF-8');

        if ($this->minLength > $length || $length > $this->maxLength) {
            throw new PasswordConstraintException(
                sprintf(
                    'Password length must be between %d and %d',
                    $this->minLength,
                    $this->maxLength
                ),
                PasswordConstraintException::INVALID_LENGTH
            );
        }
    }

    /**
     * @param string $password
     */
    private function assertPasswordScoreIsAllowed(string $password): void
    {
        $zxcvbn = new Zxcvbn();
        $result = $zxcvbn->passwordStrength($password);
        if (isset($result['score']) && $result['score'] < $this->minScore) {
            throw new PasswordConstraintException('Employee password is too weak', PasswordConstraintException::WEAK_PASSWORD);
        }
    }
}
