<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Price by Range and Zone for Carriers
 */
class CarrierRangePrice
{
    private DecimalNumber $from;
    private DecimalNumber $to;
    private DecimalNumber $price;

    public function __construct(
        string $from,
        string $to,
        string $price
    ) {
        $this->from = new DecimalNumber($from);
        $this->to = new DecimalNumber($to);
        $this->price = new DecimalNumber($price);
    }

    public function getFrom(): DecimalNumber
    {
        return $this->from;
    }

    public function getTo(): DecimalNumber
    {
        return $this->to;
    }

    public function getPrice(): DecimalNumber
    {
        return $this->price;
    }
}
