<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\ValueObject;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Read-only contract for any price that carries tax-excluded, tax-included, tax-amount
 * and a tax rate. Implemented by TaxablePrice (auto-sync during computation) and
 * RoundedPrice (frozen independently-rounded values).
 */
interface TaxablePriceInterface
{
    public function getTaxExcluded(): DecimalNumber;

    public function getTaxIncluded(): DecimalNumber;

    public function getTaxAmount(): DecimalNumber;

    public function getTaxRate(): TaxRate;
}
