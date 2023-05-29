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

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShopBundle\Form\Admin\Type\ButtonCollectionType;
use PrestaShopBundle\Form\Admin\Type\ShopSelectorType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is used to copy data from one shop to some others, you can also unselect/remove some
 * shops. The content of the shop is based on the product initial shops and the whole list of selectable
 * shops.
 */
class ProductShopsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source_shop_id', HiddenType::class)
            ->add('initial_shops', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'label' => false,
            ])
            ->add('selected_shops', ShopSelectorType::class, [
                'multiple' => true,
            ])
            ->add('buttons', ButtonCollectionType::class, [
                'buttons' => [
                    'cancel' => [
                        'type' => ButtonType::class,
                        'group' => 'left',
                        'options' => [
                            'label' => $this->trans('Cancel', 'Admin.Global'),
                            'attr' => [
                                'class' => 'btn-secondary',
                            ],
                        ],
                    ],
                    'submit' => [
                        'type' => SubmitType::class,
                        'group' => 'right',
                        'options' => [
                            'label' => $this->trans('Save', 'Admin.Global'),
                        ],
                    ],
                ],
                'justify_content' => 'flex-end',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/product.html.twig',
            'use_default_themes' => false,
        ]);
    }
}
