<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Sets specific price priority for provided product
 */
class SetSpecificPricePriorityForProductCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var PriorityList
     */
    private $priorityList;

    /**
     * @param int $productId
     * @param string[] $priorities
     */
    public function __construct(
        int $productId,
        array $priorities
    ) {
        $this->productId = new ProductId($productId);
        $this->priorityList = new PriorityList($priorities);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return PriorityList
     */
    public function getPriorityList(): PriorityList
    {
        return $this->priorityList;
    }
}
