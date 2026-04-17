<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature;

/**
 * Holds constants for FeatureValue when those settings doesn't have its own dedicated Value Objects
 */
class FeatureValueSettings
{
    /**
     * Maximum allowed length for feature value "value" property
     */
    public const VALUE_MAX_LENGTH = 255;

    /**
     * This class is not supposed to be initialized
     */
    private function __construct()
    {
    }
}
