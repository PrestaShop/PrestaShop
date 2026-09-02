<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                'help' => $this->trans(
                    'Optional additional fee added to carriers with handling charges enabled. This fee is not applied when shipping is free due to options below.',
                    'Admin.Shipping.Help'
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
                'help' => $this->trans(
                    'Shipping is free for all zones when the cart total reaches or exceeds this amount.',
                    'Admin.Shipping.Help'
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
                'help' => $this->trans(
                    'Shipping is free for all zones when the total cart weight reaches or exceeds this value.',
                    'Admin.Shipping.Help'
                ),
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
