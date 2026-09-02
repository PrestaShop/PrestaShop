<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Exception;

/**
 * Class DomainConstraintException is responsible for holding exception codes which can be raised in reusable way.
 */
class DomainConstraintException extends DomainException
{
    /**
     * @var int - raised when native php email validation fails. E.g filter_var($email, FILTER_VALIDATE_EMAIL)
     */
    public const INVALID_EMAIL = 1;

    /**
     * Used when invalid money amount is provided
     */
    public const INVALID_MONEY_AMOUNT = 2;

    /**
     * When price reduction type is not within defined types
     */
    public const INVALID_REDUCTION_TYPE = 3;

    /**
     * When price reduction percentage value is not valid
     */
    public const INVALID_REDUCTION_PERCENTAGE = 4;

    /**
     * When price reduction amount value is not valid
     */
    public const INVALID_REDUCTION_AMOUNT = 5;
}
