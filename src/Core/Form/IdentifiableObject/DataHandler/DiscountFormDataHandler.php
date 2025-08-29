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

use PrestaShop\Decimal\DecimalNumber;
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
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountUsabilityModeType;
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
                $command->setPercentDiscount(new DecimalNumber('50'));
                $command->setReductionProduct(1);
                break;
            case DiscountType::FREE_GIFT:
                $command->setProductId((int) ($data['free_gift'][0]['product_id'] ?? 0));
                $command->setCombinationId((int) ($data['free_gift'][0]['combination_id'] ?? 0));
                break;
            default:
                throw new RuntimeException('Unknown discount type ' . $discountType);
        }

        $command->setActive(true);

        if ($data['usability']['mode']['children_selector'] === DiscountUsabilityModeType::CODE_MODE) {
            $command->setCode($data['usability']['mode']['code'] ?? '');
        } else {
            $command->setCode('');
        }

        $command->setTotalQuantity(100);

        /** @var DiscountId $discountId */
        $discountId = $this->commandBus->handle($command);
        $this->updateDiscountConditions($discountId->getValue(), $data);

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
                break;
            case DiscountType::FREE_GIFT:
                $command->setProductId((int) ($data['free_gift'][0]['product_id'] ?? 0));
                $command->setCombinationId((int) ($data['free_gift'][0]['combination_id'] ?? 0));
                break;
            default:
                throw new RuntimeException('Unknown discount type ' . $discountType);
        }
        $command->setLocalizedNames($data['information']['names']);

        if ($data['usability']['mode']['children_selector'] === DiscountUsabilityModeType::CODE_MODE) {
            $command->setCode($data['usability']['mode']['code'] ?? '');
        } else {
            $command->setCode('');
        }

        $this->commandBus->handle($command);
        $this->updateDiscountConditions($id, $data);
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
        if ($data['conditions']['children_selector'] === DiscountConditionsType::CART_CONDITIONS) {
            if ($data['conditions']['cart_conditions']['children_selector'] === CartConditionsType::MINIMUM_PRODUCT_QUANTITY) {
                $conditionsCommand->setMinimumProductsQuantity($data['conditions']['cart_conditions']['minimum_product_quantity']);
            } elseif ($data['conditions']['cart_conditions']['children_selector'] === CartConditionsType::MINIMUM_AMOUNT) {
                $conditionsCommand->setMinimumAmount(
                    new DecimalNumber((string) $data['conditions']['cart_conditions']['minimum_amount']['value']),
                    $data['conditions']['cart_conditions']['minimum_amount']['currency'],
                    $data['conditions']['cart_conditions']['minimum_amount']['tax_included'],
                    $data['conditions']['cart_conditions']['minimum_amount']['shipping_included'],
                );
            } elseif ($data['conditions']['cart_conditions']['children_selector'] === CartConditionsType::SPECIFIC_PRODUCTS) {
                $specificProducts = $data['conditions']['cart_conditions']['specific_products'] ?? [];
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
            } elseif ($data['conditions']['cart_conditions']['children_selector'] === CartConditionsType::PRODUCT_SEGMENT) {
                $manufacturer = $data['conditions']['cart_conditions']['product_segment']['manufacturer'] ?? [];
                $category = $data['conditions']['cart_conditions']['product_segment']['category'] ?? '';
                $supplier = $data['conditions']['cart_conditions']['product_segment']['supplier'] ?? [];
                $attributes = $data['conditions']['cart_conditions']['product_segment']['attributes']['groups'] ?? [];

                $productRules = [];
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

                if (!empty($productRules)) {
                    $conditionsCommand->setProductConditions([
                        new ProductRuleGroup(
                            $data['conditions']['cart_conditions']['product_segment']['quantity'],
                            $productRules,
                            // CRITICAL: this is what makes the whole product rules cumulative and more and more restricting,
                            // they must all be valid for the global rule group to be valid
                            ProductRuleGroupType::ALL_PRODUCT_RULES,
                        ),
                    ]);
                }
            }
        } elseif ($data['conditions']['children_selector'] === DiscountConditionsType::DELIVERY_CONDITIONS) {
            if ($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS]['children_selector'] === DeliveryConditionsType::CARRIERS) {
                $conditionsCommand->setCarrierIds($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS][DeliveryConditionsType::CARRIERS]);
            }
            if ($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS]['children_selector'] === DeliveryConditionsType::COUNTRY) {
                $conditionsCommand->setCountryIds($data['conditions'][DiscountConditionsType::DELIVERY_CONDITIONS][DeliveryConditionsType::COUNTRY]);
            }
        }

        $this->commandBus->handle($conditionsCommand);
    }
}
