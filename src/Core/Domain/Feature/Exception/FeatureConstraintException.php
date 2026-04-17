<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Exception;

/**
 * Thrown when Feature data is not valid.
 */
class FeatureConstraintException extends FeatureException
{
    public const INVALID_ID = 1;

    public const INVALID_NAME = 2;

    public const INVALID_POSITION = 3;

    public const INVALID_SHOP_ASSOCIATION = 4;
}
