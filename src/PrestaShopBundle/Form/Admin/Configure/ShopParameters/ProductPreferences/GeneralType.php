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

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TextWithUnitType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class generates "General" form
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class GeneralType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('catalog_mode', SwitchType::class,
            [
                'label' => $this->trans('Catalog mode', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Catalog mode disables the shopping cart on your store. Visitors will be able to browse your products catalog, but not buy them.', 'Admin.Shopparameters.Help')
                    . '<br>' .
                    $this->trans('Have specific needs? Edit particular groups to let them see prices or not.', 'Admin.Shopparameters.Help'),
            ])
            ->add('catalog_mode_with_prices', SwitchType::class, [
                'row_attr' => [
                    'class' => 'catalog-mode-option',
                ],
                'label' => $this->trans('Show prices', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Display product prices when in catalog mode.', 'Admin.Shopparameters.Help'),
            ])
            ->add('new_days_number', IntegerType::class, [
                'required' => false,
                'label' => $this->trans('Number of days for which the product is considered \'new\'', 'Admin.Shopparameters.Feature'),
                'constraints' => [
                    new Type(
                        [
                            'value' => 'numeric',
                            'message' => $this->trans('The field is invalid. Please enter a positive integer number.', 'Admin.Notifications.Error'),
                        ]
                    ),
                    new GreaterThanOrEqual(
                        [
                            'value' => 0,
                            'message' => $this->trans('The field is invalid. Please enter a positive integer number.', 'Admin.Notifications.Error'),
                        ]
                    ),
                ],
            ])
            ->add('short_description_limit', TextWithUnitType::class, [
                'label' => $this->trans('Max size of product summary', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Set the maximum size of the summary of your product description (in characters).', 'Admin.Shopparameters.Help'),
                'constraints' => [
                    new Type(
                        [
                            'value' => 'numeric',
                            'message' => $this->trans('The field is invalid. Please enter a positive integer number.', 'Admin.Notifications.Error'),
                        ]
                    ),
                    new GreaterThanOrEqual(
                        [
                            'value' => 0,
                            'message' => $this->trans('The field is invalid. Please enter a positive integer number.', 'Admin.Notifications.Error'),
                        ]
                    ),
                ],
                'required' => false,
                'unit' => $this->trans('characters', 'Admin.Shopparameters.Help'),
            ])
            ->add('quantity_discount', ChoiceType::class, [
                'label' => $this->trans('Quantity discounts based on', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('How to calculate quantity discounts.', 'Admin.Shopparameters.Help'),
                'choices' => [
                    'Products' => 0,
                    'Combinations' => 1,
                ],
                'choice_translation_domain' => 'Admin.Global',
                'required' => true,
            ])
            ->add('force_friendly_url', SwitchType::class, [
                'label' => $this->trans('Force update of friendly URL', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('When active, friendly URL will be updated on every save.', 'Admin.Shopparameters.Help'),
            ])
            ->add('default_status', SwitchType::class, [
                'label' => $this->trans('Default activation status', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('When active, new products will be activated by default during creation.', 'Admin.Shopparameters.Help'),
            ]);
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
