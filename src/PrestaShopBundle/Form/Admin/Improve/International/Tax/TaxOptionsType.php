<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Tax;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Defines "Improve > International > Taxes" options form
 */
class TaxOptionsType extends AbstractType
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * TaxOptionsType constructor.
     *
     * @param bool $ecoTaxEnabled
     * @param FormChoiceProviderInterface $taxAddressTypeChoiceProvider
     * @param FormChoiceProviderInterface $taxRuleGroupChoiceProvider
     * @param TranslatorInterface $translator
     */
    public function __construct(
        $ecoTaxEnabled,
        FormChoiceProviderInterface $taxAddressTypeChoiceProvider,
        FormChoiceProviderInterface $taxRuleGroupChoiceProvider,
        TranslatorInterface $translator
    ) {
        $this->ecoTaxEnabled = $ecoTaxEnabled;
        $this->taxAddressTypeChoiceProvider = $taxAddressTypeChoiceProvider;
        $this->taxRuleGroupChoiceProvider = $taxRuleGroupChoiceProvider;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('enable_tax', SwitchType::class, [
            'label' => $this->translator->trans('Enable tax', [], 'Admin.International.Feature'),
            'help' => $this->translator->trans(
                'Select whether or not to include tax on purchases.',
                [],
                'Admin.International.Help'
            ),
            'required' => false,
            'attr' => [
                'class' => 'js-enable-tax',
            ],
        ])
            ->add('display_tax_in_cart', SwitchType::class, [
                'label' => $this->translator->trans(
                    'Display tax in the shopping cart',
                    [],
                    'Admin.International.Feature'
                ),
                'help' => $this->translator->trans(
                    'Select whether or not to display tax on a distinct line in the cart.',
                    [],
                    'Admin.International.Help'
                ),
                'required' => false,
                'attr' => [
                    'class' => 'js-display-in-cart',
                ],
            ])
            ->add('tax_address_type', ChoiceType::class, [
                'label' => $this->translator->trans('Based on', [], 'Admin.International.Feature'),
                'required' => false,
                'choices' => $this->taxAddressTypeChoiceProvider->getChoices(),
            ])
            ->add('use_eco_tax', SwitchType::class, [
                'label' => $this->translator->trans('Use ecotax', [], 'Admin.International.Feature'),
                'required' => false,
                'help' => $this->translator->trans(
                    'If you disable the ecotax, the ecotax for all your products will be set to 0.',
                    [],
                    'Admin.International.Feature'),
            ])
        ;

        if ($this->ecoTaxEnabled) {
            $builder->add('eco_tax_rule_group', ChoiceType::class, [
                'label' => $this->translator->trans(
                    'Ecotax',
                    [],
                    'Admin.International.Feature'),
                'help' => $this->translator->trans(
                    'Define the ecotax (e.g. French ecotax: 19.6%).',
                    [],
                    'Admin.International.Help'),
                'choices' => $this->taxRuleGroupChoiceProvider->getChoices(),
            ]);
        }
    }
}
