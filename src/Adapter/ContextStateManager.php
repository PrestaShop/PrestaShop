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
     * @var Cart|null
     */
    private $cart;

    /**
     * @var Country|null
     */
    private $country;

    /**
     * @var Currency|null
     */
    private $currency;

    /**
     * @var Language|null
     */
    private $language;

    /**
     * @var Customer|null
     */
    private $customer;

    /**
     * @var bool
     */
    private $cartStateChanged = false;

    /**
     * @var bool
     */
    private $countryStateChanged = false;

    /**
     * @var bool
     */
    private $currencyStateChanged = false;

    /**
     * @var bool
     */
    private $languageStateChanged = false;

    /**
     * @var bool
     */
    private $customerStateChanged = false;

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
        $this->cart = $this->context->cart;
        $this->context->cart = $cart;
        $this->cartStateChanged = true;

        return $this;
    }

    /**
     * Sets context country and saves previous value
     *
     * @param Country|null $country
     *
     * @return $this
     */
    public function setCountry(?Country $country):self
    {
        $this->country = $this->context->country;
        $this->context->country = $country;
        $this->countryStateChanged = true;

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
        $this->currency = $this->context->currency;
        $this->context->currency = $currency;
        $this->currencyStateChanged = true;

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
        $this->language = $this->context->language;
        $this->context->language = $language;
        $this->languageStateChanged = true;

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
        $this->customer = $this->context->customer;
        $this->context->customer = $customer;
        $this->customerStateChanged = true;

        return $this;
    }

    /**
     * Restores context to a state before changes
     */
    public function restoreContext(): void
    {
        if ($this->cartStateChanged) {
            $this->context->cart = $this->cart;
        }

        if ($this->currencyStateChanged) {
            $this->context->currency = $this->currency;
        }

        if ($this->languageStateChanged) {
            $this->context->language = $this->language;
        }

        if ($this->customerStateChanged) {
            $this->context->customer = $this->customer;
        }

        if ($this->countryStateChanged) {
            $this->context->country = $this->country;
        }
    }
}
