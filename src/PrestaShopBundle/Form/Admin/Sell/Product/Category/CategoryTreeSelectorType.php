<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                    'class' => 'js-apply-categories-btn btn-primary',
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
    public function configureOptions(OptionsResolver $resolver)
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
