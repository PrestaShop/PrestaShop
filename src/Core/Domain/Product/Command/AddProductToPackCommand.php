<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Adds product to a pack
 */
class AddProductToPackCommand
{
    /**
     * @var ProductId
     */
    private $productPackId;

    /**
     * @var ProductId
     */
    private $productToPackId;

    /**
     * @var CombinationId|null
     */
    private $combinationToPackId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param int $productPackId
     * @param int $productToPackId
     * @param int|null $combinationToPackId
     * @param int $quantity
     */
    public function __construct(
        int $productPackId,
        int $productToPackId,
        int $quantity,
        ?int $combinationToPackId
    ) {
        $this->productPackId = new ProductId($productPackId);
        $this->productToPackId = new ProductId($productToPackId);
        $this->quantity = $quantity;

        if ($combinationToPackId !== null) {
            $this->combinationToPackId = new CombinationId($combinationToPackId);
        }
    }

    /**
     * @return ProductId
     */
    public function getProductPackId(): ProductId
    {
        return $this->productPackId;
    }

    /**
     * @return ProductId
     */
    public function getProductToPackId(): ProductId
    {
        return $this->productToPackId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationToPackId(): ?CombinationId
    {
        return $this->combinationToPackId;
    }
}
