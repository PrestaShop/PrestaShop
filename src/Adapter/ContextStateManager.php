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
    private $stashedContextsFields = [[]];

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
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
        $currentStashIndex = count($this->stashedContextsFields) - 1;
        foreach ($this->stashedContextsFields[$currentStashIndex] as $fieldName => $contextValue) {
            $this->restoreContextField($fieldName);
        }
        $this->popContext();

        return $this;
    }

    /**
     * Saves the current overridden fields in the context, allowing you to set new values to the
     * current Context. Next time you call restoreContext only newly modified fields are restored
     * so you end up in the same state as when you called stashContext.
     *
     * This is useful if several services use the ContextStateManager, this way if every service
     * stashed the context before modifying it there is no risk of removing previous modifications
     * when you restore the context because the different states have been stacked.
     *
     * @return $this
     */
    public function stashContext(): self
    {
        // Add a new empty layer
        $this->stashedContextsFields[] = [];

        return $this;
    }

    /**
     * Save context field into local array
     *
     * @param string $fieldName
     */
    private function saveContextField(string $fieldName)
    {
        $currentStashIndex = count($this->stashedContextsFields) - 1;
        // NOTE: array_key_exists important here, isset cannot be used because it would not detect if null is stored
        if (!array_key_exists($fieldName, $this->stashedContextsFields[$currentStashIndex])) {
            $this->stashedContextsFields[$currentStashIndex][$fieldName] = $this->context->$fieldName;
        }
    }

    /**
     * Restores context saved value, and remove save value from local array
     *
     * @param string $fieldName
     */
    private function restoreContextField(string $fieldName): void
    {
        $currentStashIndex = count($this->stashedContextsFields) - 1;
        // NOTE: array_key_exists important here, isset cannot be used because it would not detect if null is stored
        if (array_key_exists($fieldName, $this->stashedContextsFields[$currentStashIndex])) {
            $this->context->$fieldName = $this->stashedContextsFields[$currentStashIndex][$fieldName];
            unset($this->stashedContextsFields[$currentStashIndex][$fieldName]);
        }
    }

    /**
     * Removes the last saved stashed context, in case this method is called too many times
     * we always keep one layer available
     */
    private function popContext(): void
    {
        array_pop($this->stashedContextsFields);

        // Always keep at least one layer (in case we pop too many)
        if (empty($this->stashedContextsFields)) {
            $this->stashedContextsFields[] = [];
        }
    }
}
