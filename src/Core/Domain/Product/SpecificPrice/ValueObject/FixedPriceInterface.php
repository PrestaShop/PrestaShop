<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject;

use PrestaShop\Decimal\DecimalNumber;

/**
 * @see FixedPrice
 * @see InitialPrice
 */
interface FixedPriceInterface
{
    /**
     * @return DecimalNumber
     */
    public function getValue(): DecimalNumber;
}
