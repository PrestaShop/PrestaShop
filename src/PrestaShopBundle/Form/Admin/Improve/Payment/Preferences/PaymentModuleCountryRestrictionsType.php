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

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\PaymentModuleListProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CountryByIdChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialMultipleChoiceTableCardType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Defines Country Restriction form in "Improve > Payment > Preferences" page.
 */
class PaymentModuleCountryRestrictionsType extends AbstractPaymentModuleRestrictionsType
{
    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    /**
     * @var CountryByIdChoiceProvider
     */
    private $countryByIdChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param PaymentModuleListProvider $paymentModuleListProvider
     * @param CountryByIdChoiceProvider $countryByIdChoiceProvider
     * @param CountryDataProvider $countryDataProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        PaymentModuleListProvider $paymentModuleListProvider,
        CountryByIdChoiceProvider $countryByIdChoiceProvider,
        CountryDataProvider $countryDataProvider
    ) {
        parent::__construct($translator, $locales, $paymentModuleListProvider);

        $this->countryDataProvider = $countryDataProvider;
        $this->countryByIdChoiceProvider = $countryByIdChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $countryChoices = $this->countryByIdChoiceProvider->getChoices();
        $builder
            ->add('country_restrictions', MaterialMultipleChoiceTableCardType::class, [
                'label' => $this->trans('Country restrictions', 'Admin.Payment.Feature'),
                'table_label' => $this->trans('Country restrictions', 'Admin.Payment.Feature'),
                'table_icon' => 'public',
                'help' => $this->trans(
                    'Please select available payment modules for each country.',
                    'Admin.Payment.Help'
                ),
                'required' => false,
                'choices' => $countryChoices,
                'multiple_choices' => $this->getCountryChoicesForPaymentModules($countryChoices),
                'headers_fixed' => true,
            ]);
    }

    /**
     * Get multiple country choices for payment modules.
     *
     * @param array $countryChoices
     * @return array
     */
    private function getCountryChoicesForPaymentModules(array $countryChoices): array
    {
        $multipleChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $limitedCountries = $paymentModule->get('limited_countries');

            if (is_array($limitedCountries) && !empty($limitedCountries)) {
                $countryChoices = $this->getLimitedCountryChoices($limitedCountries, $countryChoices);
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
     * Get country choices by country ISO codes.
     *
     * @param array $limitedCountryIsoCodes
     * @param array $countryChoices
     *
     * @return array
     */
    private function getLimitedCountryChoices(array $limitedCountryIsoCodes, array $countryChoices): array
    {
        $limitedCountryChoices = [];
        foreach ($limitedCountryIsoCodes as $isoCode) {
            $countryId = $this->countryDataProvider->getIdByIsoCode($isoCode);
            $countryValueIndex = array_search($countryId, $countryChoices);
            if (false !== $countryId && false !== $countryValueIndex) {
                $limitedCountryChoices[] = $countryChoices[$countryValueIndex];
            }
        }

        return $limitedCountryChoices;
    }
}
