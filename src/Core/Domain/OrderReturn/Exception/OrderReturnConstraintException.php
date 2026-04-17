<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception;

/**
 * Is thrown when order return constraint is violated
 */
class OrderReturnConstraintException extends OrderReturnException
{
    /**
     * When order return id is not valid
     */
    public const INVALID_ID = 10;

    /**
     * When customer id is not valid
     */
    public const INVALID_ID_CUSTOMER = 20;

    /**
     * When order id is not valid
     */
    public const INVALID_ID_ORDER = 30;

    /**
     * When state is not valid
     */
    public const INVALID_STATE = 40;

    /**
     * When question is not valid
     */
    public const INVALID_QUESTION = 50;

    /**
     * When date added is not valid
     */
    public const INVALID_DATE_ADD = 60;

    /**
     * When date updated is not valid
     */
    public const INVALID_DATE_UPD = 70;
}
