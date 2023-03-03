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

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Preferences;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShopBundle\Form\Admin\Type\MoneyWithSuffixType;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class generates "Handling" form
 * in "Improve > Shipping > Preferences" page.
 */
class HandlingType extends TranslatorAwareType
{
    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurationInterface $configuration,
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        parent::__construct($translator, $locales);

        $this->currencyDataProvider = $currencyDataProvider;
        $this->configuration = $configuration;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultCurrencyIsoCode = $this->currencyDataProvider->getDefaultCurrencyIsoCode();
        $weightUnit = $this->configuration->get('PS_WEIGHT_UNIT');

        $builder
            ->add('shipping_handling_charges', MoneyWithSuffixType::class, [
                'currency' => $defaultCurrencyIsoCode,
                'suffix' => $this->trans('(tax excl.)', 'Admin.Global'),
                'required' => false,
                'empty_data' => '0',
                'constraints' => [
                    new GreaterThanOrEqual(['value' => '0']),
                    new Type(['type' => 'numeric']),
                ],
                'label' => $this->trans(
                    'Handling charges',
                    'Admin.Shipping.Feature'
                ),
                'multistore_configuration_key' => 'PS_SHIPPING_HANDLING',
            ])
            ->add('free_shipping_price', MoneyType::class, [
                'currency' => $defaultCurrencyIsoCode,
                'required' => false,
                'empty_data' => '0',
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0]),
                    new Type(['type' => 'numeric']),
                ],
                'label' => $this->trans(
                    'Free shipping starts at',
                    'Admin.Shipping.Feature'
                ),
                'multistore_configuration_key' => 'PS_SHIPPING_FREE_PRICE',
            ])
            ->add('free_shipping_weight', NumberType::class, [
                'unit' => $weightUnit,
                'required' => false,
                'empty_data' => '0',
                'label' => $this->trans(
                    'Free shipping starts at',
                    'Admin.Shipping.Feature'
                ),
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0]),
                    new Type(['type' => 'numeric']),
                ],
                'multistore_configuration_key' => 'PS_SHIPPING_FREE_WEIGHT',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shipping.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shipping_preferences_handling_block';
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
