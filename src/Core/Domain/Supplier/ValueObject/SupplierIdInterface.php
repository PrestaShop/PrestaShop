<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject;

/**
 * Defines contract for supplier identity value
 */
interface SupplierIdInterface
{
    /**
     * @return int
     */
    public function getValue(): int;
}
