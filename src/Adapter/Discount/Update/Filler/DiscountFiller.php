<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\Update\Filler;

use CartRule;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Trait\ProductConditionsTrait;
use PrestaShop\PrestaShop\Adapter\Domain\LocalizedObjectModelTrait;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class DiscountFiller
{
    use LocalizedObjectModelTrait;
    use ProductConditionsTrait;

    public function __construct(
        protected readonly DiscountRepository $discountRepository,
    ) {
    }

    public function fillUpdatableProperties(CartRule $cartRule, UpdateDiscountCommand $command): array
    {
        $updatableProperties = [];
        if ($command->isDirty('validFrom')) {
            $cartRule->date_from = $command->getValidFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'date_from';
        }
        if ($command->isDirty('validTo')) {
            $validTo = $command->getValidTo();
            $cartRule->date_to = $validTo !== null ? $validTo->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT) : null;
            $updatableProperties[] = 'date_to';
        }
        if ($command->isDirty('localizedNames')) {
            $cartRule->name = $command->getLocalizedNames();
            $this->fillLocalizedValues($cartRule, 'name', $command->getLocalizedNames(), $updatableProperties);
        }
        if ($command->isDirty('description')) {
            $cartRule->description = $command->getDescription();
            $updatableProperties[] = 'description';
        }
        if ($command->isDirty('code')) {
            $cartRule->code = $command->getCode();
            $updatableProperties[] = 'code';
        }
        if ($command->isDirty('highlightInCart')) {
            $cartRule->highlight = $command->isHighlightInCart();
            $updatableProperties[] = 'highlight';
        }
        if ($command->isDirty('allowPartialUse')) {
            $cartRule->partial_use = $command->allowPartialUse();
            $updatableProperties[] = 'partial_use';
        }
        if ($command->isDirty('active')) {
            $cartRule->active = $command->isActive();
            $updatableProperties[] = 'active';
        }
        if ($command->isDirty('customerId')) {
            $cartRule->id_customer = $command->getCustomerId()->getValue();
            $updatableProperties[] = 'id_customer';
        }
        if ($command->isDirty('totalQuantity')) {
            if (null === $command->getTotalQuantity()) {
                $cartRule->total_quantity = null;
                $cartRule->quantity = null;
            } else {
                // Update total quantity field with provided integer value, the remaining quantity should be updated
                // accordingly based on the already used quantity
                $quantityUsed = $this->discountRepository->getQuantityUsedInOrders($command->getDiscountId());
                $cartRule->total_quantity = $command->getTotalQuantity();
                $cartRule->quantity = max($cartRule->total_quantity - $quantityUsed, 0);
            }
            $updatableProperties[] = 'total_quantity';
            $updatableProperties[] = 'quantity';
        }
        if ($command->isDirty('quantityPerUser')) {
            $cartRule->quantity_per_user = $command->getQuantityPerUser();
            $updatableProperties[] = 'quantity_per_user';
        }
        if ($command->isDirty('priority')) {
            $cartRule->priority = $command->getPriority();
            $updatableProperties[] = 'priority';
        }
        if ($command->isDirty('cheapestProduct')) {
            // If cheapest product is enabled we set the specific value, if not we use 0 as the no target value
            $cartRule->reduction_product = $command->getCheapestProduct() ? DiscountSettings::CHEAPEST_PRODUCT : 0;
            $updatableProperties[] = 'reduction_product';
        }
        if ($command->isDirty('reductionProductId')) {
            if (null === $command->getReductionProductId()) {
                $cartRule->reduction_product = 0;
            } else {
                $cartRule->reduction_product = $command->getReductionProductId()->getValue();
            }
            $updatableProperties[] = 'reduction_product';
        }

        // If a segment is being targeted we automatically update the reduction_product (or the validator will trigger
        // an error) but only if the command isn't modifying the cheapest product target as well, in which case reduction_product
        // value uses DiscountSettings::CHEAPEST_PRODUCT
        if (null !== $command->getProductConditions() && null === $command->getCheapestProduct()) {
            if ($this->isSegmentTargeted($command->getProductConditions())) {
                $cartRule->reduction_product = DiscountSettings::PRODUCT_SEGMENT;
                $updatableProperties[] = 'reduction_product';
            }
        }

        if ($command->isDirty('reductionAmount') && null !== $command->getReductionAmount()) {
            $cartRule->reduction_amount = (float) (string) $command->getReductionAmount()->getAmount();
            $cartRule->reduction_currency = $command->getReductionAmount()->getCurrencyId()->getValue();
            $cartRule->reduction_tax = $command->getReductionAmount()->isTaxIncluded();
            $updatableProperties[] = 'reduction_amount';
            $updatableProperties[] = 'reduction_currency';
            $updatableProperties[] = 'reduction_tax';

            if (!$command->isDirty('reductionPercent')) {
                $cartRule->reduction_percent = null;
                $updatableProperties[] = 'reduction_percent';
            }
        }
        if ($command->isDirty('reductionPercent')) {
            $cartRule->reduction_percent = (float) (string) $command->getReductionPercent();
            $updatableProperties[] = 'reduction_percent';

            if (!$command->isDirty('reductionAmount')) {
                $cartRule->reduction_amount = null;
                $cartRule->reduction_currency = 0;
                $cartRule->reduction_tax = false;
                $updatableProperties[] = 'reduction_amount';
                $updatableProperties[] = 'reduction_currency';
                $updatableProperties[] = 'reduction_tax';
            }
        }
        if ($command->isDirty('giftProductId')) {
            $cartRule->gift_product = $command->getGiftProductId()?->getValue() ?: 0;
            $updatableProperties[] = 'gift_product';
        }
        if ($command->isDirty('giftCombinationId')) {
            $cartRule->gift_product_attribute = $command->getGiftCombinationId()?->getValue() ?: 0;
            $updatableProperties[] = 'gift_product_attribute';
        }

        if ($command->isDirty('minimumProductQuantity')) {
            $cartRule->minimum_product_quantity = $command->getMinimumProductQuantity() ?? 0;
            $updatableProperties[] = 'minimum_product_quantity';
        }

        if ($command->isDirty('minimumAmount')) {
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
            $updatableProperties[] = 'minimum_amount';
            $updatableProperties[] = 'minimum_amount_currency';
            $updatableProperties[] = 'minimum_amount_tax';
            $updatableProperties[] = 'minimum_amount_shipping';
        }

        return $updatableProperties;
    }
}
