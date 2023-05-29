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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Category;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryTreeSelectorType extends CollectionType
{
    private const PROTOTYPE_INDEX_PLACEHOLDER = '__CATEGORY_INDEX__';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_categories', CategoryTagsCollectionType::class)
            ->add('category_tree', CollectionType::class, [
                'label' => false,
                'required' => false,
                'entry_type' => CheckboxType::class,
                'block_prefix' => 'category_tree_collection',
                'entry_options' => [
                    'block_prefix' => 'category_tree_entry',
                    'label' => false,
                    'attr' => [
                        'class' => 'category tree-checkbox-input',
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => self::PROTOTYPE_INDEX_PLACEHOLDER,
            ])
            ->add('apply_btn', ButtonType::class, [
                'label' => $this->translator->trans('Apply', [], 'Admin.Actions'),
                'attr' => [
                    'class' => 'js-apply-categories-btn btn-outline-primary',
                ],
            ])
            ->add('cancel_btn', ButtonType::class, [
                'label' => $this->translator->trans('Cancel', [], 'Admin.Actions'),
                'attr' => [
                    'class' => 'js-cancel-categories-btn btn-outline-secondary',
                ],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/categories.html.twig',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'category_tree_selector';
    }
}
