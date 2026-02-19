<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use DateTime;
use DateTimeInterface;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleType;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Provider\ProductImageProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilder;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopBundle\Form\Admin\Sell\Discount\CartConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DeliveryConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountCustomerEligibilityChoiceType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountProductSegmentType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountUsabilityModeType;
use PrestaShopBundle\Form\Admin\Sell\Discount\ProductConditionsType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class DiscountFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly ProductRepository $productRepository,
        private readonly CombinationRepository $combinationRepository,
        private readonly CombinationNameBuilder $combinationNameBuilder,
        private readonly ProductImageProviderInterface $productImageProvider,
        private readonly LanguageContext $languageContext,
        private readonly AttributeRepository $attributeRepository,
        private readonly FeatureValueRepository $featureValueRepository,
        private readonly ShopContext $shopContext,
        private readonly RequestStack $requestStack,
        private readonly CustomerRepository $customerRepository,
    ) {
    }

    public function getDefaultData()
    {
        $now = new DateTime();
        $startDate = (clone $now)->setTime(0, 0);
        $endDate = (clone $now)->modify('+1 month')->setTime(23, 59);

        return [
            'period' => [
                'valid_date_range' => [
                    'from' => $startDate->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                    'to' => $endDate->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                ],
                'period_never_expires' => false,
            ],
            'customer_eligibility' => [
                'eligibility' => [
                    'children_selector' => DiscountCustomerEligibilityChoiceType::ALL_CUSTOMERS,
                    DiscountCustomerEligibilityChoiceType::CUSTOMER_GROUPS => [],
                    DiscountCustomerEligibilityChoiceType::SINGLE_CUSTOMER => [],
                ],
            ],
            'usability' => [
                'mode' => [
                    'children_selector' => DiscountUsabilityModeType::AUTO_MODE,
                    'code' => '',
                ],
                'quantity_total' => null,
                'quantity_per_customer' => null,
                'compatibility' => [],
                'priority' => 1,
            ],
            'conditions' => [
                DiscountConditionsType::PRODUCT_CONDITIONS => [
                    'children_selector' => ProductConditionsType::NONE,
                ],
                DiscountConditionsType::CART_CONDITIONS => [
                    'children_selector' => ProductConditionsType::NONE,
                ],
                DiscountConditionsType::DELIVERY_CONDITIONS => [
                    'children_selector' => ProductConditionsType::NONE,
                ],
            ],
        ];
    }

    /**
     * @throws ShopException
     * @throws ProductNotFoundException
     * @throws ProductConstraintException
     * @throws CombinationConstraintException
     */
    public function getData($id)
    {
        /** @var DiscountForEditing $discountForEditing */
        $discountForEditing = $this->queryBus->handle(new GetDiscountForEditing($id));
        $isAmountDiscount = $discountForEditing->getReductionAmount() !== null;
        $details = $this->getGiftDetails($discountForEditing);
        $specificProducts = $this->getSpecificProducts($discountForEditing);
        $productSegment = $this->getProductSegmentDetails($discountForEditing);
        $productSegmentDefined =
            !empty($productSegment[DiscountProductSegmentType::MANUFACTURER])
            || !empty($productSegment[DiscountProductSegmentType::SUPPLIER])
            || !empty($productSegment[DiscountProductSegmentType::CATEGORY])
            || !empty($productSegment[DiscountProductSegmentType::ATTRIBUTES]['groups'])
            || !empty($productSegment[DiscountProductSegmentType::FEATURES]['groups'])
        ;

        $selectedProductCondition = ProductConditionsType::NONE;
        $selectedCartCondition = CartConditionsType::NONE;
        $selectedDeliveryCondition = DeliveryConditionsType::NONE;

        if (!empty($specificProducts)) {
            $selectedProductCondition = ProductConditionsType::SPECIFIC_PRODUCTS;
        } elseif ($productSegmentDefined) {
            $selectedProductCondition = ProductConditionsType::PRODUCT_SEGMENT;
        }
        // Cheapest product condition has been decided not relevant
        /*elseif ($discountForEditing->getCheapestProduct()) {
            $selectedProductCondition = ProductConditionsType::CHEAPEST_PRODUCT;
        }*/

        if ($discountForEditing->getMinimumProductQuantity()) {
            $selectedCartCondition = CartConditionsType::MINIMUM_PRODUCT_QUANTITY;
        } elseif ($discountForEditing->getMinimumAmount()) {
            $selectedCartCondition = CartConditionsType::MINIMUM_AMOUNT;
        }

        if (!empty($discountForEditing->getCarrierIds())) {
            $selectedDeliveryCondition = DeliveryConditionsType::CARRIERS;
        } elseif (!empty($discountForEditing->getCountryIds())) {
            $selectedDeliveryCondition = DeliveryConditionsType::COUNTRY;
        }

        return [
            'id' => $id,
            'information' => [
                'discount_type' => $discountForEditing->getType()->getValue(),
                'names' => $discountForEditing->getLocalizedNames(),
                'description' => $discountForEditing->getDescription(),
            ],
            'value' => [
                'reduction' => [
                    'type' => $isAmountDiscount ? DiscountSettings::AMOUNT : DiscountSettings::PERCENT,
                    'value' => $isAmountDiscount
                        ? (float) (string) $discountForEditing->getReductionAmount()->getAmount()
                        : (float) (string) $discountForEditing->getReductionPercent(),
                    'currency' => $discountForEditing->getReductionAmount()?->getCurrencyId(),
                    'include_tax' => $discountForEditing->getReductionAmount()?->isTaxIncluded(),
                ],
            ],
            'free_gift' => [
                [
                    'product_id' => $discountForEditing->getGiftProductId(),
                    'combination_id' => $discountForEditing->getGiftCombinationId(),
                    'name' => $details['name'],
                    'image' => $details['imageUrl'],
                ],
            ],
            'conditions' => [
                DiscountConditionsType::PRODUCT_CONDITIONS => [
                    'children_selector' => $selectedProductCondition,
                    'specific_products' => $specificProducts,
                    ProductConditionsType::PRODUCT_SEGMENT => $productSegment,
                ],
                DiscountConditionsType::CART_CONDITIONS => [
                    'children_selector' => $selectedCartCondition,
                    'minimum_product_quantity' => $discountForEditing->getMinimumProductQuantity(),
                    'minimum_amount' => [
                        'value' => $discountForEditing->getMinimumAmount() ? (float) (string) $discountForEditing->getMinimumAmount()->getAmount() : null,
                        'currency' => $discountForEditing->getMinimumAmount()?->getCurrencyId(),
                        'tax_included' => $discountForEditing->getMinimumAmount()?->isTaxIncluded(),
                        'shipping_included' => $discountForEditing->getMinimumAmount()?->isShippingIncluded(),
                    ],
                ],
                DiscountConditionsType::DELIVERY_CONDITIONS => [
                    'children_selector' => $selectedDeliveryCondition,
                    DeliveryConditionsType::CARRIERS => $discountForEditing->getCarrierIds(),
                    DeliveryConditionsType::COUNTRY => $discountForEditing->getCountryIds(),
                ],
            ],
            'period' => [
                'valid_date_range' => [
                    'from' => $discountForEditing->getValidFrom() ? $discountForEditing->getValidFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT) : null,
                    'to' => $discountForEditing->getValidTo() ? $discountForEditing->getValidTo()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT) : null,
                ],
                'period_never_expires' => $this->isPeriodNeverExpires($discountForEditing->getValidFrom(), $discountForEditing->getValidTo()),
            ],
            'customer_eligibility' => [
                'eligibility' => $this->getCustomerEligibilityData($discountForEditing),
            ],
            'usability' => [
                'mode' => [
                    'children_selector' => $discountForEditing->getCode() ? DiscountUsabilityModeType::CODE_MODE : DiscountUsabilityModeType::AUTO_MODE,
                    'code' => $discountForEditing->getCode(),
                ],
                'quantity_total' => $discountForEditing->getTotalQuantity(),
                'quantity_per_customer' => $discountForEditing->getQuantityPerUser(),
                'compatibility' => $discountForEditing->getCompatibleDiscountTypeIds(),
                'priority' => $discountForEditing->getPriority(),
            ],
        ];
    }

    private function getSpecificProducts(DiscountForEditing $discountForEditing): array
    {
        if (empty($discountForEditing->getProductConditions())) {
            return [];
        }

        $specificProducts = [];
        foreach ($discountForEditing->getProductConditions() as $conditions) {
            foreach ($conditions->getRules() as $rule) {
                if ($rule->getType() == ProductRuleType::PRODUCTS) {
                    // The data is not formatted as expected and would break the page (it may happen with data from old page),
                    // to be resilient against this kind of data so we ignore it. But it means some data is going be lost so
                    // we warn the user
                    if (count($rule->getItemIds()) === 0) {
                        $this->displayWarning('Invalid specific product has been removed from form data, it will be erased if you submit this form.');
                        continue;
                    }

                    $productId = new ProductId($rule->getItemIds()[0]);
                    $productDefaultShopId = $this->productRepository->getProductDefaultShopId($productId);
                    $product = $this->productRepository->get($productId, $productDefaultShopId);
                    $combinationIdValue = NoCombinationId::NO_COMBINATION_ID;
                    $imageUrl = $this->productImageProvider->getProductCoverUrl($productId, $productDefaultShopId);
                } elseif ($rule->getType() == ProductRuleType::COMBINATIONS) {
                    // The data is not formatted as expected and would break the page (it may happen with data from old page),
                    // to be resilient against this kind of data so we ignore it. But it means some data is going be lost so
                    // we warn the user
                    if (count($rule->getItemIds()) === 0) {
                        $this->displayWarning('Invalid specific combination has been removed from form data, it will be erased if you submit this form.');
                        continue;
                    }

                    $combinationIdValue = $rule->getItemIds()[0];
                    $combinationId = new CombinationId($combinationIdValue);
                    $productId = $this->combinationRepository->getProductId($combinationId);
                    $productDefaultShopId = $this->productRepository->getProductDefaultShopId($productId);
                    $product = $this->productRepository->get($productId, $productDefaultShopId);
                    $imageUrl = $this->productImageProvider->getCombinationCoverUrl($combinationId, $productDefaultShopId);
                } else {
                    continue;
                }

                $productName = $product->name[$this->languageContext->getId()];
                if (!empty($product->reference)) {
                    $productName .= sprintf(' (ref: %s)', $product->reference);
                }

                $specificProducts[] = [
                    'id' => $product->id,
                    'combination_id' => $combinationIdValue,
                    'product_type' => $product->product_type,
                    'name' => $productName,
                    'image' => $imageUrl,
                    'quantity' => $conditions->getQuantity(),
                ];
            }
        }

        return $specificProducts;
    }

    /**
     * @throws ShopAssociationNotFound
     * @throws ShopException
     * @throws ProductConstraintException
     * @throws ProductNotFoundException
     * @throws CombinationConstraintException
     */
    private function getGiftDetails(DiscountForEditing $discountForEditing): array
    {
        $name = '';
        $imageUrl = '';
        if (!empty($discountForEditing->getGiftProductId())) {
            $product = $this->productRepository->getProductByDefaultShop(new ProductId($discountForEditing->getGiftProductId()));
            $name = $product->name[$this->languageContext->getId()];

            if (!empty($discountForEditing->getGiftCombinationId())) {
                $attributesInformations = $this->attributeRepository->getAttributesInfoByCombinationIds(
                    [new CombinationId($discountForEditing->getGiftCombinationId())],
                    new LanguageId($this->languageContext->getId())
                );

                $name = $this->combinationNameBuilder->buildFullName(
                    $name,
                    $attributesInformations[$discountForEditing->getGiftCombinationId()]
                );
                $imageUrl = $this->productImageProvider->getCombinationCoverUrl(
                    new CombinationId($discountForEditing->getGiftCombinationId()),
                    new ShopId($this->shopContext->getId())
                );
            } else {
                $imageUrl = $this->productImageProvider->getProductCoverUrl(
                    new ProductId($discountForEditing->getGiftProductId()),
                    new ShopId($this->shopContext->getId())
                );
            }
        }

        return [
            'name' => $name,
            'imageUrl' => $imageUrl,
        ];
    }

    private function displayWarning(string $message): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('warning', $message);
        }
    }

    private function getProductSegmentDetails(DiscountForEditing $discountForEditing): array
    {
        $productSegment = [
            DiscountProductSegmentType::MANUFACTURER => 0,
            DiscountProductSegmentType::CATEGORY => '',
            DiscountProductSegmentType::SUPPLIER => 0,
            DiscountProductSegmentType::ATTRIBUTES => [
                'groups' => [],
            ],
            DiscountProductSegmentType::FEATURES => [
                'groups' => [],
            ],
            'quantity' => 0,
        ];

        if (empty($discountForEditing->getProductConditions())) {
            return $productSegment;
        }

        // We can loop through all the rule groups but there should be only one anyway
        foreach ($discountForEditing->getProductConditions() as $condition) {
            foreach ($condition->getRules() as $rule) {
                if ($rule->getType() === ProductRuleType::MANUFACTURERS) {
                    foreach ($rule->getItemIds() as $manufacturerId) {
                        $productSegment[DiscountProductSegmentType::MANUFACTURER] = $manufacturerId;
                    }
                }
                if ($rule->getType() === ProductRuleType::CATEGORIES) {
                    $productSegment[DiscountProductSegmentType::CATEGORY] = $rule->getItemIds()[0];
                }
                if ($rule->getType() === ProductRuleType::SUPPLIERS) {
                    foreach ($rule->getItemIds() as $supplierId) {
                        $productSegment[DiscountProductSegmentType::SUPPLIER] = $supplierId;
                    }
                }
                if ($rule->getType() === ProductRuleType::ATTRIBUTES) {
                    $attributesInfo = $this->attributeRepository->getAttributesInfoByAttributeIds($rule->getItemIds(), $this->languageContext->getId());
                    foreach ($rule->getItemIds() as $attributeId) {
                        $attributeInfo = $attributesInfo[$attributeId];
                        $groupId = $attributeInfo['id_attribute_group'];
                        if (empty($productSegment[DiscountProductSegmentType::ATTRIBUTES]['groups'][$groupId])) {
                            $productSegment[DiscountProductSegmentType::ATTRIBUTES]['groups'][$groupId] = [
                                'id' => $groupId,
                                'name' => $attributeInfo['attribute_group_name'],
                                'items' => [],
                            ];
                        }

                        $productSegment[DiscountProductSegmentType::ATTRIBUTES]['groups'][$groupId]['items'][] = [
                            'id' => $attributeId,
                            'name' => $attributeInfo['attribute_name'],
                        ];
                    }
                }
                if ($rule->getType() === ProductRuleType::FEATURES) {
                    $featuresInfo = $this->featureValueRepository->getFeaturesInfoByFeatureValueIds($rule->getItemIds(), $this->languageContext->getId());
                    foreach ($rule->getItemIds() as $featureValueId) {
                        $featureInfo = $featuresInfo[$featureValueId];
                        $featureId = $featureInfo['id_feature'];
                        if (empty($productSegment[DiscountProductSegmentType::FEATURES]['groups'][$featureId])) {
                            $productSegment[DiscountProductSegmentType::FEATURES]['groups'][$featureId] = [
                                'id' => $featureId,
                                'name' => $featureInfo['feature_name'],
                                'items' => [],
                            ];
                        }

                        $productSegment[DiscountProductSegmentType::FEATURES]['groups'][$featureId]['items'][] = [
                            'id' => $featureValueId,
                            'name' => $featureInfo['feature_value_name'],
                        ];
                    }
                }
            }

            $productSegment['quantity'] = $condition->getQuantity();
        }

        return $productSegment;
    }

    private function getCustomerEligibilityData(DiscountForEditing $discountForEditing): array
    {
        $customerId = $discountForEditing->getCustomerId();
        $customerGroupIds = $discountForEditing->getCustomerGroupIds();

        $data = [
            'children_selector' => DiscountCustomerEligibilityChoiceType::ALL_CUSTOMERS,
            DiscountCustomerEligibilityChoiceType::CUSTOMER_GROUPS => [],
            DiscountCustomerEligibilityChoiceType::SINGLE_CUSTOMER => [],
        ];

        if (!empty($customerGroupIds)) {
            $data['children_selector'] = DiscountCustomerEligibilityChoiceType::CUSTOMER_GROUPS;
            $data[DiscountCustomerEligibilityChoiceType::CUSTOMER_GROUPS] = $customerGroupIds;
        } elseif ($customerId) {
            try {
                $customer = $this->customerRepository->get(new CustomerId($customerId));
                $fullnameAndEmail = sprintf(
                    '%s %s - %s',
                    $customer->firstname,
                    $customer->lastname,
                    $customer->email
                );

                $data['children_selector'] = DiscountCustomerEligibilityChoiceType::SINGLE_CUSTOMER;
                $data[DiscountCustomerEligibilityChoiceType::SINGLE_CUSTOMER] = [
                    [
                        'id_customer' => $customerId,
                        'fullname_and_email' => $fullnameAndEmail,
                    ],
                ];
            } catch (CustomerNotFoundException $e) {
            }
        }

        return $data;
    }

    /**
     * Check if the discount period is set to "never expires" (>= 100 years duration).
     */
    private function isPeriodNeverExpires(?DateTimeInterface $validFrom, ?DateTimeInterface $validTo): bool
    {
        if ($validFrom === null || $validTo === null) {
            return false;
        }

        $diff = $validFrom->diff($validTo);
        $years = $diff->y + ($diff->m / 12) + ($diff->d / 365);

        return $years >= 100;
    }
}
