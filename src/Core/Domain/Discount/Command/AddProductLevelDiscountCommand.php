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

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;

class AddProductLevelDiscountCommand extends AddDiscountCommand
{
    private ?DecimalNumber $percentDiscount = null;
    private int $reductionProduct = 0;

    public function __construct(
    ) {
        parent::__construct(DiscountType::PRODUCTS_DISCOUNT);
    }

    public function getPercentDiscount(): ?DecimalNumber
    {
        return $this->percentDiscount;
    }

    /**
     * @throws DiscountConstraintException
     */
    public function setPercentDiscount(DecimalNumber $percentDiscount): self
    {
        if ($percentDiscount->isLowerThanZero() || $percentDiscount->isGreaterThan(new DecimalNumber('100.0'))) {
            throw new DiscountConstraintException(sprintf('Invalid percent reduction "%s".', $percentDiscount), DiscountConstraintException::INVALID_REDUCTION_PERCENT);
        }
        $this->percentDiscount = $percentDiscount;

        return $this;
    }

    public function getReductionProduct(): int
    {
        return $this->reductionProduct;
    }

    /**
     * @param int $reductionProduct
     *
     * @return $this
     *
     * This can have several values
     *  0 => The discount is not a Product discount
     * -1 => The discounted product is the cheapest of the cart
     * -2 => The discount is applied on a selection of product // this case is not yet handled.
     * >0 => The productId of the discounted product
     */
    public function setReductionProduct(int $reductionProduct): self
    {
        $this->reductionProduct = $reductionProduct;

        return $this;
    }
}
