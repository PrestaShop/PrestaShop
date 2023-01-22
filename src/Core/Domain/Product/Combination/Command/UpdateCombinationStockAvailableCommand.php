<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
