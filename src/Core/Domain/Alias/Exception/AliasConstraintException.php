<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Alias\Exception;

/**
 * Is thrown when alias constraints are violated
 */
class AliasConstraintException extends AliasException
{
    /**
     * Code is used when invalid id is supplied.
     */
    public const INVALID_ID = 10;

    /**
     * When invalid alias search type is supplied.
     */
    public const INVALID_SEARCH = 20;

    /**
     * When invalid alias type is supplied
     */
    public const INVALID_ALIAS = 30;

    /**
     * When alias visibility value is invalid
     */
    public const INVALID_VISIBILITY = 40;

    /**
     * When alias is already used
     */
    public const ALIAS_ALREADY_USED = 50;
}
