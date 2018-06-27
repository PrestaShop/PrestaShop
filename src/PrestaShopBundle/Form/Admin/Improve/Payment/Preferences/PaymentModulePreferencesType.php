<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\Payment\Preferences;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialMultipleChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
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
    private $currencyChoices;

    /**
     * @var array
     */
    private $paymentModules;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $paymentModules
     * @param array $countryChoices
     * @param array $groupChoices
     * @param array $carrierChoices
     * @param array $currencyChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $paymentModules,
        array $countryChoices,
        array $groupChoices,
        array $carrierChoices,
        array $currencyChoices
    ) {
        parent::__construct($translator, $locales);

        $this->countryChoices = $countryChoices;
        $this->groupChoices = $groupChoices;
        $this->carrierChoices = $carrierChoices;
        $this->currencyChoices = $currencyChoices;
        $this->paymentModules = $paymentModules;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        list(
            $multipleCurrencyChoices,
            $multipleCountryChoices,
            $multipleGroupChoices,
            $multipleCarrierChoices
        ) = $this->getMultipleChoicesForPaymentModules();

        $builder
            ->add('currency_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Currency restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->getCurrencyChoices(),
                'multiple_choices' => $multipleCurrencyChoices,
            ])
            ->add('country_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Country restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->countryChoices,
                'multiple_choices' => $multipleCountryChoices,
            ])
            ->add('group_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Group restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->groupChoices,
                'multiple_choices' => $multipleGroupChoices,
            ])
            ->add('carrier_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Carrier restrictions', 'Admin.Payment.Feature'),
                'choices' => $this->carrierChoices,
                'multiple_choices' => $multipleCarrierChoices,
            ])
        ;
    }

    /**
     * Get multiple choices for payment preferences form
     *
     * @return array
     */
    private function getMultipleChoicesForPaymentModules()
    {
        $multipleCurrencyChoices = [];
        $multipleCountryChoices = [];
        $multipleGroupChoices = [];
        $multipleCarrierChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $moduleInstance = $paymentModule->getInstance();

            $allowMultipleCurrencies = true;
            $currencyChoices = $this->currencyChoices;

            if ('radio' === $moduleInstance->currencies_mode) {
                $allowMultipleCurrencies = false;

                $currencyChoices = array_merge(
                    $currencyChoices,
                    $this->getAdditionalCurrencyChoices()
                );
            }

            $multipleCurrencyChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => $allowMultipleCurrencies,
                'choices' => $currencyChoices,
            ];

            if (is_array($moduleInstance->limited_countries) &&
                !empty($moduleInstance->limited_countries)
            ) {
                $countryChoices = [
                    'Albania' => '230',
                    'Angola' => '41',
                    'Lithuania' => '131',
                    'Latvia' => '125',
                    'Poland' => '14',
                ];
            } else {
                $countryChoices = $this->countryChoices;
            }

            $multipleCountryChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $countryChoices,
            ];

            $multipleGroupChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $this->groupChoices,
            ];

            $multipleCarrierChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $this->carrierChoices,
            ];
        }

        return [
            $multipleCurrencyChoices,
            $multipleCountryChoices,
            $multipleGroupChoices,
            $multipleCarrierChoices,
        ];
    }

    private function getCurrencyChoices()
    {
        return array_merge(
            $this->currencyChoices,
            $this->getAdditionalCurrencyChoices()
        );
    }

    private function getAdditionalCurrencyChoices()
    {
        return [
            $this->trans('Customer currency', 'Admin.Payment.Feature') => -1,
            $this->trans('Shop default currency', 'Admin.Payment.Feature') => -2,
        ];
    }
}
