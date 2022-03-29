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

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

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
                    'Your shop\'s default carrier.',
                    'Admin.Shipping.Help'
                ),
                'multistore_configuration_key' => 'PS_CARRIER_DEFAULT',
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ],
            ])
            ->add('carrier_default_order_by', ChoiceType::class, [
                'choices' => $this->orderByChoices,
                'choice_translation_domain' => 'Admin.Global',
                'label' => $this->trans(
                    'Sort by',
                    'Admin.Actions'
                ),
                'help' => $this->trans(
                    'This will only be visible in the front office.',
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
                    'This will only be visible in the front office.',
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
