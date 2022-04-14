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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use Cart;
use Context;
use Country;
use Currency;
use Customer;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Shop;

/**
 * Allows manipulating context state.
 * This was adapted for specific broken legacy use cases when the previous state of context must be restored after some actions.
 *
 * e.g. order creation from back office.
 *  Legacy requires Context properties (currency, country etc.) instead of using cart properties
 *  so some context props must be changed for a while and then restored to previous state.
 */
final class ContextStateManager
{
    private const FIELD_CART = 'cart';
    private const FIELD_COUNTRY = 'country';
    private const FIELD_CURRENCY = 'currency';
    private const FIELD_LANGUAGE = 'language';
    private const FIELD_CUSTOMER = 'customer';
    private const FIELD_SHOP = 'shop';
    private const FIELD_SHOP_CONTEXT = 'shopContext';

    private const MANAGED_FIELDS = [
        self::FIELD_CART,
        self::FIELD_COUNTRY,
        self::FIELD_CURRENCY,
        self::FIELD_LANGUAGE,
        self::FIELD_CUSTOMER,
        self::FIELD_SHOP,
        self::FIELD_SHOP_CONTEXT,
    ];

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var array|null
     */
    private $contextFieldsStack = null;

    /**
     * @param LegacyContext $legacyContext
     */
    public function __construct(LegacyContext $legacyContext)
    {
        $this->legacyContext = $legacyContext;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->legacyContext->getContext();
    }

    /**
     * Sets context cart and saves previous value
     *
     * @param Cart|null $cart
     *
     * @return $this
     */
    public function setCart(?Cart $cart): self
    {
        $this->saveContextField(self::FIELD_CART);
        $this->getContext()->cart = $cart;

        return $this;
    }

    /**
     * Sets context country and saves previous value
     *
     * @param Country|null $country
     *
     * @return $this
     */
    public function setCountry(?Country $country): self
    {
        $this->saveContextField(self::FIELD_COUNTRY);
        $this->getContext()->country = $country;

        return $this;
    }

    /**
     * Sets context currency and saves previous value
     *
     * @param Currency|null $currency
     *
     * @return $this
     */
    public function setCurrency(?Currency $currency): self
    {
        $this->saveContextField(self::FIELD_CURRENCY);
        $this->getContext()->currency = $currency;

        return $this;
    }

    /**
     * Sets context language and saves previous value
     *
     * @param Language|null $language
     *
     * @return $this
     */
    public function setLanguage(?Language $language): self
    {
        $this->saveContextField(self::FIELD_LANGUAGE);
        $this->getContext()->language = $language;
        if ($language) {
            $this->getContext()->getTranslator()->setLocale($language->locale);
        }

        return $this;
    }

    /**
     * Sets context customer and saves previous value
     *
     * @param Customer|null $customer
     *
     * @return $this
     */
    public function setCustomer(?Customer $customer): self
    {
        $this->saveContextField(self::FIELD_CUSTOMER);
        $this->getContext()->customer = $customer;

        return $this;
    }

    /**
     * Sets context shop and saves previous value
     *
     * @param Shop $shop
     *
     * @return $this
     *
     * @throws \PrestaShopException
     */
    public function setShop(Shop $shop): self
    {
        $this->saveContextField(self::FIELD_SHOP);
        $this->getContext()->shop = $shop;
        Shop::setContext(Shop::CONTEXT_SHOP, $shop->id);

        return $this;
    }

    /**
     * @param ShopConstraint $shopConstraint
     *
     * @return $this
     */
    public function setShopContext(ShopConstraint $shopConstraint): self
    {
        $this->saveContextField(self::FIELD_SHOP);

        if ($shopConstraint->forAllShops()) {
            Shop::setContext(Shop::CONTEXT_ALL);

            return $this;
        }

        if ($shopConstraint->getShopGroupId()) {
            Shop::setContext(Shop::CONTEXT_GROUP, $shopConstraint->getShopGroupId()->getValue());

            return $this;
        }

        if ($shopConstraint->getShopId()) {
            $shopId = $shopConstraint->getShopId()->getValue();
            Shop::setContext(Shop::CONTEXT_SHOP, $shopId);
            $this->getContext()->shop = new Shop($shopId);
        }

        return $this;
    }

    /**
     * Restores context to a state before changes
     *
     * @return self
     */
    public function restorePreviousContext(): self
    {
        $stackFields = array_keys($this->contextFieldsStack[$this->getCurrentStashIndex()]);
        foreach ($stackFields as $fieldName) {
            $this->restoreContextField($fieldName);
        }
        $this->removeLastSavedContext();

        return $this;
    }

    /**
     * Saves the current overridden fields in the context, allowing you to set new values to the
     * current Context. Next time you call restorePreviousContext the context will be refilled with
     * the values that were saved during this call.
     *
     * This is useful if several services use the ContextStateManager, this way if every service
     * saved the context before modifying it there is no risk of removing previous modifications
     * when you restore the context, because the different states have been stacked.
     *
     * @return $this
     */
    public function saveCurrentContext(): self
    {
        // No context field has been overridden yet so no need to save/stack it
        if (null === $this->contextFieldsStack) {
            return $this;
        }

        // Saves all the fields that have not been overridden
        foreach (self::MANAGED_FIELDS as $contextField) {
            $this->saveContextField($contextField);
        }

        // Add a new empty layer
        $this->contextFieldsStack[] = [];

        return $this;
    }

    /**
     * Return the stack of modified fields
     * If it's null, no context field has been overridden
     *
     * @return array|null
     */
    public function getContextFieldsStack(): ?array
    {
        return $this->contextFieldsStack;
    }

    /**
     * Save context field into local array
     *
     * @param string $fieldName
     */
    private function saveContextField(string $fieldName)
    {
        $currentStashIndex = $this->getCurrentStashIndex();
        // NOTE: array_key_exists important here, isset cannot be used because it would not detect if null is stored
        if (!array_key_exists($fieldName, $this->contextFieldsStack[$currentStashIndex])) {
            switch ($fieldName) {
                case self::FIELD_SHOP:
                    $this->contextFieldsStack[$currentStashIndex][self::FIELD_SHOP] = $this->getContext()->$fieldName;
                    $this->contextFieldsStack[$currentStashIndex][self::FIELD_SHOP_CONTEXT] = Shop::getContext();
                    break;
                default:
                    $this->contextFieldsStack[$currentStashIndex][$fieldName] = $this->getContext()->$fieldName;
            }
        }
    }

    /**
     * Restores context saved value, and remove save value from local array
     *
     * @param string $fieldName
     */
    private function restoreContextField(string $fieldName): void
    {
        $currentStashIndex = $this->getCurrentStashIndex();
        // NOTE: array_key_exists important here, isset cannot be used because it would not detect if null is stored
        if (array_key_exists($fieldName, $this->contextFieldsStack[$currentStashIndex])) {
            if (self::FIELD_SHOP === $fieldName) {
                $this->restoreShopContext($currentStashIndex);
            }
            if (self::FIELD_LANGUAGE === $fieldName && $this->contextFieldsStack[$currentStashIndex][$fieldName] instanceof Language) {
                $this->getContext()->getTranslator()->setLocale($this->contextFieldsStack[$currentStashIndex][$fieldName]->locale);
            }
            $this->getContext()->$fieldName = $this->contextFieldsStack[$currentStashIndex][$fieldName];
            unset($this->contextFieldsStack[$currentStashIndex][$fieldName]);
        }
    }

    /**
     * Returns the index of the current stack
     *
     * @return int
     */
    private function getCurrentStashIndex(): int
    {
        // If this is the first time the index is needed we need to init the stack
        if (null === $this->contextFieldsStack) {
            $this->contextFieldsStack = [[]];
        }

        return array_key_last($this->contextFieldsStack);
    }

    /**
     * Restore the ShopContext, this is used when Shop has been overridden, we need to
     * restore context->shop of course But also the static fields in Shop class
     *
     * @param int $currentStashIndex
     */
    private function restoreShopContext(int $currentStashIndex): void
    {
        $shop = $this->contextFieldsStack[$currentStashIndex][self::FIELD_SHOP];
        $shopId = $shop instanceof Shop ? $shop->id : null;
        $shopContext = $this->contextFieldsStack[$currentStashIndex][self::FIELD_SHOP_CONTEXT];
        if (null !== $shopContext) {
            Shop::setContext($shopContext, $shopId);
        }
        unset($this->contextFieldsStack[$currentStashIndex]['shopContext']);
    }

    /**
     * Removes the last saved stashed context, in case this method is called too many times
     * we always keep one layer available
     */
    private function removeLastSavedContext(): void
    {
        array_pop($this->contextFieldsStack);

        // When all layers have been popped we restore the initial null value
        if (empty($this->contextFieldsStack)) {
            $this->contextFieldsStack = null;
        }
    }
}
