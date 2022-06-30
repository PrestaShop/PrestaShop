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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Form\Admin\Sell\Product\Pricing\SpecificPricePriorityType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class generates "General" form
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class GeneralType extends TranslatorAwareType
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        LegacyContext $legacyContext
    ) {
        parent::__construct($translator, $locales);
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('catalog_mode', SwitchType::class, [
                'label' => $this->trans('Catalog mode', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                        'Catalog mode disables the shopping cart on your store. Visitors will be able to browse your products catalog, but not buy them.',
                        'Admin.Shopparameters.Help'
                    ),
                'required' => false,
            ])
            ->add('catalog_mode_with_prices', SwitchType::class, [
                'label' => $this->trans('Show prices', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Display product prices when in catalog mode.',
                    'Admin.Shopparameters.Help'
                ) . '<br />' . $this->trans(
                    'To hide prices for a specific group, go to [1]Customer Settings > Groups[/1].',
                    'Admin.Shopparameters.Help',
                        [
                            '[1]' => sprintf(
                                '<a target="_blank" href="%s">',
                                $this->legacyContext->getAdminLink('AdminGroups')
                            ),
                            '[/1]' => '</a>',
                        ]
                ),
                'row_attr' => [
                    'class' => 'catalog-mode-option',
                ],
                'required' => false,
            ])
            ->add('new_days_number', IntegerType::class, [
                'label' => $this->trans(
                    'Number of days for which the product is considered \'new\'',
                    'Admin.Shopparameters.Feature'
                ),
                'required' => false,
            ])
            ->add('short_description_limit', IntegerType::class, [
                'label' => $this->trans(
                    'Max size of product summary',
                    'Admin.Shopparameters.Feature'
                ),
                'required' => false,
                'unit' => $this->trans('characters', 'Admin.Shopparameters.Help'),
            ])
            ->add('quantity_discount', ChoiceType::class, [
                'label' => $this->trans(
                    'Quantity discounts based on',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'How to calculate quantity discounts.',
                    'Admin.Shopparameters.Help'
                ),
                'choices' => [
                    'Products' => 0,
                    'Combinations' => 1,
                ],
                'choice_translation_domain' => 'Admin.Global',
                'placeholder' => false,
                'required' => false,
            ])
            ->add('force_friendly_url', SwitchType::class, [
                'label' => $this->trans(
                    'Force update of friendly URL',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'When active, friendly URL will be updated on every save.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
            ->add('default_status', SwitchType::class, [
                'label' => $this->trans(
                    'Activate new products by default',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Enable this option if you want to activate by default all your manually created new products.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
            ->add('specific_price_priorities', SpecificPricePriorityType::class, [
                'label' => $this->trans(
                    'Default order of priority for specific prices',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'If a customer meets multiple conditions, specific prices will be applied in this order of priority, unless a different order has been set for a particular product.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'product_preferences_general_block';
    }
}
