<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierReferenceId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Set the list of carriers for a product
 */
class SetCarriersCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @var CarrierReferenceId[]|null
     */
    private $carrierReferenceIds;

    /**
     * @param int $productId
     * @param int[] $carrierReferenceIds List of carrier reference IDs (instead of usual primary id as most entities)
     * @param ShopConstraint $shopConstraint
     *
     * @throws ProductConstraintException
     */
    public function __construct(
        int $productId,
        array $carrierReferenceIds,
        ShopConstraint $shopConstraint
    ) {
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
        $this->setCarrierReferenceIds($carrierReferenceIds);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @return CarrierReferenceId[]
     */
    public function getCarrierReferenceIds(): ?array
    {
        return $this->carrierReferenceIds;
    }

    /**
     * @param int[] $carrierReferenceIds
     */
    private function setCarrierReferenceIds(array $carrierReferenceIds): void
    {
        $this->carrierReferenceIds = [];
        foreach (array_unique($carrierReferenceIds) as $carrierReferenceId) {
            $this->carrierReferenceIds[] = new CarrierReferenceId((int) $carrierReferenceId);
        }
    }
}
