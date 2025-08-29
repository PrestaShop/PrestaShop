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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
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
use PrestaShopBundle\Form\Admin\Sell\Discount\CartConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DeliveryConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountProductSegmentType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountUsabilityModeType;
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
        private readonly ShopContext $shopContext,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getDefaultData()
    {
        return [
            'usability' => [
                'mode' => [
                    'children_selector' => DiscountUsabilityModeType::AUTO_MODE,
                    'code' => '',
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
        $isAmountDiscount = $discountForEditing->getAmountDiscount() !== null;
        $details = $this->getGiftDetails($discountForEditing);
        $specificProducts = $this->getSpecificProducts($discountForEditing);
        $productSegment = $this->getProductSegmentDetails($discountForEditing);
        $productSegmentDefined =
            !empty($productSegment[DiscountProductSegmentType::MANUFACTURER])
            || !empty($productSegment[DiscountProductSegmentType::SUPPLIER])
            || !empty($productSegment[DiscountProductSegmentType::CATEGORY])
            || !empty($productSegment[DiscountProductSegmentType::ATTRIBUTES]['groups'])
        ;

        $selectedCondition = 'none';
        $selectedCartCondition = 'none';
        $selectedDeliveryCondition = 'none';
        if ($discountForEditing->getMinimumProductQuantity()) {
            $selectedCondition = DiscountConditionsType::CART_CONDITIONS;
            $selectedCartCondition = CartConditionsType::MINIMUM_PRODUCT_QUANTITY;
        } elseif ($discountForEditing->getMinimumAmount()) {
            $selectedCondition = DiscountConditionsType::CART_CONDITIONS;
            $selectedCartCondition = CartConditionsType::MINIMUM_AMOUNT;
        } elseif (!empty($specificProducts)) {
            $selectedCondition = DiscountConditionsType::CART_CONDITIONS;
            $selectedCartCondition = CartConditionsType::SPECIFIC_PRODUCTS;
        } elseif ($productSegmentDefined) {
            $selectedCondition = DiscountConditionsType::CART_CONDITIONS;
            $selectedCartCondition = CartConditionsType::PRODUCT_SEGMENT;
        } elseif (!empty($discountForEditing->getCarrierIds())) {
            $selectedCondition = DiscountConditionsType::DELIVERY_CONDITIONS;
            $selectedDeliveryCondition = DeliveryConditionsType::CARRIERS;
        } elseif (!empty($discountForEditing->getCountryIds())) {
            $selectedCondition = DiscountConditionsType::DELIVERY_CONDITIONS;
            $selectedDeliveryCondition = DeliveryConditionsType::COUNTRY;
        }

        return [
            'id' => $id,
            'information' => [
                'discount_type' => $discountForEditing->getType()->getValue(),
                'names' => $discountForEditing->getLocalizedNames(),
            ],
            'value' => [
                'reduction' => [
                    'type' => $isAmountDiscount ? DiscountSettings::AMOUNT : DiscountSettings::PERCENT,
                    'value' => $isAmountDiscount
                        ? (float) (string) $discountForEditing->getAmountDiscount()
                        : (float) (string) $discountForEditing->getPercentDiscount(),
                    'currency' => $discountForEditing->getCurrencyId(),
                    'include_tax' => $discountForEditing->isTaxIncluded(),
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
                'children_selector' => $selectedCondition,
                DiscountConditionsType::CART_CONDITIONS => [
                    'children_selector' => $selectedCartCondition,
                    'minimum_product_quantity' => $discountForEditing->getMinimumProductQuantity(),
                    'minimum_amount' => [
                        'value' => $discountForEditing->getMinimumAmount() ? (float) (string) $discountForEditing->getMinimumAmount() : null,
                        'currency' => $discountForEditing->getMinimumAmountCurrencyId(),
                        'include_tax' => $discountForEditing->getMinimumAmountTaxIncluded(),
                    ],
                    'specific_products' => $specificProducts,
                    CartConditionsType::PRODUCT_SEGMENT => $productSegment,
                ],
                DiscountConditionsType::DELIVERY_CONDITIONS => [
                    'children_selector' => $selectedDeliveryCondition,
                    DeliveryConditionsType::CARRIERS => $discountForEditing->getCarrierIds(),
                    DeliveryConditionsType::COUNTRY => $discountForEditing->getCountryIds(),
                ],
            ],
            'usability' => [
                'mode' => [
                    'children_selector' => $discountForEditing->getCode() ? DiscountUsabilityModeType::CODE_MODE : DiscountUsabilityModeType::AUTO_MODE,
                    'code' => $discountForEditing->getCode(),
                ],
            ],
        ];
    }

    private function getSpecificProducts(DiscountForEditing $discountForEditing): array
    {
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
            'quantity' => 0,
        ];

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
            }

            $productSegment['quantity'] = $condition->getQuantity();
        }

        return $productSegment;
    }
}
