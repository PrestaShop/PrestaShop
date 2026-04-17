<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception;

/**
 * Thrown when customization constraints are violated
 */
class CustomizationConstraintException extends CustomizationException
{
    /**
     * When customization field is required to be filled
     */
    public const FIELD_IS_REQUIRED = 1;

    /**
     * When customization field value length is exceeded
     */
    public const FIELD_IS_TOO_LONG = 2;

    /**
     * When customization id is invalid
     */
    public const INVALID_ID = 3;
}
