<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\QueryHandler;

use DateTimeImmutable;
use Exception;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryHandler\GetDiscountForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountForEditing;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

#[AsQueryHandler]
class GetDiscountForEditingHandler implements GetDiscountForEditingHandlerInterface
{
    public function __construct(
        protected readonly DiscountRepository $discountRepository,
        protected readonly DiscountTypeRepository $discountTypeRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function handle(GetDiscountForEditing $query): DiscountForEditing
    {
        $cartRule = $this->discountRepository->get($query->getDiscountId());
        $productConditions = $this->discountRepository->getProductRulesGroup($query->getDiscountId());
        $carrierIds = $this->discountRepository->getCarriersIds($query->getDiscountId());
        $countryIds = $this->discountRepository->getCountriesIds($query->getDiscountId());
        $customerGroupIds = $this->discountRepository->getCustomerGroupsIds($query->getDiscountId());
        $compatibleDiscountTypeIds = $this->discountTypeRepository->getCompatibleTypesIdsForDiscount($query->getDiscountId()->getValue());
        $quantityUsedInOrders = $this->discountRepository->getQuantityUsedInOrders($query->getDiscountId());

        return new DiscountForEditing(
            $query->getDiscountId()->getValue(),
            $cartRule->name,
            $cartRule->priority,
            $cartRule->active,
            new DateTimeImmutable($cartRule->date_from),
            DateTimeUtil::buildDateTimeOrNull($cartRule->date_to),
            $cartRule->total_quantity,
            $cartRule->quantity,
            $quantityUsedInOrders,
            $cartRule->quantity_per_user,
            $cartRule->description,
            $cartRule->code,
            $cartRule->id_customer ?: null,
            $cartRule->highlight,
            $cartRule->partial_use,
            $cartRule->getType(),
            (float) $cartRule->reduction_percent > 0.00 ? new DecimalNumber($cartRule->reduction_percent) : null,
            (float) $cartRule->reduction_amount > 0.00 ? new DecimalNumber($cartRule->reduction_amount) : null,
            $cartRule->reduction_currency,
            $cartRule->reduction_tax,
            $cartRule->reduction_product === DiscountSettings::CHEAPEST_PRODUCT,
            $cartRule->reduction_product > 0 ? $cartRule->reduction_product : null,
            $cartRule->gift_product ?: null,
            $cartRule->gift_product_attribute ?: null,
            $cartRule->minimum_product_quantity,
            $productConditions,
            (float) $cartRule->minimum_amount > 0.00 ? new DecimalNumber($cartRule->minimum_amount) : null,
            $cartRule->minimum_amount_currency,
            $cartRule->minimum_amount_tax,
            $cartRule->minimum_amount_shipping,
            $carrierIds,
            $countryIds,
            $customerGroupIds,
            $compatibleDiscountTypeIds,
        );
    }
}
