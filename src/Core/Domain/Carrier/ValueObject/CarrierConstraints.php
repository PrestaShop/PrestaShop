<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use PrestaShop\Decimal\DecimalNumber;

class CarrierConstraints
{
    public function __construct(
        public readonly DecimalNumber $maxWeight,
        public readonly int $maxWidth,
        public readonly int $maxHeight,
        public readonly int $maxDepth,
    ) {
    }
}
