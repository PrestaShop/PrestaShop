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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;

/**
 * Holds delta quantity for a product update along with a stock movement reason
 */
class StockModification
{
    /**
     * @var int|null
     */
    private $deltaQuantity;

    /**
     * @var int|null
     */
    private $fixedQuantity;

    /**
     * Builds class using delta quantity (delta means the quantity will be added up to the previous quantity)
     *
     * @param int $deltaQuantity
     *
     * @return self
     */
    public static function buildDeltaQuantity(int $deltaQuantity): self
    {
        return new self($deltaQuantity, null);
    }

    /**
     * Builds class using fixed quantity (fixed means the new quantity will replace the previous quantity)
     *
     * @param int $fixedQuantity
     *
     * @return self
     */
    public static function buildFixedQuantity(int $fixedQuantity): self
    {
        return new self(null, $fixedQuantity);
    }

    /**
     * @return int|null
     */
    public function getDeltaQuantity(): ?int
    {
        return $this->deltaQuantity;
    }

    /**
     * @return int|null
     */
    public function getFixedQuantity(): ?int
    {
        return $this->fixedQuantity;
    }

    /**
     * Constructor is private on purpose. This Value object can either have delta OR fixed quantity set,
     * so we need to use static factory methods to make sure we always build it correctly.
     *
     * @see buildDeltaQuantity
     * @see buildFixedQuantity
     *
     * @param int|null $deltaQuantity
     * @param int|null $fixedQuantity
     *
     * @throws ProductStockConstraintException
     */
    private function __construct(
        ?int $deltaQuantity,
        ?int $fixedQuantity
    ) {
        $this->assertDeltaQuantityIsNotZero($deltaQuantity);
        $this->deltaQuantity = $deltaQuantity;
        $this->fixedQuantity = $fixedQuantity;
    }

    /**
     * @param int|null $quantity
     */
    private function assertDeltaQuantityIsNotZero(?int $quantity): void
    {
        if (0 === $quantity) {
            throw new ProductStockConstraintException(
                'Delta quantity cannot be 0',
                ProductStockConstraintException::INVALID_DELTA_QUANTITY
            );
        }
    }
}
