<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Exception;

/**
 * Thrown when feature value constraints are violated
 */
class FeatureValueConstraintException extends FeatureValueException
{
    /**
     * Used when feature ID is invalid
     */
    public const INVALID_FEATURE_ID = 10;

    /**
     * Used when value is invalid
     */
    public const INVALID_VALUE = 20;
}
