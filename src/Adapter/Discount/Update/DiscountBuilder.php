<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\Update;

use CartRule;
use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class DiscountBuilder
{
    public function __construct(
        private readonly DiscountTypeRepository $discountTypeRepository
    ) {
    }

    public function build(AddDiscountCommand $command): CartRule
    {
        $cartRule = new CartRule();
        $validFrom = $command->getValidFrom() ?: new DateTimeImmutable();
        $validTo = $command->getValidTo();

        $cartRule->name = $command->getLocalizedNames();
        $cartRule->description = $command->getDescription();
        $cartRule->code = $command->getCode();
        $cartRule->highlight = $command->isHighlightInCart();
        $cartRule->partial_use = $command->allowPartialUse();
        $cartRule->priority = $command->getPriority();
        $cartRule->active = $command->isActive();
        $cartRule->id_customer = $command->getCustomerId()?->getValue();
        $cartRule->date_from = $validFrom->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
        $cartRule->date_to = $validTo !== null ? $validTo->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT) : null;
        // We are initializing the discount so the remaining quantity and the total quantity are the same thing
        $cartRule->total_quantity = $command->getTotalQuantity();
        $cartRule->quantity = $command->getTotalQuantity();
        $cartRule->quantity_per_user = $command->getQuantityPerUser();
        $cartRule->reduction_amount = 0;
        $cartRule->reduction_currency = 0;
        $cartRule->reduction_tax = false;
        $cartRule->reduction_percent = 0;

        $discountType = $command->getDiscountType()->getValue();
        $cartRule->id_cart_rule_type = $this->discountTypeRepository->getTypeIdByString($discountType);
        $cartRule->free_shipping = $discountType === DiscountType::FREE_SHIPPING;

        if (in_array($command->getDiscountType()->getValue(), [
            DiscountType::CART_LEVEL,
            DiscountType::ORDER_LEVEL,
            DiscountType::PRODUCT_LEVEL]
        )) {
            if ($command->getReductionPercent()) {
                $cartRule->reduction_percent = (float) (string) $command->getReductionPercent();
            }
            if ($command->getReductionAmount()) {
                $cartRule->reduction_amount = (float) (string) $command->getReductionAmount()->getAmount();
                $cartRule->reduction_currency = $command->getReductionAmount()->getCurrencyId()->getValue();
                $cartRule->reduction_tax = $command->getReductionAmount()->isTaxIncluded();
            }
        } elseif ($command->getDiscountType()->getValue() === DiscountType::FREE_GIFT) {
            $cartRule->gift_product = $command->getGiftProductId()?->getValue() ?? 0;
            $cartRule->gift_product_attribute = $command->getGiftCombinationId()?->getValue() ?? 0;
        }

        if ($command->getDiscountType()->getValue() === DiscountType::PRODUCT_LEVEL) {
            if ($command->getCheapestProduct()) {
                // If cheapest product is enabled we set the specific value
                $cartRule->reduction_product = DiscountSettings::CHEAPEST_PRODUCT;
            } elseif (null !== $command->getReductionProductId()) {
                $cartRule->reduction_product = $command->getReductionProductId()->getValue();
            }
        }

        if (null !== $command->getMinimumProductQuantity()) {
            $cartRule->minimum_product_quantity = $command->getMinimumProductQuantity();
        }

        if (null !== $command->getMinimumAmount()) {
            $cartRule->minimum_amount = (float) (string) $command->getMinimumAmount()->getAmount();
            $cartRule->minimum_amount_currency = $command->getMinimumAmount()->getCurrencyId()->getValue();
            $cartRule->minimum_amount_tax = $command->getMinimumAmount()->isTaxIncluded();
            $cartRule->minimum_amount_shipping = $command->getMinimumAmount()->isShippingIncluded();
        } else {
            $cartRule->minimum_amount = 0;
            $cartRule->minimum_amount_currency = 0;
            $cartRule->minimum_amount_tax = false;
            $cartRule->minimum_amount_shipping = false;
        }

        return $cartRule;
    }
}
