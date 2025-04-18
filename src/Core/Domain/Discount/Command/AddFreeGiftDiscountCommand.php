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

namespace PrestaShop\PrestaShop\Core\Domain\Discount\Command;

use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class AddFreeGiftDiscountCommand extends AddDiscountCommand
{
    private ?ProductId $productId = null;
    private ?CombinationIdInterface $combinationId = null;

    public function __construct()
    {
        parent::__construct(DiscountType::FREE_GIFT);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ?ProductId
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     *
     * @return $this
     */
    public function setProductId(int $productId): self
    {
        $this->productId = new ProductId($productId);

        return $this;
    }

    /**
     * @return CombinationIdInterface
     */
    public function getCombinationId(): ?CombinationIdInterface
    {
        return $this->combinationId;
    }

    /**
     * @param int $combinationId
     *
     * @return $this
     */
    public function setCombinationId(int $combinationId): self
    {
        if (NoCombinationId::NO_COMBINATION_ID === $combinationId) {
            $this->combinationId = new NoCombinationId();
        } else {
            $this->combinationId = new CombinationId($combinationId);
        }

        return $this;
    }
}
