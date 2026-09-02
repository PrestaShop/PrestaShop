<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use Stringable;

interface SupplierAssociationInterface extends Stringable
{
    /**
     * @return ProductId|null
     */
    public function getProductId(): ?ProductId;

    /**
     * @return CombinationIdInterface
     */
    public function getCombinationId(): CombinationIdInterface;

    /**
     * @return SupplierId
     */
    public function getSupplierId(): SupplierId;

    /**
     * @return ProductSupplierId|null
     */
    public function getProductSupplierId(): ?ProductSupplierId;
}
