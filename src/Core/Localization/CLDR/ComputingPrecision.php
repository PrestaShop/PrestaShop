<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

/**
 * {@inheritdoc}
 */
final class ComputingPrecision implements ComputingPrecisionInterface
{
    public const MULTIPLIER = 1;
    public const MINIMAL_VALUE = 0;

    /**
     * {@inheritdoc}
     */
    public function getPrecision(int $displayPrecision)
    {
        // the MULTIPLIER attribute is set to 1 for now, so that it matches display precision
        $computingPrecision = $displayPrecision * self::MULTIPLIER;

        return ($computingPrecision < self::MINIMAL_VALUE) ? self::MINIMAL_VALUE : $computingPrecision;
    }
}
