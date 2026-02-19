<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use DateTime;
use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountCommand;
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

        $command->setActive(true);
        $this->fillCommandFromData($command, $data);

        /** @var DiscountId $discountId */
        $discountId = $this->commandBus->handle($command);

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

        $command->setLocalizedNames($data['information']['names']);

        $this->fillCommandFromData($command, $data);

        $this->commandBus->handle($command);
    }

    /**
     * Fill command properties from form data (common to create and update).
     *
     * @param AddDiscountCommand|UpdateDiscountCommand $command
     * @param array $data
     *
     * @throws CurrencyException
     * @throws DiscountConstraintException
     * @throws DomainConstraintException
     */
    private function fillCommandFromData(AddDiscountCommand|UpdateDiscountCommand $command, array $data): void
    {
        $discountType = $data['information']['discount_type'];

        // Handle discount value based on type
        switch ($discountType) {
            case DiscountType::FREE_SHIPPING:
                break;
            case DiscountType::CART_LEVEL:
            case DiscountType::ORDER_LEVEL:
            case DiscountType::PRODUCT_LEVEL:
                $this->setDiscountValue($command, $data);
                break;
            case DiscountType::FREE_GIFT:
                $command->setGiftProductId(!empty($data['free_gift'][0]['product_id']) ? (int) $data['free_gift'][0]['product_id'] : null);
                $command->setGiftCombinationId(!empty($data['free_gift'][0]['combination_id']) ? (int) $data['free_gift'][0]['combination_id'] : null);
                break;
            default:
                throw new RuntimeException('Unknown discount type ' . $discountType);
        }

        // Set description
        $command->setDescription($data['information']['description'] ?? '');

        // Set code
        if ($data['usability']['mode']['children_selector'] === DiscountUsabilityModeType::CODE_MODE) {
            $command->setCode($data['usability']['mode']['code'] ?? '');
        } else {
            $command->setCode('');
        }

        // Set validity date range
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

        // Set customer eligibility
        $this->handleCustomerEligibility($command, $data);

        // Set priority and compatibility
        if (isset($data['usability']['priority']) && $data['usability']['priority'] > 0) {
            $command->setPriority((int) $data['usability']['priority']);
        }
        $command->setCompatibleDiscountTypeIds(array_unique($data['usability']['compatibility'] ?? []));

        // Quantity limitations
        if (array_key_exists('quantity_total', $data['usability'])) {
            $command->setTotalQuantity($data['usability']['quantity_total']);
        }

        if (array_key_exists('quantity_per_customer', $data['usability'])) {
            $command->setQuantityPerUser($data['usability']['quantity_per_customer']);
        }

        // Set discount conditions
        $this->updateDiscountConditions($command, $data);
    }

    /**
     * Set the discount value (amount or percent) on the command.
     *
     * @param AddDiscountCommand|UpdateDiscountCommand $command
     * @param array $data
     */
    private function setDiscountValue(AddDiscountCommand|UpdateDiscountCommand $command, array $data): void
    {
        if ($data['value']['reduction']['type'] === DiscountSettings::AMOUNT) {
            $command->setReductionAmount(
                new DecimalNumber((string) $data['value']['reduction']['value']),
                (int) $data['value']['reduction']['currency'],
                (bool) $data['value']['reduction']['include_tax']
            );
        } elseif ($data['value']['reduction']['type'] === DiscountSettings::PERCENT) {
            $command->setReductionPercent(new DecimalNumber((string) $data['value']['reduction']['value']));
        } else {
            throw new RuntimeException('Unknown discount value type ' . $data['value']['reduction']['type']);
        }
    }

    private function updateDiscountConditions(AddDiscountCommand|UpdateDiscountCommand $command, array $data): void
    {
        // Products conditions
        if ($data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['children_selector'] === ProductConditionsType::SPECIFIC_PRODUCTS) {
            $specificProducts = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS][ProductConditionsType::SPECIFIC_PRODUCTS] ?? [];
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

            $command->setCheapestProduct(false);
            $command->setProductConditions($productRuleGroups);
        } elseif ($data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['children_selector'] === ProductConditionsType::PRODUCT_SEGMENT) {
            $manufacturer = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS][ProductConditionsType::PRODUCT_SEGMENT]['manufacturer'] ?? [];
            $category = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS][ProductConditionsType::PRODUCT_SEGMENT]['category'] ?? '';
            $supplier = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS][ProductConditionsType::PRODUCT_SEGMENT]['supplier'] ?? [];
            $attributes = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS][ProductConditionsType::PRODUCT_SEGMENT]['attributes']['groups'] ?? [];
            $features = $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS][ProductConditionsType::PRODUCT_SEGMENT]['features']['groups'] ?? [];

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

            $command->setCheapestProduct(false);
            $command->setProductConditions([
                new ProductRuleGroup(
                    $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS][ProductConditionsType::PRODUCT_SEGMENT]['quantity'],
                    $productRules,
                    // CRITICAL: this is what makes the whole product rules cumulative and more and more restricting,
                    // they must all be valid for the global rule group to be valid
                    ProductRuleGroupType::ALL_PRODUCT_RULES,
                ),
            ]);
        } elseif ($data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['children_selector'] === ProductConditionsType::CHEAPEST_PRODUCT) {
            $command->setProductConditions([]);
            $command->setCheapestProduct(true);
        } elseif ($data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['children_selector'] === ProductConditionsType::NONE) {
            $command->setProductConditions([]);
            $command->setCheapestProduct(false);
        }

        // Cart conditions
        if ($data['conditions'][DiscountConditionsType::CART_CONDITIONS]['children_selector'] === CartConditionsType::MINIMUM_PRODUCT_QUANTITY) {
            $command->setMinimumProductQuantity($data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_product_quantity']);
        } elseif ($data['conditions'][DiscountConditionsType::CART_CONDITIONS]['children_selector'] === CartConditionsType::MINIMUM_AMOUNT) {
            $command->setMinimumAmount(
                new DecimalNumber((string) $data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_amount']['value']),
                $data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_amount']['currency'],
                $data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_amount']['tax_included'],
                $data['conditions'][DiscountConditionsType::CART_CONDITIONS]['minimum_amount']['shipping_included'] ?? false,
            );
        } elseif ($data['conditions'][DiscountConditionsType::CART_CONDITIONS]['children_selector'] === CartConditionsType::NONE) {
            $command->setMinimumAmount(null);
            $command->setMinimumProductQuantity(0);
        }

        // Delivery conditions
        if ($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS]['children_selector'] === DeliveryConditionsType::CARRIERS) {
            $command->setCarrierIds($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS][DeliveryConditionsType::CARRIERS]);
        }
        if ($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS]['children_selector'] === DeliveryConditionsType::COUNTRY) {
            $command->setCountryIds($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS][DeliveryConditionsType::COUNTRY]);
        }
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
            $command->setCustomerGroupIds([]);
        } elseif ($selectedOption === DiscountCustomerEligibilityChoiceType::CUSTOMER_GROUPS) {
            $groupIds = $this->extractCustomerGroupIds($customerEligibility[DiscountCustomerEligibilityChoiceType::CUSTOMER_GROUPS] ?? []);
            $command->setCustomerGroupIds($groupIds);
            $command->setCustomerId(0);
        } else {
            $command->setCustomerGroupIds([]);
            $command->setCustomerId(0);
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
