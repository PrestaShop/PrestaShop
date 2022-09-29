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
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use ZxcvbnPhp\Zxcvbn;

/**
 * Stores plain password
 */
class Password
{
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
     * @param int|null $minLength
     * @param int|null $maxLength
     * @param int|null $minScore
     */
    public function __construct(string $password, $minLength = null, $maxLength = null, $minScore = null)
    {
        $this->password = $password;
        $this->minLength = (is_int($minLength) ? $minLength : PasswordPolicyConfiguration::DEFAULT_MINIMUM_LENGTH);
        $this->maxLength = (is_int($maxLength) ? $maxLength : PasswordPolicyConfiguration::DEFAULT_MAXIMUM_LENGTH);
        $this->minScore = (is_int($minScore) ? $minScore : 0);

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
