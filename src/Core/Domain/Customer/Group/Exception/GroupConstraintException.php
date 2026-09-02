<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception;

/**
 * Is thrown when Group constraints are violated
 */
class GroupConstraintException extends GroupException
{
    /**
     * When invalid groupId value is provided
     */
    public const INVALID_ID = 10;

    public const INVALID_REDUCTION = 20;

    public const EMPTY_SHOP_LIST = 30;

    public const EMPTY_NAME = 40;

    public const NAME_TOO_LONG = 50;

    public const INVALID_NAME = 60;

    public const INVALID_PRICE_DISPLAY_METHOD = 70;
}
