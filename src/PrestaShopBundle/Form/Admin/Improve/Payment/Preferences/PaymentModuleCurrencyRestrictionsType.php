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

use PrestaShop\PrestaShop\Adapter\Module\PaymentModuleListProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CurrencyByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Payment\PaymentModulePreferencesConfiguration;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialMultipleChoiceTableCardType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Defines Currency Restriction form in "Improve > Payment > Preferences" page.
 */
class PaymentModuleCurrencyRestrictionsType extends AbstractPaymentModuleRestrictionsType
{
    /**
     * @var CurrencyByIdChoiceProvider
     */
    private $currencyByIdChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param PaymentModuleListProvider $paymentModuleListProvider
     * @param CurrencyByIdChoiceProvider $currencyByIdChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        PaymentModuleListProvider $paymentModuleListProvider,
        CurrencyByIdChoiceProvider $currencyByIdChoiceProvider
    ) {
        parent::__construct($translator, $locales, $paymentModuleListProvider);

        $this->currencyByIdChoiceProvider = $currencyByIdChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currencyChoices = $this->currencyByIdChoiceProvider->getChoices();
        $builder
            ->add('currency_restrictions', MaterialMultipleChoiceTableCardType::class, [
                'label' => $this->trans('Currency restrictions', 'Admin.Payment.Feature'),
                'table_label' => $this->trans('Currency restrictions', 'Admin.Payment.Feature'),
                'table_icon' => 'euro_symbol',
                'help' => $this->trans(
                    'Please select available payment modules for each currency.',
                    'Admin.Payment.Help'
                ),
                'required' => false,
                'choices' => $this->getCurrencyChoices($currencyChoices),
                'multiple_choices' => $this->getCurrencyChoicesForPaymentModules($currencyChoices),
                'headers_fixed' => true,
            ]);
    }

    /**
     * Get multiple currency choices for payment modules.
     *
     * @param array $currencyChoices
     *
     * @return array
     */
    private function getCurrencyChoicesForPaymentModules(array $currencyChoices): array
    {
        $choices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $moduleInstance = $paymentModule->getInstance();

            $allowMultipleCurrencies = true;
            if ('radio' === $moduleInstance->currencies_mode) {
                $allowMultipleCurrencies = false;
                $currencyChoices = $this->getCurrencyChoices($currencyChoices);
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
     * @param array $currencyChoices
     *
     * @return array
     */
    private function getCurrencyChoices(array $currencyChoices): array
    {
        return array_merge(
            $currencyChoices,
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
            $this->trans('Customer currency', 'Admin.Payment.Feature') => PaymentModulePreferencesConfiguration::CUSTOMER_CURRENCY,
            $this->trans('Shop default currency', 'Admin.Payment.Feature') => PaymentModulePreferencesConfiguration::SHOP_DEFAULT_CURRENCY,
        ];
    }
}
