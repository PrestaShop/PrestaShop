<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class UpdateCombinationStockAvailableCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var int|null
     */
    private $deltaQuantity;

    /**
     * @var int|null
     */
    private $fixedQuantity;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $combinationId
     */
    public function __construct(
        int $combinationId,
        ShopConstraint $shopConstraint
    ) {
        $this->combinationId = new CombinationId($combinationId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return int|null
     */
    public function getDeltaQuantity(): ?int
    {
        return $this->deltaQuantity;
    }

    /**
     * @param int $deltaQuantity
     *
     * @return $this
     */
    public function setDeltaQuantity(int $deltaQuantity): self
    {
        if (null !== $this->fixedQuantity) {
            throw new ProductStockConstraintException(
                'Cannot set $deltaQuantity, because $fixedQuantity is already set',
                ProductStockConstraintException::FIXED_AND_DELTA_QUANTITY_PROVIDED
            );
        }
        $this->deltaQuantity = $deltaQuantity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFixedQuantity(): ?int
    {
        return $this->fixedQuantity;
    }

    /**
     * @param int $fixedQuantity
     *
     * @return $this
     */
    public function setFixedQuantity(int $fixedQuantity): self
    {
        if ($this->deltaQuantity) {
            throw new ProductStockConstraintException(
                'Cannot set $fixedQuantity, because $deltaQuantity is already set',
                ProductStockConstraintException::FIXED_AND_DELTA_QUANTITY_PROVIDED
            );
        }
        $this->fixedQuantity = $fixedQuantity;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return $this
     */
    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
