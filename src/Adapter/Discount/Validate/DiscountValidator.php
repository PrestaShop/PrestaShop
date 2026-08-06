<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Discount\Validate;

use CartRule;
use Customer;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Trait\ProductConditionsTrait;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Adapter\Manufacturer\Repository\ManufacturerRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Supplier\Repository\SupplierRepository;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopException;

/**
 * This validator is used for the new Discount domain, but it still relies on the legacy CartRule ObjectModel.
 */
class DiscountValidator extends AbstractObjectModelValidator
{
    use ProductConditionsTrait;

    protected ?DiscountRepository $discountRepository = null;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CombinationRepository $combinationRepository,
        private readonly CarrierRepository $carrierRepository,
        private readonly ManufacturerRepository $manufacturerRepository,
        private readonly AttributeRepository $attributeRepository,
        private readonly SupplierRepository $supplierRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly CountryRepository $countryRepository,
        private readonly GroupRepository $groupRepository,
        private readonly FeatureValueRepository $featureValueRepository,
    ) {
    }

    /**
     * Repository is injected via a setter to avoid circular injection problems
     */
    public function setDiscountRepository(DiscountRepository $discountRepository): void
    {
        $this->discountRepository = $discountRepository;
    }

    public function validate(CartRule $cartRule): void
    {
        $this->validateCartRuleProperty($cartRule, 'id_customer', DiscountConstraintException::INVALID_CUSTOMER_ID);
        $this->assertCustomerIsNotGuest($cartRule);
        $this->validateCartRuleProperty($cartRule, 'date_from', DiscountConstraintException::INVALID_DATE_FROM);
        $this->validateCartRuleProperty($cartRule, 'date_to', DiscountConstraintException::INVALID_DATE_TO);
        $this->validateCartRuleProperty($cartRule, 'description', DiscountConstraintException::INVALID_DESCRIPTION);
        $this->validateCartRuleProperty($cartRule, 'quantity', DiscountConstraintException::INVALID_QUANTITY);
        $this->validateCartRuleProperty($cartRule, 'quantity_per_user', DiscountConstraintException::INVALID_QUANTITY_PER_USER);
        $this->validateCartRuleProperty($cartRule, 'priority', DiscountConstraintException::INVALID_PRIORITY);
        $this->validateCartRuleProperty($cartRule, 'partial_use', DiscountConstraintException::INVALID_PARTIAL_USE);
        $this->validateCartRuleProperty($cartRule, 'code', DiscountConstraintException::INVALID_CODE);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount', DiscountConstraintException::INVALID_MINIMUM_AMOUNT);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount_tax', DiscountConstraintException::INVALID_MINIMUM_AMOUNT_TAX);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount_currency', DiscountConstraintException::INVALID_MINIMUM_AMOUNT_CURRENCY);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount_shipping', DiscountConstraintException::INVALID_MINIMUM_AMOUNT_SHIPPING);
        $this->validateCartRuleProperty($cartRule, 'country_restriction', DiscountConstraintException::INVALID_COUNTRY_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'carrier_restriction', DiscountConstraintException::INVALID_CARRIER_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'group_restriction', DiscountConstraintException::INVALID_GROUP_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'cart_rule_restriction', DiscountConstraintException::INVALID_CART_RULE_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'product_restriction', DiscountConstraintException::INVALID_PRODUCT_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'shop_restriction', DiscountConstraintException::INVALID_SHOP_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'free_shipping', DiscountConstraintException::INVALID_FREE_SHIPPING);
        $this->validateCartRuleProperty($cartRule, 'reduction_percent', DiscountConstraintException::INVALID_REDUCTION_PERCENT);
        $this->validateCartRuleProperty($cartRule, 'reduction_amount', DiscountConstraintException::INVALID_REDUCTION_AMOUNT);
        $this->validateCartRuleProperty($cartRule, 'reduction_tax', DiscountConstraintException::INVALID_REDUCTION_TAX);
        $this->validateCartRuleProperty($cartRule, 'reduction_currency', DiscountConstraintException::INVALID_REDUCTION_CURRENCY);
        $this->validateCartRuleProperty($cartRule, 'reduction_product', DiscountConstraintException::INVALID_REDUCTION_PRODUCT);
        $this->validateCartRuleProperty($cartRule, 'reduction_exclude_special', DiscountConstraintException::INVALID_REDUCTION_EXCLUDE_SPECIAL);
        $this->validateCartRuleProperty($cartRule, 'gift_product', DiscountConstraintException::INVALID_GIFT_PRODUCT);
        $this->validateCartRuleProperty($cartRule, 'gift_product_attribute', DiscountConstraintException::INVALID_GIFT_PRODUCT_ATTRIBUTE);
        $this->validateCartRuleProperty($cartRule, 'highlight', DiscountConstraintException::INVALID_HIGHLIGHT);
        $this->validateCartRuleProperty($cartRule, 'active', DiscountConstraintException::INVALID_ACTIVE);

        $this->validateObjectModelLocalizedProperty(
            $cartRule,
            'name',
            DiscountConstraintException::class,
            DiscountConstraintException::INVALID_NAME
        );

        $this->assertCodeIsUnique($cartRule);
        $this->assertDateRangeIsCorrect($cartRule);
    }

    /**
     * @param ProductRuleGroup[]|null $productConditions
     * @param int[]|null $carrierIds
     * @param int[]|null $countryIds
     * @param int[]|null $customerGroupIds
     */
    public function validateAssociations(
        ?array $productConditions = null,
        ?array $carrierIds = null,
        ?array $countryIds = null,
        ?array $customerGroupIds = null,
    ): void {
        if (!empty($productConditions)) {
            // Check that selected items do exist
            foreach ($productConditions as $productRuleGroup) {
                foreach ($productRuleGroup->getRules() as $rule) {
                    foreach ($rule->getItemIds() as $itemId) {
                        match ($rule->getType()) {
                            ProductRuleType::PRODUCTS => $this->productRepository->assertProductExists(new ProductId($itemId)),
                            ProductRuleType::COMBINATIONS => $this->combinationRepository->assertCombinationExists(new CombinationId($itemId)),
                            ProductRuleType::MANUFACTURERS => $this->manufacturerRepository->assertManufacturerExists(new ManufacturerId($itemId)),
                            ProductRuleType::SUPPLIERS => $this->supplierRepository->assertSupplierExists(new SupplierId($itemId)),
                            ProductRuleType::ATTRIBUTES => $this->attributeRepository->assertAttributeExists(new AttributeId($itemId)),
                            ProductRuleType::FEATURES => $this->featureValueRepository->assertExists(new FeatureValueId($itemId)),
                            ProductRuleType::CATEGORIES => $this->categoryRepository->assertCategoryExists(new CategoryId($itemId)),
                        };
                    }
                }
            }
        }

        // Check carrier exist
        if (!empty($carrierIds)) {
            foreach ($carrierIds as $carrierId) {
                $this->carrierRepository->assertCarrierExists(new CarrierId($carrierId));
            }
        }

        // Check countries exist
        if (!empty($countryIds)) {
            foreach ($countryIds as $countryId) {
                $this->countryRepository->assertCountryExists(new CountryId($countryId));
            }
        }

        // Check customer group exist
        if (!empty($customerGroupIds)) {
            foreach ($customerGroupIds as $customerGroupId) {
                $this->groupRepository->assertGroupExists(new GroupId($customerGroupId));
            }
        }
    }

    /**
     * @throws DiscountConstraintException
     */
    public function validateDiscountPropertiesForType(CartRule $discount, ?array $productConditions): void
    {
        $discountType = $discount->getType();
        $hasReductionAmount = !empty($discount->reduction_amount) && !(new DecimalNumber((string) $discount->reduction_amount))->equalsZero() && ((int) $discount->reduction_currency !== 0);
        $hasReductionPercent = !empty($discount->reduction_percent) && !(new DecimalNumber((string) $discount->reduction_percent))->equalsZero();

        switch ($discountType) {
            case DiscountType::FREE_SHIPPING:
                break;
            case DiscountType::CART_LEVEL:
            case DiscountType::ORDER_LEVEL:
                if ($hasReductionAmount && $hasReductionPercent) {
                    throw new DiscountConstraintException('Discount can not be amount and percent at the same time', DiscountConstraintException::INVALID_DISCOUNT_CANNOT_BE_AMOUNT_AND_PERCENT);
                }
                if ($hasReductionAmount) {
                    if ($discount->reduction_amount < 0) {
                        throw new DiscountConstraintException('Discount value can not be negative', DiscountConstraintException::INVALID_DISCOUNT_VALUE_CANNOT_BE_NEGATIVE);
                    }
                }
                if ($hasReductionPercent) {
                    if ($discount->reduction_percent < 0 || $discount->reduction_percent > 100) {
                        throw new DiscountConstraintException('Discount value can not be negative or above 100', DiscountConstraintException::INVALID_DISCOUNT_VALUE_CANNOT_BE_NEGATIVE);
                    }
                }
                break;
            case DiscountType::PRODUCT_LEVEL:
                $segmentTargeted = false;
                $discountCheapestProduct = $discount->reduction_product === DiscountSettings::CHEAPEST_PRODUCT;
                $discountSingleProduct = $discount->reduction_product > 0;
                $targetNumber = 0;
                if ($discountCheapestProduct) {
                    ++$targetNumber;
                }
                if ($discountSingleProduct) {
                    ++$targetNumber;
                }

                // During update (so only for CartRule with existing ID)) When no value is updated, we use the current product rules or we would end up with
                // an error that cart rule has no target
                if (!$discountCheapestProduct && !$discountSingleProduct && null === $productConditions && $discount->id) {
                    $productConditions = $this->discountRepository->getProductRulesGroup(new DiscountId((int) $discount->id));
                }
                if (!empty($productConditions)) {
                    $segmentTargeted = $this->isSegmentTargeted($productConditions);
                }
                if ($segmentTargeted) {
                    ++$targetNumber;
                }

                // At least one target must be selected, but not more
                if ($targetNumber === 0) {
                    throw new DiscountConstraintException('Product discount must target at least one product.', DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_MISSING_TARGET);
                }
                if ($targetNumber > 1) {
                    throw new DiscountConstraintException('You need to choose only one target, cheapest, single product or product segment.', DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_INCOMPATIBLE_TARGETS);
                }

                // Either amount or percent discount must be defined, but never both
                if (!$hasReductionAmount && !$hasReductionPercent) {
                    throw new DiscountConstraintException('Product discount must have a discount value (amount or percent).', DiscountConstraintException::INVALID_MISSING_REDUCTION);
                }
                if ($hasReductionAmount && $hasReductionPercent) {
                    throw new DiscountConstraintException('You need to choose between reduction amount or percent.', DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_INCOMPATIBLE_REDUCTIONS);
                }

                break;
            case DiscountType::FREE_GIFT:
                if (empty($discount->gift_product)) {
                    throw new DiscountConstraintException('Free gift discount must have his properties set.', DiscountConstraintException::INVALID_FREE_GIFT_DISCOUNT_PROPERTIES);
                }

                $product = $this->productRepository->getByShopConstraint(new ProductId((int) $discount->gift_product), ShopConstraint::allShops());
                if ((int) $product->customizable === ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION) {
                    throw new DiscountConstraintException('Product with required customization fields cannot be used as a gift.', DiscountConstraintException::INVALID_GIFT_PRODUCT);
                }
                if ((int) $product->minimal_quantity > 1) {
                    throw new DiscountConstraintException('Gift product has a minimum quantity greater than 1.', DiscountConstraintException::INVALID_GIFT_PRODUCT_MINIMUM_QUANTITY);
                }
                break;
            default:
                throw new DiscountConstraintException(sprintf("Invalid discount type '%s'.", $discountType), DiscountConstraintException::INVALID_DISCOUNT_TYPE);
        }
    }

    private function validateCartRuleProperty(CartRule $cartRule, string $propertyName, int $code): void
    {
        $this->validateObjectModelProperty(
            $cartRule,
            $propertyName,
            DiscountConstraintException::class,
            $code
        );
    }

    private function assertCodeIsUnique(CartRule $cartRule): void
    {
        // To avoid circular dependency, we need to set the repository with setDiscountRepository.
        // So, we need to check if discountRepository property is set before use this function!
        if ($this->discountRepository === null) {
            throw new CoreException('Discount repository is mandatory to check discount code uniquicity.');
        }

        $code = $cartRule->code;

        if (empty($code)) {
            return;
        }

        try {
            $duplicateCodeCartRuleId = $this->discountRepository->getIdByCode($code);
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when trying to check if discount code is unique', 0, $e);
        }

        if ($duplicateCodeCartRuleId && $duplicateCodeCartRuleId !== (int) $cartRule->id) {
            throw new DiscountConstraintException(
                sprintf('This discount code "%s" is already used (conflict with discount %s)', $code, $duplicateCodeCartRuleId),
                DiscountConstraintException::NON_UNIQUE_CODE
            );
        }
    }

    private function assertDateRangeIsCorrect(CartRule $cartrule): void
    {
        if (DateTimeUtil::isNull($cartrule->date_to)) {
            return;
        }

        if ($cartrule->date_from > $cartrule->date_to) {
            throw new DiscountConstraintException('Date from cannot be greater than date to.', DiscountConstraintException::DATE_FROM_GREATER_THAN_DATE_TO);
        }
    }

    private function assertCustomerIsNotGuest(CartRule $cartRule): void
    {
        if (empty($cartRule->id_customer)) {
            return;
        }

        $customer = new Customer((int) $cartRule->id_customer);
        if ($customer->isGuest()) {
            throw new DiscountConstraintException(
                sprintf('Cannot assign discount to guest customer with ID %d', $cartRule->id_customer),
                DiscountConstraintException::INVALID_GUEST_CUSTOMER
            );
        }
    }
}
