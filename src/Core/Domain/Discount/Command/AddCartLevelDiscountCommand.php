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
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

class AddCartLevelDiscountCommand extends AddDiscountCommand
{
    private ?DecimalNumber $percentDiscount = null;
    private ?Money $amountDiscount = null;

    public function __construct(
    ) {
        parent::__construct(DiscountType::CART_DISCOUNT);
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
        if ($this->getAmountDiscount() !== null) {
            throw new DiscountConstraintException('Amount discount is already set.', DiscountConstraintException::INVALID_AMOUNT_DISCOUNT_ALREADY_SET);
        }
        if ($percentDiscount->isLowerThanZero() || $percentDiscount->isGreaterThan(new DecimalNumber('100.0'))) {
            throw new DiscountConstraintException(sprintf('Invalid percent reduction "%s".', $percentDiscount), DiscountConstraintException::INVALID_REDUCTION_PERCENT);
        }
        $this->percentDiscount = $percentDiscount;

        return $this;
    }

    public function getAmountDiscount(): ?Money
    {
        return $this->amountDiscount;
    }

    /**
     * @throws DiscountConstraintException
     * @throws DomainConstraintException
     */
    public function setAmountDiscount(DecimalNumber $amountDiscount, CurrencyId $currencyId, bool $taxIncluded): self
    {
        if ($this->getPercentDiscount() !== null) {
            throw new DiscountConstraintException('Percent discount is already set.', DiscountConstraintException::INVALID_PERCENT_DISCOUNT_ALREADY_SET);
        }
        if ($amountDiscount->isLowerThanZero()) {
            throw new DiscountConstraintException(sprintf('Invalid amount reduction "%s".', $amountDiscount), DiscountConstraintException::INVALID_REDUCTION_AMOUNT);
        }
        $this->amountDiscount = new Money($amountDiscount, $currencyId, $taxIncluded);

        return $this;
    }
}
