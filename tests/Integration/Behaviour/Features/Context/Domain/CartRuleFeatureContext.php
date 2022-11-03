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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use CartRule;
use Configuration;
use Currency;
use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\CartRule\LegacyDiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\AddCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\BulkDeleteCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\BulkToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\DeleteCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\ToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRule;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;

class CartRuleFeatureContext extends AbstractDomainFeatureContext
{
    private $cartRuleStorageProperty = 'add_cart_rule';

    /**
     * @Transform /^active from "([^"]+)"$/
     * @Transform /^active until "([^"]+)"$/
     */
    public function transformDate(string $date)
    {
        return new DateTime($date);
    }

    /**
     * @Transform reduction should apply to "([^"]+)"
     */
    public function transformDiscountApplicationType(string $type): string
    {
        $availableTypesMap = [
            'order without shipping' => DiscountApplicationType::ORDER_WITHOUT_SHIPPING,
            'specific product' => DiscountApplicationType::SPECIFIC_PRODUCT,
            'cheapest product' => DiscountApplicationType::CHEAPEST_PRODUCT,
            'selected products' => DiscountApplicationType::SELECTED_PRODUCTS,
        ];

        return $availableTypesMap[$type] ?? $type;
    }

    /**
     * @When /^I want to create a new cart rule$/
     */
    public function prepareCartRuleCreation()
    {
        $this->setCartRuleProperties([]);
    }

    /**
     * @When /^I specify (its) name in default language as "([^"]+)"$/
     */
    public function createCartRuleWithName(array $properties, string $nameInDefaultLanguage)
    {
        $properties['name'] = $nameInDefaultLanguage;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^I specify (cart rule|its) "([^"]+)" as "([^"]*)"$/
     * @Given /^I specify that (its) "([^"]+)" is "([^"]*)"$/
     */
    public function specifyCartRuleProperty(array $properties, string $property, string $value)
    {
        $properties[$property] = $value;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^I specify that (its) ((active from|active until) "([^"]+)")$/
     */
    public function specifyCartRuleActiveFromOrUntil(array $properties, DateTime $date, string $property)
    {
        $properties[$property] = $date;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^I specify that partial use is (enabled|disabled) for (it)$/
     */
    public function specifyCartRulePartialUse(bool $isPartialUseEnabled, array $properties)
    {
        $properties['partial_use'] = $isPartialUseEnabled;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^I specify (its) status as (enabled|disabled)$/
     */
    public function specifyCartRuleStatus(array $properties, bool $isEnabled)
    {
        $properties['active'] = $isEnabled;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^I specify that (it) (should|should not) be highlighted in cart$/
     */
    public function specifyCartRuleHighlightedInCart(array $properties, bool $highlightInCart)
    {
        $properties['highlight'] = $highlightInCart;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^(its) minimum purchase amount in (currency "[^"]+") is "([^"]+)"$/
     */
    public function specifyMinimumPurchaseAmountInCurrency(array $properties, Currency $currency, $amount)
    {
        $properties['minimum_amount'] = (float) $amount;
        $properties['minimum_amount_currency'] = $currency->id;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^(its) minimum purchase amount is tax (included|excluded)$/
     */
    public function specifyIfMinimumPurchaseAmountIsTaxIncluded(array $properties, bool $isTaxIncluded)
    {
        $properties['minimum_amount_tax'] = $isTaxIncluded;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^(its) minimum purchase amount is shipping (included|excluded)$/
     */
    public function specifyIfMinimumPurchaseAmountIsShippingIncluded(array $properties, bool $isShippingIncluded)
    {
        $properties['minimum_amount_shipping'] = $isShippingIncluded;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^(it) gives free shipping$/
     */
    public function specifyCartRuleGivesFreeShipping(array $properties)
    {
        $properties['free_shipping'] = true;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^(it) gives a reduction amount of "([^"]+)" in (currency "[^"]+") which is tax (included|excluded) and (applies to (order without shipping|specific product|cheapest product|selected products))$/
     */
    public function specifyCartRuleGivesAmountReduction(
        array $properties,
        $reductionAmount,
        Currency $currency,
        $isTaxIncluded,
        $discountApplicationType
    ) {
        $properties['reduction_amount'] = (float) $reductionAmount;
        $properties['reduction_currency'] = $currency->id;
        $properties['reduction_tax'] = $isTaxIncluded;
        $properties['discount_application_type'] = $discountApplicationType;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Given /^(it) gives a percentage reduction of "([^"]+)" which (excludes|includes) discounted products and (applies to (order without shipping|specific product|cheapest product|selected products))$/
     */
    public function specifyCartRuleGivesPercentageReduction(
        array $properties,
        $percentage,
        bool $includesDiscountedProducts,
        $discountApplicationType
    ) {
        $properties['reduction_percentage'] = (float) $percentage;
        $properties['reduction_applies_to_discounted_products'] = $includesDiscountedProducts;
        $properties['discount_application_type'] = $discountApplicationType;
        $this->setCartRuleProperties($properties);
    }

    /**
     * @Then /^I save (it)$/
     */
    public function saveCartRule(array $properties)
    {
        $cartRuleAction = $this->createCartRuleAction(
            $properties['free_shipping'] ?? false,
            $properties['reduction_percentage'] ?? null,
            $properties['reduction_applies_to_discounted_products'] ?? null,
            $properties['reduction_amount'] ?? null,
            $properties['reduction_currency'] ?? null,
            $properties['reduction_tax'] ?? null,
            $properties['gift_product_id'] ?? null,
            $properties['gift_product_attribute_id'] ?? null
        );
        $cartRuleId = $this->createCartRule(
            $properties['name'],
            $properties['highlight'] ?? false,
            $properties['partial_use'] ?? false,
            $properties['priority'] ?? 0,
            $properties['active'] ?? true,
            $properties['date_from'],
            $properties['date_to'],
            $properties['quantity'],
            $properties['quantity_per_user'],
            $cartRuleAction,
            $properties['minimum_amount'],
            $properties['minimum_amount_currency'],
            $properties['minimum_amount_tax'],
            $properties['minimum_amount_shipping'],
            $properties['code'] ?? null,
            $properties['description'] ?? null,
            $properties['customer_id'] ?? null,
            $properties['discount_application_type'] ?? null,
            $properties['discount_product_id'] ?? null
        );

        if ($properties['code']) {
            SharedStorage::getStorage()->set(
                $properties['code'],
                $cartRuleId->getValue()
            );
        }

        SharedStorage::getStorage()->set(
            sprintf('cart_rule_object_%s', $properties['name']),
            new CartRule($cartRuleId->getValue())
        );

        SharedStorage::getStorage()->clear($this->cartRuleStorageProperty);
    }

    /**
     * @Then /^(its) name in default language should be "([^"]*)"$/
     */
    public function assertCartRuleNameInDefaultLanguage(CartRule $cartRule, string $value)
    {
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        if ($cartRule->name[$defaultLanguageId] !== $value) {
            throw new RuntimeException(sprintf('Invalid cart rule name in default language: got "%s", expected "%s"', $value, $cartRule->name[$defaultLanguageId]));
        }
    }

    /**
     * @Then /^(its) "([^"]+)" should be "([^"]+)"$/
     */
    public function assertCartRuleProperty(CartRule $cartRule, string $property, $value)
    {
        $propertyToCheck = (string) $cartRule->{$property};

        if (is_bool($value)) {
            $propertyToCheck = (bool) $propertyToCheck;
        }

        if ($propertyToCheck !== $value) {
            throw new RuntimeException(sprintf('Invalid cart rule property "%s", with value "%s"', $property, var_export($value, true)));
        }
    }

    /**
     * @Then /^(it) should be ((active from|active until) "([^"]+)")$/
     */
    public function assertCartRuleIsActiveFromUntil(CartRule $cartRule, DateTime $date, string $property)
    {
        $formattedDate = $date->format('Y-m-d H:i:s');

        if ($cartRule->{$property} !== $formattedDate) {
            throw new RuntimeException(sprintf('Invalid cart rule property "%s", with value "%s"', $property, var_export($formattedDate, true)));
        }
    }

    /**
     * @Given /^(it) (should|should not) be highlighted in cart$/
     */
    public function assertCartRuleIsHighlightedInCart(CartRule $cartRule, bool $isHighlighted)
    {
        if ((bool) $cartRule->highlight !== $isHighlighted) {
            throw new RuntimeException(sprintf('Invalid cart rule property "highlight", with value "%s"', var_export($isHighlighted, true)));
        }
    }

    /**
     * @Given /^(it) should have minimum purchase amount of "([^"]+)" in (currency "[^"]+")$/
     */
    public function assertCartRuleHasMinimumPurchaseAmountInCurrency(
        CartRule $cartRule,
        $minimumPurchaseAmount,
        Currency $currency
    ) {
        if (!$this->areNumbersEqual($cartRule->minimum_amount, $minimumPurchaseAmount)) {
            throw new RuntimeException(sprintf('Invalid cart rule minimum purchase amount value "%s"', $minimumPurchaseAmount));
        }

        if ((int) $cartRule->minimum_amount_currency !== (int) $currency->id) {
            throw new RuntimeException(sprintf('Invalid cart rule minimum purchase amount currency with ID "%s"', $currency->id));
        }
    }

    /**
     * @Given /^(its) minimum purchase amount should be tax (included|excluded)$/
     */
    public function assertCartRuleMinimumPurchaseAmountIsTaxExcluded(CartRule $cartRule, bool $isTaxIncluded)
    {
        if ((bool) $cartRule->minimum_amount_tax !== $isTaxIncluded) {
            throw new RuntimeException('Invalid cart rule minimum purchase amount tax flag');
        }
    }

    /**
     * @Given /^(its) minimum purchase amount should be shipping (included|excluded)$/
     */
    public function assertCartRuleMinimumPurchaseAmountIsShippingIncluded(CartRule $cartRule, bool $isShippingIncluded)
    {
        if ((bool) $cartRule->minimum_amount_shipping !== $isShippingIncluded) {
            throw new RuntimeException('Invalid cart rule minimum purchase amount shipping flag');
        }
    }

    /**
     * @Given /^(it) (should|should not) give free shipping$/
     */
    public function assertCartRuleGivesFreeShipping(CartRule $cartRule, bool $isFreeShipping)
    {
        if ((bool) $cartRule->free_shipping !== $isFreeShipping) {
            throw new RuntimeException('Cart rule free shipping flag is invalid');
        }
    }

    /**
     * @Given /^(it) should give a reduction of "([^"]+)" in (currency "[^"]+") which is tax (included|excluded) and (applies to (order without shipping|specific product|cheapest product|selected products))$/
     */
    public function assertCartRuleGivesAmountReduction(
        CartRule $cartRule,
        $reductionAmount,
        Currency $currency,
        bool $isTaxIncluded,
        string $discountApplicationType
    ) {
        if (!$this->areNumbersEqual($cartRule->reduction_amount, $reductionAmount)) {
            throw new RuntimeException(sprintf('Cart rule reduction amount "%s" is not expected', $reductionAmount));
        }

        if ((int) $cartRule->reduction_currency !== (int) $currency->id) {
            throw new RuntimeException(sprintf('Cart rule reduction currency ID "%s" is not expected', $currency->id));
        }

        if ((bool) $cartRule->reduction_tax !== $isTaxIncluded) {
            throw new RuntimeException(sprintf('Cart rule reduction tax flag "%s" is not expected', var_export($isTaxIncluded)));
        }

        $this->assertDiscountApplicationTypeIsValid($cartRule, $discountApplicationType);
    }

    /**
     * @Given /^(it) should give a percentage reduction of "([^"]+)" which (excludes|includes) discounted products and (applies to (order without shipping|specific product|cheapest product|selected products))$/
     */
    public function assertCartRuleGivesPercentageReduction(
        CartRule $cartRule,
        $percentage,
        bool $includesDiscountedProducts,
        string $discountApplicationType
    ) {
        if (!$this->areNumbersEqual($cartRule->reduction_percent, $percentage)) {
            throw new RuntimeException(sprintf('Cart rule reduction percentage "%s" is not expected', $percentage));
        }

        if ((bool) $cartRule->reduction_exclude_special !== !$includesDiscountedProducts) {
            throw new RuntimeException(sprintf('Cart rule reduction_exclude_special flag "%s" is not expected', var_export($includesDiscountedProducts)));
        }

        $this->assertDiscountApplicationTypeIsValid($cartRule, $discountApplicationType);
    }

    /**
     * @When I create cart rule :cartRuleReference with following properties:
     *
     * @param string $cartRuleReference
     * @param TableNode $node
     *
     * @throws CartRuleConstraintException
     * @throws DomainConstraintException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function createCartRuleWithReference(string $cartRuleReference, TableNode $node): void
    {
        $data = $node->getRowsHash();
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        $cartRuleAction = $this->createCartRuleAction(
            $data['free_shipping'] ?? false,
            $data['reduction_percentage'] ?? null,
            $data['reduction_applies_to_discounted_products'] ?? null,
            $data['reduction_amount'] ?? null,
            $data['reduction_currency'] ?? null,
            $data['reduction_tax'] ?? null,
            $data['gift_product_id'] ?? null,
            $data['gift_product_attribute_id'] ?? null
        );

        $currencyId = SharedStorage::getStorage()->get($data['minimum_amount_currency']);

        $command = new AddCartRuleCommand(
            [$defaultLanguageId => $data['name_in_default_language']],
            $data['highlight'],
            $data['allow_partial_use'],
            $data['priority'],
            $data['is_active'],
            new DateTime($data['valid_from']),
            new DateTime($data['valid_to']),
            $data['total_quantity'],
            $data['quantity_per_user'],
            $cartRuleAction,
            $data['minimum_amount'],
            $currencyId,
            $data['minimum_amount_tax_included'],
            $data['minimum_amount_shipping_included']
        );

        $command->setDescription($data['description'] ?? '');
        $command->setCode($data['code'] ?? '');

        /** @var CartRuleId $cartRule */
        $cartRule = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($cartRuleReference, new CartRule($cartRule->getValue()));
    }

    /**
     * @When I delete Cart rule with reference :cartRuleReference
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     */
    public function deleteCartRule(string $cartRuleReference): void
    {
        $cartRule = SharedStorage::getStorage()->get($cartRuleReference);
        $command = new DeleteCartRuleCommand((int) $cartRule->id);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I disable cart rule with reference :cartRuleReference
     *
     * @param string $cartRuleReference
     */
    public function disableCartRule(string $cartRuleReference): void
    {
        $cartRule = SharedStorage::getStorage()->get($cartRuleReference);
        $command = new ToggleCartRuleStatusCommand((int) $cartRule->id, false);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I enable cart rule with reference :cartRuleReference
     *
     * @param string $cartRuleReference
     */
    public function enableCartRule(string $cartRuleReference): void
    {
        $cartRule = SharedStorage::getStorage()->get($cartRuleReference);
        $command = new ToggleCartRuleStatusCommand((int) $cartRule->id, true);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I bulk enable cart rules :cartRuleReferences
     *
     * @param string $cartRuleReferences
     *
     * @throws CartRuleConstraintException
     */
    public function bulkEnableCartRules(string $cartRuleReferences): void
    {
        $cartRuleIds = [];
        $cartRuleReferenceArray = explode(',', $cartRuleReferences);

        foreach ($cartRuleReferenceArray as $carRuleReference) {
            $cartRuleIds[] = SharedStorage::getStorage()->get($carRuleReference)->id;
        }

        $command = new BulkToggleCartRuleStatusCommand($cartRuleIds, true);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I bulk disable cart rules :cartRuleReferences
     *
     * @param string $cartRuleReferences
     *
     * @throws CartRuleConstraintException
     */
    public function bulkDisableCartRules(string $cartRuleReferences): void
    {
        $cartRuleIds = [];
        $cartRuleReferenceArray = explode(',', $cartRuleReferences);

        foreach ($cartRuleReferenceArray as $carRuleReference) {
            $cartRuleIds[] = SharedStorage::getStorage()->get($carRuleReference)->id;
        }

        $command = new BulkToggleCartRuleStatusCommand($cartRuleIds, false);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I bulk delete cart rules :cartRuleReferences
     *
     * @param string $cartRuleReferences
     *
     * @throws CartRuleConstraintException
     */
    public function bulkDeleteCartRules(string $cartRuleReferences): void
    {
        $cartRuleIds = [];
        $cartRuleReferenceArray = explode(',', $cartRuleReferences);

        foreach ($cartRuleReferenceArray as $carRuleReference) {
            $cartRuleIds[] = SharedStorage::getStorage()->get($carRuleReference)->id;
        }
        $command = new BulkDeleteCartRuleCommand($cartRuleIds);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then Cart rule with reference :cartRuleReference is enabled
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     * @throws RuntimeException
     */
    public function assertCartRuleEnabled(string $cartRuleReference): void
    {
        $cartRuleId = (int) SharedStorage::getStorage()->get($cartRuleReference)->id;

        /** @var EditableCartRule $cartRule */
        $cartRule = $this->getQueryBus()->handle(new GetCartRuleForEditing($cartRuleId));
        if (!$cartRule->getInformation()->isEnabled() === true) {
            throw new RuntimeException(sprintf('Cart rule %s is not disabled', $cartRuleReference));
        }
    }

    /**
     * @Then Cart rule with reference :cartRuleReference is disabled
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     */
    public function assertCartRuleDisabled(string $cartRuleReference): void
    {
        $cartRuleId = (int) SharedStorage::getStorage()->get($cartRuleReference)->id;

        /** @var EditableCartRule $cartRule */
        $cartRule = $this->getQueryBus()->handle(new GetCartRuleForEditing($cartRuleId));
        if (!$cartRule->getInformation()->isEnabled() === false) {
            throw new RuntimeException(sprintf('Cart rule %s is not enabled', $cartRuleReference));
        }
    }

    /**
     * @Then Cart rule with reference :cartRuleReference does not exist
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     * @throws NoExceptionAlthoughExpectedException
     */
    public function assertCartRuleDeleted(string $cartRuleReference): void
    {
        $cartRuleId = (int) SharedStorage::getStorage()->get($cartRuleReference)->id;

        try {
            $this->getQueryBus()->handle(new GetCartRuleForEditing($cartRuleId));
            throw new NoExceptionAlthoughExpectedException(sprintf('Cart rule "%s" was found, but it was expected to be deleted', $cartRuleReference));
        } catch (CartRuleNotFoundException $e) {
            SharedStorage::getStorage()->clear($cartRuleReference);
        }
    }

    /**
     * @Transform /^(active from|active until|quantity per user|partial use|status|highlight in cart)$/
     *
     * @param string $property
     *
     * @return string
     */
    public function getMappedProperty(string $property): string
    {
        $map = [
            'active from' => 'date_from',
            'active until' => 'date_to',
            'quantity per user' => 'quantity_per_user',
            'partial use' => 'partial_use',
            'status' => 'active',
            'highlight in cart' => 'highlight',
        ];

        return $map[$property] ?? $property;
    }

    /**
     * @Transform /^applies to ([^"]+)$/
     *
     * @param string $type
     *
     * @return mixed|string
     */
    public function getMappedDiscountApplicationType(string $type)
    {
        $map = [
            'order without shipping' => DiscountApplicationType::ORDER_WITHOUT_SHIPPING,
            'specific product' => DiscountApplicationType::SPECIFIC_PRODUCT,
            'cheapest product' => DiscountApplicationType::CHEAPEST_PRODUCT,
            'selected products' => DiscountApplicationType::SELECTED_PRODUCTS,
        ];

        return $map[$type] ?? $type;
    }

    /**
     * Asserts that discount application type is valid for given cart rule
     *
     * @param CartRule $cartRule
     * @param string $discountApplicationType
     */
    private function assertDiscountApplicationTypeIsValid(CartRule $cartRule, string $discountApplicationType): void
    {
        $reductionType = (int) $cartRule->reduction_product;

        switch ($discountApplicationType) {
            case DiscountApplicationType::ORDER_WITHOUT_SHIPPING:
                $hasError = LegacyDiscountApplicationType::ORDER_WITHOUT_SHIPPING !== $reductionType;

                break;

            case DiscountApplicationType::SELECTED_PRODUCTS:
                $hasError = LegacyDiscountApplicationType::SELECTED_PRODUCTS !== $reductionType;

                break;

            case DiscountApplicationType::CHEAPEST_PRODUCT:
                $hasError = LegacyDiscountApplicationType::CHEAPEST_PRODUCT !== $reductionType;

                break;

            case DiscountApplicationType::SPECIFIC_PRODUCT:
                $hasError = 0 >= $reductionType;

                break;

            default:
                throw new RuntimeException(sprintf('Invalid cart rule discount application type "%s"', $discountApplicationType));
        }

        if ($hasError) {
            throw new RuntimeException(sprintf('Cart rule discount application type "%s" was expected', $discountApplicationType));
        }
    }

    /**
     * @param string $nameInDefaultLanguage
     * @param bool $highlightInCart
     * @param bool $allowPartialUse
     * @param int $priority
     * @param bool $isActive
     * @param DateTime $validFrom
     * @param DateTime $validTo
     * @param int $totalQuantity
     * @param int $quantityPerUser
     * @param CartRuleActionInterface $cartRuleAction
     * @param float $minimumAmount
     * @param int $minimumAmountCurrencyId
     * @param bool $minimumAmountTaxIncluded
     * @param bool $minimumAmountShippingIncluded
     * @param string|null $code
     * @param string|null $description
     * @param int|null $customerId
     * @param string|null $discountApplicationType
     * @param int|null $discountProductId
     *
     * @return CartRuleId
     *
     * @throws CartRuleConstraintException
     * @throws DomainConstraintException
     */
    private function createCartRule(
        string $nameInDefaultLanguage,
        bool $highlightInCart,
        bool $allowPartialUse,
        int $priority,
        bool $isActive,
        DateTime $validFrom,
        DateTime $validTo,
        int $totalQuantity,
        int $quantityPerUser,
        CartRuleActionInterface $cartRuleAction,
        float $minimumAmount,
        int $minimumAmountCurrencyId,
        bool $minimumAmountTaxIncluded,
        bool $minimumAmountShippingIncluded,
        string $code = null,
        string $description = null,
        int $customerId = null,
        string $discountApplicationType = null,
        int $discountProductId = null
    ): CartRuleId {
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        $command = new AddCartRuleCommand(
            [$defaultLanguageId => $nameInDefaultLanguage],
            $highlightInCart,
            $allowPartialUse,
            $priority,
            $isActive,
            $validFrom,
            $validTo,
            $totalQuantity,
            $quantityPerUser,
            $cartRuleAction,
            $minimumAmount,
            $minimumAmountCurrencyId,
            !$minimumAmountTaxIncluded,
            !$minimumAmountShippingIncluded
        );

        if (null !== $code) {
            $command->setCode($code);
        }

        if (null !== $description) {
            $command->setDescription($description);
        }

        if (null !== $customerId) {
            $command->setCustomerId($customerId);
        }

        if (null !== $discountApplicationType) {
            $command->setDiscountApplicationType($discountApplicationType);
        }

        if (null !== $discountProductId) {
            $command->setDiscountProductId($discountProductId);
        }

        return $this->getCommandBus()->handle($command);
    }

    /**
     * Create a cart rule action that can be used for cart rule creation.
     *
     * @param bool $isFreeShipping
     * @param float|null $percentage
     * @param bool|null $percentageAppliesToDiscountedProducts
     * @param float|null $amount
     * @param int|null $amountCurrencyId
     * @param bool|null $amountTaxIncluded
     * @param int|null $giftProductId
     * @param int|null $giftProductAttributeId
     *
     * @return CartRuleActionInterface
     *
     * @throws CartRuleConstraintException
     * @throws DomainConstraintException
     * @throws \PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException
     */
    private function createCartRuleAction(
        bool $isFreeShipping,
        float $percentage = null,
        bool $percentageAppliesToDiscountedProducts = null,
        float $amount = null,
        int $amountCurrencyId = null,
        bool $amountTaxIncluded = null,
        int $giftProductId = null,
        int $giftProductAttributeId = null
    ): CartRuleActionInterface {
        $builder = new CartRuleActionBuilder();

        $builder->setFreeShipping($isFreeShipping);

        if (null !== $percentage) {
            $builder->setPercentageDiscount(
                new PercentageDiscount($percentage, $percentageAppliesToDiscountedProducts)
            );
        }

        if (null !== $amount) {
            $builder->setAmountDiscount(
                new MoneyAmountCondition(
                    new Money(new DecimalNumber((string) $amount), new CurrencyId($amountCurrencyId)),
                    !$amountTaxIncluded
                )
            );
        }

        if (null !== $giftProductId) {
            $builder->setGiftProduct(new GiftProduct($giftProductId, $giftProductAttributeId));
        }

        return $builder->build();
    }

    private function areNumbersEqual($number1, $number2): bool
    {
        $number1 = new DecimalNumber((string) $number1);

        return $number1->equals(new DecimalNumber((string) $number2));
    }

    /**
     * Sets given properties into shared storage under common key.
     *
     * @param array $properties
     */
    private function setCartRuleProperties(array $properties): void
    {
        SharedStorage::getStorage()->set($this->cartRuleStorageProperty, $properties);
    }
}
