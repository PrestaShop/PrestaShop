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

namespace PrestaShopBundle\Form\Admin\Improve\Payment\Preferences;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialMultipleChoiceTableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PaymentModuleCurrencyRestrictionsType defines Currency Restriction form in "Improve > Payment > Preferences" page.
 */
class PaymentModuleCurrencyRestrictionsType extends PaymentModuleRestrictionsParentType
{
    public const CUSTOMER_CURRENCY = -1;
    public const SHOP_DEFAULT_CURRENCY = -2;
    /**
     * @var array
     */
    private $currencyChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $paymentModules
     * @param array $currencyChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $paymentModules,
        array $currencyChoices
    ) {
        parent::__construct($translator, $locales, $paymentModules);

        $this->currencyChoices = $currencyChoices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Currency restrictions', 'Admin.Payment.Feature'),
                'help' => $this->trans(
                    'Please select available payment modules for each currency.',
                    'Admin.Payment.Help'
                ),
                'required' => false,
                'choices' => $this->getCurrencyChoices(),
                'multiple_choices' => $this->getCurrencyChoicesForPaymentModules(),
                'headers_fixed' => true,
            ]);
    }

    /**
     * Get multiple currency choices for payment modules.
     *
     * @return array
     */
    private function getCurrencyChoicesForPaymentModules(): array
    {
        $choices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $moduleInstance = $paymentModule->getInstance();

            $allowMultipleCurrencies = true;
            $currencyChoices = $this->currencyChoices;
            if ('radio' === $moduleInstance->currencies_mode) {
                $allowMultipleCurrencies = false;
                $currencyChoices = $this->getCurrencyChoices();
            }

            $choices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => $allowMultipleCurrencies,
                'choices' => $currencyChoices,
            ];
        }

        return $choices;
    }

    /**
     * Get currency choices with specific additional choices.
     *
     * @return array
     */
    private function getCurrencyChoices(): array
    {
        return array_merge(
            $this->currencyChoices,
            $this->getAdditionalCurrencyChoices()
        );
    }

    /**
     * Get payment preferences specific currency choices.
     *
     * @return array<string, int>
     */
    private function getAdditionalCurrencyChoices(): array
    {
        return [
            $this->trans('Customer currency', 'Admin.Payment.Feature') => static::CUSTOMER_CURRENCY,
            $this->trans('Shop default currency', 'Admin.Payment.Feature') => static::SHOP_DEFAULT_CURRENCY,
        ];
    }
}
