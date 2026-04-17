<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Preferences;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class generates "Handling" form
 * in "Improve > Shipping > Preferences" page.
 */
class CarrierOptionsType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $carriers;

    /**
     * @var array
     */
    private $orderByChoices;

    /**
     * @var array
     */
    private $orderWayChoices;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $carriers,
        array $orderByChoices,
        array $orderWayChoices
    ) {
        parent::__construct($translator, $locales);

        $this->carriers = $carriers;
        $this->orderByChoices = $orderByChoices;
        $this->orderWayChoices = $orderWayChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $carrierChoices = array_merge([
            'Best price' => (int) -1,
            'Best grade' => (int) -2,
        ], $this->carriers);

        $builder
            ->add('default_carrier', ChoiceType::class, [
                'choices' => $carrierChoices,
                'label' => $this->trans(
                    'Default carrier',
                    'Admin.Shipping.Feature'
                ),
                'help' => $this->trans(
                    'Default carrier that will be pre-selected for customers in the checkout process.',
                    'Admin.Shipping.Help'
                ),
                'multistore_configuration_key' => 'PS_CARRIER_DEFAULT',
                'autocomplete' => true,
            ])
            ->add('carrier_default_order_by', ChoiceType::class, [
                'choices' => $this->orderByChoices,
                'choice_translation_domain' => 'Admin.Global',
                'label' => $this->trans(
                    'Sort by',
                    'Admin.Actions'
                ),
                'help' => $this->trans(
                    'Determines how carriers are sorted in the checkout.',
                    'Admin.Shipping.Help'
                ),
                'multistore_configuration_key' => 'PS_CARRIER_DEFAULT_SORT',
            ])
            ->add('carrier_default_order_way', ChoiceType::class, [
                'choices' => $this->orderWayChoices,
                'choice_translation_domain' => 'Admin.Global',
                'label' => $this->trans(
                    'Order by',
                    'Admin.Actions'
                ),
                'help' => $this->trans(
                    'Sets the sorting direction of carriers in the checkout.',
                    'Admin.Shipping.Help'
                ),
                'multistore_configuration_key' => 'PS_CARRIER_DEFAULT_ORDER',
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
        return 'shipping_preferences_carrier_options_block';
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
