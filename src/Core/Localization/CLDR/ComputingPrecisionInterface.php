<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

/**
 *  Will calculate the computing precision (fraction digits number used for computations) that should
 * be used for a given display precision.
 */
interface ComputingPrecisionInterface
{
    /**
     * Number of decimal digits to take into account when computing values
     * for a given display precision
     *
     * @param int $displayPrecision
     *
     * @return int
     */
    public function getPrecision(int $displayPrecision);
}
