<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use Cart;
use Context;
use Country;
use Currency;
use Customer;
use Language;

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
    /**
     * @var Context
     */
    private $context;

    /**
     * @var array
     */
    private $savedContextFields = [];

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
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
        $this->saveContextField('cart');
        $this->context->cart = $cart;

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
        $this->saveContextField('country');
        $this->context->country = $country;

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
        $this->saveContextField('currency');
        $this->context->currency = $currency;

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
        $this->saveContextField('language');
        $this->context->language = $language;

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
        $this->saveContextField('customer');
        $this->context->customer = $customer;

        return $this;
    }

    /**
     * Restores context to a state before changes
     *
     * @return self
     */
    public function restoreContext(): self
    {
        foreach ($this->savedContextFields as $fieldName => $contextValue) {
            $this->restoreContextField($fieldName);
        }

        return $this;
    }

    /**
     * Save context field into local array
     *
     * @param string $fieldName
     */
    private function saveContextField(string $fieldName)
    {
        // NOTE: array_key_exists important here, isset cannot be used because it would not detect if null is stored
        if (!array_key_exists($fieldName, $this->savedContextFields)) {
            $this->savedContextFields[$fieldName] = $this->context->$fieldName;
        }
    }

    /**
     * Restores context saved value, and remove save value from local array
     *
     * @param string $fieldName
     */
    private function restoreContextField(string $fieldName): void
    {
        // NOTE: array_key_exists important here, isset cannot be used because it would not detect if null is stored
        if (array_key_exists($fieldName, $this->savedContextFields)) {
            $this->context->$fieldName = $this->savedContextFields[$fieldName];
            unset($this->savedContextFields[$fieldName]);
        }
    }
}
