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

namespace PrestaShopBundle\Form\Admin\Improve\Payment\Preferences;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CurrencyByIdChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialMultipleChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @deprecated should be removed in next major version in favor of multiple types
 * Class PaymentModulePreferencesType defines form in "Improve > Payment > Preferences" page
 */
class PaymentModulePreferencesType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $countryChoices;

    /**
     * @var array
     */
    private $groupChoices;

    /**
     * @var array
     */
    private $carrierChoices;

    /**
     * @var array
     */
    private $paymentModules;

    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;
    /**
     * @var CurrencyByIdChoiceProvider
     */
    private $currencyChoicesProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $paymentModules
     * @param array $countryChoices
     * @param array $groupChoices
     * @param array $carrierChoices
     * @param CurrencyByIdChoiceProvider $currencyChoicesProvider
     * @param CountryDataProvider $countryDataProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $paymentModules,
        array $countryChoices,
        array $groupChoices,
        array $carrierChoices,
        CurrencyByIdChoiceProvider $currencyChoicesProvider,
        CountryDataProvider $countryDataProvider
    ) {
        parent::__construct($translator, $locales);

        $this->countryChoices = $countryChoices;
        $this->groupChoices = $groupChoices;
        $this->carrierChoices = $carrierChoices;
        $this->paymentModules = $this->sortPaymentModules($paymentModules);
        $this->countryDataProvider = $countryDataProvider;
        $this->currencyChoicesProvider = $currencyChoicesProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currency_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Currency restrictions', 'Admin.Payment.Feature'),
                'table_label' => $this->trans('Currency restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->getCurrencyChoices(),
                'multiple_choices' => $this->getCurrencyChoicesForPaymentModules(),
                'help' => $this->trans(
                    'Please mark each checkbox for the currency, or currencies, for which you want the payment module(s) to be available.',
                    'Admin.Payment.Help'
                ),
                'required' => false,
                'headers_fixed' => true,
            ])
            ->add('country_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Country restrictions', 'Admin.Payment.Feature'),
                'table_label' => $this->trans('Country restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->countryChoices,
                'multiple_choices' => $this->getCountryChoicesForPaymentModules(),
                'help' => $this->trans(
                    'Please select available payment modules for each country.',
                    'Admin.Payment.Help'
                ),
                'required' => false,
                'headers_fixed' => true,
            ])
            ->add('group_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Group restrictions', 'Admin.Payment.Feature'),
                'table_label' => $this->trans('Group restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->groupChoices,
                'multiple_choices' => $this->getGroupChoicesForPaymentModules(),
                'help' => $this->trans(
                    'Please select available payment modules for each customer group.',
                    'Admin.Payment.Help'
                ),
                'required' => false,
                'headers_fixed' => true,
            ])
            ->add('carrier_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Carrier restrictions', 'Admin.Payment.Feature'),
                'table_label' => $this->trans('Carrier restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->carrierChoices,
                'multiple_choices' => $this->getCarrierChoicesForPaymentModules(),
                'help' => $this->trans(
                    'Please select available payment modules for each carrier.',
                    'Admin.Payment.Help'
                ),
                'required' => false,
                'headers_fixed' => true,
            ]);
    }

    /**
     * Get multiple currency choices for payment modules.
     *
     * @return array
     */
    private function getCurrencyChoicesForPaymentModules()
    {
        $choices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $moduleInstance = $paymentModule->getInstance();

            if ('radio' === $moduleInstance->currencies_mode) {
                $allowMultipleCurrencies = false;
                $currencyChoices = $this->getCurrencyChoices();
            } else {
                $allowMultipleCurrencies = true;
                $currencyChoices = $this->currencyChoicesProvider->getChoices();
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
     * Get multiple country choices for payment modules.
     *
     * @return array
     */
    private function getCountryChoicesForPaymentModules()
    {
        $multipleChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $limitedCountries = $paymentModule->get('limited_countries');

            if (is_array($limitedCountries) && !empty($limitedCountries)) {
                $countryChoices = $this->getLimitedCountryChoices($limitedCountries);
            } else {
                $countryChoices = $this->countryChoices;
            }

            $multipleChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $countryChoices,
            ];
        }

        return $multipleChoices;
    }

    /**
     * Get multiple group choices for payment modules.
     *
     * @return array
     */
    private function getGroupChoicesForPaymentModules()
    {
        $groupChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $groupChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $this->groupChoices,
            ];
        }

        return $groupChoices;
    }

    /**
     * Get multiple carrier choices for payment modules.
     *
     * @return array
     */
    private function getCarrierChoicesForPaymentModules()
    {
        $carrierChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $carrierChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $this->carrierChoices,
            ];
        }

        return $carrierChoices;
    }

    /**
     * Get currency choices with specific addtional choices.
     *
     * @return array
     */
    private function getCurrencyChoices()
    {
        return array_merge(
            $this->currencyChoicesProvider->getChoices(),
            $this->getAdditionalCurrencyChoices()
        );
    }

    /**
     * Get payment preferences specific currency choices.
     *
     * @return array
     */
    private function getAdditionalCurrencyChoices()
    {
        return [
            $this->trans('Customer currency', 'Admin.Payment.Feature') => -1,
            $this->trans('Shop default currency', 'Admin.Payment.Feature') => -2,
        ];
    }

    /**
     * Get country choices by country ISO codes.
     *
     * @param array $limitedCountryIsoCodes
     *
     * @return array
     */
    private function getLimitedCountryChoices(array $limitedCountryIsoCodes)
    {
        $countryChoices = [];

        foreach ($limitedCountryIsoCodes as $isoCode) {
            $countryId = $this->countryDataProvider->getIdByIsoCode($isoCode);
            $countryValueIndex = array_search($countryId, $this->countryChoices);
            if (false !== $countryId && false !== $countryValueIndex) {
                $countryChoices[] = $this->countryChoices[$countryValueIndex];
            }
        }

        return $countryChoices;
    }

    /**
     * Sort payment modules by display name.
     *
     * @param array $paymentModules
     *
     * @return array
     */
    private function sortPaymentModules(array $paymentModules)
    {
        $sortingBy = [];

        foreach ($paymentModules as $key => $paymentModule) {
            $sortingBy[$key] = $paymentModule->get('displayName');
        }

        array_multisort($sortingBy, SORT_ASC, $paymentModules);

        return $paymentModules;
    }
}
