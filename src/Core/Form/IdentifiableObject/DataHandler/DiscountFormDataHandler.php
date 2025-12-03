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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use DateTime;
use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountConditionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRule;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroupType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShopBundle\Form\Admin\Sell\Discount\CartConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DeliveryConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountCustomerEligibilityChoiceType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountUsabilityModeType;
use PrestaShopBundle\Form\Admin\Sell\Discount\ProductConditionsType;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        #[Autowire(service: 'prestashop.default.language.context')]
        protected readonly LanguageContext $defaultLanguageContext,
        protected readonly TranslatorInterface $translator,
        protected readonly DiscountTypeRepository $discountTypeRepository,
    ) {
    }

    /**
     * @throws DiscountConstraintException
     * @throws DomainConstraintException
     * @throws CurrencyException
     */
    public function create(array $data)
    {
        // For the moment the names are not sent by the form so we continue to generate it as we did later in the method.
        $discountType = $data['information']['discount_type'];
        $command = new AddDiscountCommand($discountType, $data['information']['names'] ?? []);
        switch ($discountType) {
            case DiscountType::FREE_SHIPPING:
                break;
            case DiscountType::CART_LEVEL:
            case DiscountType::ORDER_LEVEL:
                if ($data['value']['reduction']['type'] === DiscountSettings::AMOUNT) {
                    $command->setAmountDiscount(
                        new DecimalNumber((string) $data['value']['reduction']['value']),
                        (int) $data['value']['reduction']['currency'],
                        (bool) $data['value']['reduction']['include_tax']
                    );
                } elseif ($data['value']['reduction']['type'] === DiscountSettings::PERCENT) {
                    $command->setPercentDiscount(new DecimalNumber((string) $data['value']['reduction']['value']));
                } else {
                    throw new RuntimeException('Unknown discount value type ' . $data['value']['reduction']['type']);
                }
                break;
            case DiscountType::PRODUCT_LEVEL:
                if (!isset($data['value']['reduction']['type'])) {
                    throw new DiscountConstraintException(
                        'Discount value is required for catalog products discount.',
                        DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_PROPERTIES
                    );
                }

                if ($data['value']['reduction']['type'] === DiscountSettings::AMOUNT) {
                    $command->setAmountDiscount(
                        new DecimalNumber((string) $data['value']['reduction']['value']),
                        (int) $data['value']['reduction']['currency'],
                        (bool) $data['value']['reduction']['include_tax']
                    );
                } elseif ($data['value']['reduction']['type'] === DiscountSettings::PERCENT) {
                    $command->setPercentDiscount(new DecimalNumber((string) $data['value']['reduction']['value']));
                } else {
                    throw new RuntimeException('Unknown discount value type ' . $data['value']['reduction']['type']);
                }

                // Read selected product from Product Conditions → Cart Conditions → Specific Products
                $reductionProduct = -2; // Default: use product conditions (selection of products)
                if (!empty($data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['specific_products'])) {
                    $specificProducts = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['specific_products'];
                    if (count($specificProducts) === 1 && isset($specificProducts[0]['id'])) {
                        // Single specific product selected
                        $reductionProduct = (int) $specificProducts[0]['id'];
                    }
                    // If multiple products selected, keep -2 (selection of products)
                }
                $command->setReductionProduct($reductionProduct);
                break;
            case DiscountType::FREE_GIFT:
                $command->setProductId((int) ($data['free_gift'][0]['product_id'] ?? 0));
                $command->setCombinationId((int) ($data['free_gift'][0]['combination_id'] ?? 0));
                break;
            default:
                throw new RuntimeException('Unknown discount type ' . $discountType);
        }

        $command->setActive(true);

        $command->setDescription($data['information']['description'] ?? '');

        if ($data['usability']['mode']['children_selector'] === DiscountUsabilityModeType::CODE_MODE) {
            $command->setCode($data['usability']['mode']['code'] ?? '');
        } else {
            $command->setCode('');
        }

        if (!empty($data['period']['valid_date_range'])) {
            $dateRange = $data['period']['valid_date_range'];
            $validFrom = $this->parseDateWithDefaultTime($dateRange['from'] ?? null, '00:00');

            $neverExpires = !empty($data['period']['period_never_expires']);
            if ($neverExpires) {
                $validTo = (new DateTime())->modify('+100 years')->setTime(23, 59, 59);
                $validTo = DateTimeImmutable::createFromMutable($validTo);
            } else {
                $validTo = $this->parseDateWithDefaultTime($dateRange['to'] ?? null, '23:59');
            }

            if ($validFrom && $validTo) {
                $command->setValidityDateRange($validFrom, $validTo);
            }
        }

        $this->handleCustomerEligibility($command, $data);
        $command->setTotalQuantity(100);

        if (isset($data['usability']['priority']) && $data['usability']['priority'] > 0) {
            $command->setPriority((int) $data['usability']['priority']);
        }

        /** @var DiscountId $discountId */
        $discountId = $this->commandBus->handle($command);
        $this->updateDiscountConditions($discountId->getValue(), $data);
        $this->updateDiscountCompatibility($discountId->getValue(), $data);

        return $discountId->getValue();
    }

    /**
     * @throws DomainConstraintException
     * @throws DiscountConstraintException
     * @throws CurrencyException
     */
    public function update($id, array $data): void
    {
        $command = new UpdateDiscountCommand($id);
        $discountType = $data['information']['discount_type'];
        switch ($discountType) {
            case DiscountType::FREE_SHIPPING:
            case DiscountType::CART_LEVEL:
            case DiscountType::ORDER_LEVEL:
                if ($data['value']['reduction']['type'] === DiscountSettings::AMOUNT) {
                    $command->setAmountDiscount(
                        new DecimalNumber((string) $data['value']['reduction']['value']),
                        $data['value']['reduction']['currency'],
                        (bool) $data['value']['reduction']['include_tax']
                    );
                } elseif ($data['value']['reduction']['type'] === DiscountSettings::PERCENT) {
                    $command->setPercentDiscount(new DecimalNumber((string) $data['value']['reduction']['value']));
                } else {
                    throw new RuntimeException('Unknown discount value type ' . $data['value']['reduction']['type']);
                }
                break;
            case DiscountType::PRODUCT_LEVEL:
                if (!isset($data['value']['reduction']['type'])) {
                    throw new DiscountConstraintException(
                        'Discount value is required for catalog products discount.',
                        DiscountConstraintException::INVALID_PRODUCT_DISCOUNT_PROPERTIES
                    );
                }

                if ($data['value']['reduction']['type'] === DiscountSettings::AMOUNT) {
                    $command->setAmountDiscount(
                        new DecimalNumber((string) $data['value']['reduction']['value']),
                        $data['value']['reduction']['currency'],
                        (bool) $data['value']['reduction']['include_tax']
                    );
                } elseif ($data['value']['reduction']['type'] === DiscountSettings::PERCENT) {
                    $command->setPercentDiscount(new DecimalNumber((string) $data['value']['reduction']['value']));
                } else {
                    throw new RuntimeException('Unknown discount value type ' . $data['value']['reduction']['type']);
                }

                // Read selected product from Product Conditions → Cart Conditions → Specific Products
                $reductionProduct = -2; // Default: use product conditions (selection of products)
                if (!empty($data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['specific_products'])) {
                    $specificProducts = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['specific_products'];
                    if (count($specificProducts) === 1 && isset($specificProducts[0]['id'])) {
                        // Single specific product selected
                        $reductionProduct = (int) $specificProducts[0]['id'];
                    }
                    // If multiple products selected, keep -2 (selection of products)
                }
                $command->setReductionProduct($reductionProduct);
                break;
            case DiscountType::FREE_GIFT:
                $command->setProductId((int) ($data['free_gift'][0]['product_id'] ?? 0));
                $command->setCombinationId((int) ($data['free_gift'][0]['combination_id'] ?? 0));
                break;
            default:
                throw new RuntimeException('Unknown discount type ' . $discountType);
        }
        $command->setLocalizedNames($data['information']['names']);
        $command->setDescription($data['information']['description'] ?? '');

        if ($data['usability']['mode']['children_selector'] === DiscountUsabilityModeType::CODE_MODE) {
            $command->setCode($data['usability']['mode']['code'] ?? '');
        } else {
            $command->setCode('');
        }

        if (!empty($data['period']['valid_date_range'])) {
            $dateRange = $data['period']['valid_date_range'];
            $validFrom = $this->parseDateWithDefaultTime($dateRange['from'] ?? null, '00:00');

            // Check if "never expires" checkbox is checked
            $neverExpires = !empty($data['period']['period_never_expires']);
            if ($neverExpires) {
                // Set expiration date to 100 years in the future
                $validTo = (new DateTime())->modify('+100 years')->setTime(23, 59, 59);
                $validTo = DateTimeImmutable::createFromMutable($validTo);
            } else {
                $validTo = $this->parseDateWithDefaultTime($dateRange['to'] ?? null, '23:59');
            }

            if ($validFrom && $validTo) {
                $command->setValidityDateRange($validFrom, $validTo);
            }
        }

        $this->handleCustomerEligibility($command, $data);

        if (isset($data['usability']['priority']) && $data['usability']['priority'] > 0) {
            $command->setPriority((int) $data['usability']['priority']);
        }

        $this->commandBus->handle($command);
        $this->updateDiscountConditions($id, $data);
        $this->updateDiscountCompatibility($id, $data);
    }

    private function updateDiscountConditions(int $discountId, array $data): void
    {
        $conditionsCommand = new UpdateDiscountConditionsCommand($discountId);

        // If no setter is called and the UpdateDiscountConditionsCommand is left empty, this will result in removing all
        // the conditions, that's because DiscountConditionsUpdater::update starts by removing/resetting all the conditions
        // and then apply new ones Since there are no conditions specified it is equivalent to removing all
        // It works for now, but it may cause unstability or unexpected behaviour, hence:
        // todo: we should force UpdateDiscountConditionsCommand to have at least one condition, alternatively we'll need
        //       a ClearDiscountConditionsCommand to clean everything on purpose

        // Products conditions
        if ($data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['children_selector'] === ProductConditionsType::SPECIFIC_PRODUCTS) {
            $specificProducts = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['specific_products'] ?? [];
            $productRuleGroups = [];

            foreach ($specificProducts as $specificProduct) {
                if (!empty($specificProduct['combination_id']) && $specificProduct['combination_id'] !== NoCombinationId::NO_COMBINATION_ID) {
                    $productRuleGroups[] = new ProductRuleGroup(
                        $specificProduct['quantity'],
                        [
                            new ProductRule(ProductRuleType::COMBINATIONS, [(int) $specificProduct['combination_id']]),
                        ]
                    );
                } else {
                    $productRuleGroups[] = new ProductRuleGroup(
                        $specificProduct['quantity'],
                        [
                            new ProductRule(ProductRuleType::PRODUCTS, [(int) $specificProduct['id']]),
                        ]
                    );
                }
            }

            $conditionsCommand->setProductConditions($productRuleGroups);
        } elseif ($data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['children_selector'] === ProductConditionsType::PRODUCT_SEGMENT) {
            $manufacturer = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['product_segment']['manufacturer'] ?? [];
            $category = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['product_segment']['category'] ?? '';
            $supplier = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['product_segment']['supplier'] ?? [];
            $attributes = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['product_segment']['attributes']['groups'] ?? [];
            $features = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['product_segment']['features']['groups'] ?? [];

            $productRules = [];
            $productRuleGroups = [];
            if (!empty($manufacturer)) {
                $productRules[] = new ProductRule(ProductRuleType::MANUFACTURERS, [(int) $manufacturer]);
            }
            if (!empty($category)) {
                $productRules[] = new ProductRule(ProductRuleType::CATEGORIES, [(int) $category]);
            }
            if (!empty($supplier)) {
                $productRules[] = new ProductRule(ProductRuleType::SUPPLIERS, [(int) $supplier]);
            }
            if (!empty($attributes)) {
                // We create a ProductRule for each attribute group, thus building more and more restrictive conditions
                // The values of each product rule is a range of possibility though
                foreach ($attributes as $attributesByGroup) {
                    $productRules[] = new ProductRule(
                        ProductRuleType::ATTRIBUTES,
                        array_map(fn (array $attribute) => (int) $attribute['id'], $attributesByGroup['items']),
                    );
                }
            }
            if (!empty($features)) {
                // We create a ProductRule for each feature group, similar to attributes
                foreach ($features as $featuresByGroup) {
                    $productRules[] = new ProductRule(
                        ProductRuleType::FEATURES,
                        array_map(fn (array $feature) => (int) $feature['id'], $featuresByGroup['items']),
                    );
                }
            }

            if (!empty($productRules)) {
                $conditionsCommand->setProductConditions([
                    new ProductRuleGroup(
                        $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['product_segment']['quantity'],
                        $productRules,
                        // CRITICAL: this is what makes the whole product rules cumulative and more and more restricting,
                        // they must all be valid for the global rule group to be valid
                        ProductRuleGroupType::ALL_PRODUCT_RULES,
                    ),
                ]);
            }
        }

        // Cart conditions
        if ($data['conditions'][DiscountConditionsType::CART_CONDITIONS]['children_selector'] === CartConditionsType::MINIMUM_PRODUCT_QUANTITY) {
            $conditionsCommand->setMinimumProductsQuantity($data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_product_quantity']);
        } elseif ($data['conditions'][DiscountConditionsType::CART_CONDITIONS]['children_selector'] === CartConditionsType::MINIMUM_AMOUNT) {
            $conditionsCommand->setMinimumAmount(
                new DecimalNumber((string) $data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_amount']['value']),
                $data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_amount']['currency'],
                $data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_amount']['tax_included'],
                $data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_amount']['shipping_included'],
            );
        }

        // Delivery conditions
        if ($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS]['children_selector'] === DeliveryConditionsType::CARRIERS) {
            $conditionsCommand->setCarrierIds($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS][DeliveryConditionsType::CARRIERS]);
        }
        if ($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS]['children_selector'] === DeliveryConditionsType::COUNTRY) {
            $conditionsCommand->setCountryIds($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS][DeliveryConditionsType::COUNTRY]);
        }

        // Customer eligibility conditions
        if (isset($data['customer_eligibility']['eligibility'])) {
            $customerEligibility = $data['customer_eligibility']['eligibility'];
            $selectedOption = $customerEligibility['children_selector'] ?? DiscountCustomerEligibilityChoiceType::ALL_CUSTOMERS;

            if ($selectedOption === DiscountCustomerEligibilityChoiceType::CUSTOMER_GROUPS) {
                $groupIds = $this->extractCustomerGroupIds($customerEligibility[DiscountCustomerEligibilityChoiceType::CUSTOMER_GROUPS] ?? []);
                $conditionsCommand->setCustomerGroupIds($groupIds);
            } else {
                $conditionsCommand->setCustomerGroupIds([]);
            }
        }

        $this->commandBus->handle($conditionsCommand);
    }

    private function updateDiscountCompatibility(int $discountId, array $data): void
    {
        if (!isset($data['usability']['compatibility'])) {
            return;
        }

        $compatibleTypeIds = [];
        foreach ($data['usability']['compatibility'] as $fieldName => $isChecked) {
            if ($isChecked && str_starts_with($fieldName, 'compatible_type_')) {
                $typeId = (int) str_replace('compatible_type_', '', $fieldName);
                $compatibleTypeIds[] = $typeId;
            }
        }

        $this->discountTypeRepository->setCompatibleTypesForDiscount($discountId, $compatibleTypeIds);
    }

    private function parseDateWithDefaultTime(?string $dateString, string $defaultTime): ?DateTimeImmutable
    {
        if (empty($dateString)) {
            return null;
        }

        $dateString = trim($dateString);

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d',
        ];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                // If the format doesn't include time, add the default time
                if (!str_contains($format, 'H:i')) {
                    $date->setTime((int) substr($defaultTime, 0, 2), (int) substr($defaultTime, 3, 2));
                }

                return DateTimeImmutable::createFromMutable($date);
            }
        }

        return null;
    }

    /**
     * Handle customer eligibility and set customer ID on the command if needed.
     *
     * @param AddDiscountCommand|UpdateDiscountCommand $command
     * @param array $data
     */
    private function handleCustomerEligibility(mixed $command, array $data): void
    {
        if (!isset($data['customer_eligibility']['eligibility'])) {
            return;
        }

        $customerEligibility = $data['customer_eligibility']['eligibility'];
        $selectedOption = $customerEligibility['children_selector'] ?? DiscountCustomerEligibilityChoiceType::ALL_CUSTOMERS;

        if ($selectedOption === DiscountCustomerEligibilityChoiceType::SINGLE_CUSTOMER) {
            $customerData = $customerEligibility[DiscountCustomerEligibilityChoiceType::SINGLE_CUSTOMER] ?? [];
            if (!empty($customerData) && isset($customerData[0]['id_customer'])) {
                $command->setCustomerId((int) $customerData[0]['id_customer']);
            }
        }
    }

    /**
     * Extract customer group IDs from the form data.
     * MaterialChoiceTableType returns a flat array of selected group IDs.
     *
     * @param array $groupData
     *
     * @return int[]
     */
    private function extractCustomerGroupIds(array $groupData): array
    {
        // MaterialChoiceTableType returns a flat array like [3, 4, 5]
        return array_map('intval', array_filter($groupData, 'is_numeric'));
    }
}
