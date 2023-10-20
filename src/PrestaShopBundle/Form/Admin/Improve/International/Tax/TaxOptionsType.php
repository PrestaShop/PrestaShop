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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Tax;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Defines "Improve > International > Taxes" options form
 */
class TaxOptionsType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $ecoTaxEnabled;

    /**
     * @var FormChoiceProviderInterface
     */
    private $taxAddressTypeChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $taxRuleGroupChoiceProvider;

    /**
     * TaxOptionsType constructor.
     *
     * Backwards compatibility break introduced in 1.7.8.0 due to extension of TranslatorAwareType
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $ecoTaxEnabled
     * @param FormChoiceProviderInterface $taxAddressTypeChoiceProvider
     * @param FormChoiceProviderInterface $taxRuleGroupChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $ecoTaxEnabled,
        FormChoiceProviderInterface $taxAddressTypeChoiceProvider,
        FormChoiceProviderInterface $taxRuleGroupChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->ecoTaxEnabled = $ecoTaxEnabled;
        $this->taxAddressTypeChoiceProvider = $taxAddressTypeChoiceProvider;
        $this->taxRuleGroupChoiceProvider = $taxRuleGroupChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'enable_tax',
                SwitchType::class,
                [
                    'label' => $this->trans('Enable tax', 'Admin.International.Feature'),
                    'help' => $this->trans(
                        'Select whether or not to include tax on purchases.',
                        'Admin.International.Help'
                    ),
                    'required' => false,
                    'attr' => [
                        'class' => 'js-enable-tax',
                    ],
                    'multistore_configuration_key' => 'PS_USE_ECOTAX',
                ]
            )
            ->add(
                'display_tax_in_cart',
                SwitchType::class,
                [
                    'label' => $this->trans(
                        'Display tax in the shopping cart',
                        'Admin.International.Feature'
                    ),
                    'help' => $this->trans(
                        'Select whether or not to display tax on a distinct line in the cart.',
                        'Admin.International.Help'
                    ),
                    'empty_data' => false,
                    'required' => false,
                    'attr' => [
                        'class' => 'js-display-in-cart',
                    ],
                    'multistore_configuration_key' => 'PS_TAX_DISPLAY',
                ]
            )
            ->add('tax_address_type', ChoiceType::class, [
                'label' => $this->trans('Based on', 'Admin.International.Feature'),
                'required' => false,
                'placeholder' => false,
                'choices' => $this->taxAddressTypeChoiceProvider->getChoices(),
                'multistore_configuration_key' => 'PS_TAX_ADDRESS_TYPE',
            ])
            ->add('use_eco_tax', SwitchType::class, [
                'label' => $this->trans('Use ecotax', 'Admin.International.Feature'),
                'required' => false,
                'help' => $this->trans(
                    'If you disable the ecotax, the ecotax for all your products will be set to 0.',
                    'Admin.International.Help'),
                'multistore_configuration_key' => 'PS_USE_ECOTAX',
            ])
            ->add('eco_tax_rule_group', ChoiceType::class, [
                'label' => $this->trans(
                    'Ecotax',
                    'Admin.International.Feature'),
                'help' => $this->trans(
                    'Define the ecotax (e.g. French ecotax: 20%).',
                    'Admin.International.Help'),
                'choices' => $this->taxRuleGroupChoiceProvider->getChoices(),
                'multistore_configuration_key' => 'PS_ECOTAX_TAX_RULES_GROUP_ID',
                'row_attr' => [
                    'class' => 'editEcoTaxRuleGroup' . ($this->ecoTaxEnabled ? '' : ' d-none'),
                ],
            ]);
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
