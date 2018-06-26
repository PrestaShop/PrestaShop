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
                'multiple_choices' => $multipleCurrencyChoices,
            ])
            ->add('country_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Country restrictions', 'Admin.Payment.Feature'),
                'multiple_choices' => $multipleCountryChoices,
            ])
            ->add('group_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Group restrictions', 'Admin.Payment.Feature'),
                'multiple_choices' => $multipleGroupChoices,
            ])
            ->add('carrier_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Carrier restrictions', 'Admin.Payment.Feature'),
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
            $allowMultiple = true;
            $currencyChoices = $this->currencyChoices;

            if ('radio' === $paymentModule->getInstance()->currencies_mode) {
                $allowMultiple = false;

                $currencyChoices[$this->trans('Customer currency', 'Admin.Payment.Feature')] = -1;
                $currencyChoices[$this->trans('Shop default currency', 'Admin.Payment.Feature')] = -2;
            }

            $multipleCurrencyChoices[] = [
                'id' => $paymentModule->get('name'),
                'name' => $paymentModule->get('displayName'),
                'allow_multiple' => $allowMultiple,
                'choices' => $currencyChoices,
            ];

            $multipleCountryChoices[] = [
                'id' => $paymentModule->get('name'),
                'name' => $paymentModule->get('displayName'),
                'allow_multiple' => true,
                'choices' => $this->countryChoices,
            ];

            $multipleGroupChoices[] = [
                'id' => $paymentModule->get('name'),
                'name' => $paymentModule->get('displayName'),
                'allow_multiple' => true,
                'choices' => $this->groupChoices,
            ];

            $multipleCarrierChoices[] = [
                'id' => $paymentModule->get('name'),
                'name' => $paymentModule->get('displayName'),
                'allow_multiple' => true,
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
}
