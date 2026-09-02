<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception;

/**
 * Thrown when customization field constraints are violated
 */
class CustomizationFieldConstraintException extends CustomizationFieldException
{
    /**
     * When Customization field id is invalid
     */
    public const INVALID_ID = 1;

    /**
     * When customization type is invalid
     */
    public const INVALID_TYPE = 2;

    /**
     * When customization field name is invalid
     */
    public const INVALID_NAME = 3;
}
