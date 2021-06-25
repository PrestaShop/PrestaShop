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

namespace PrestaShopBundle\Form\Admin\Sell\Category;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class QuickAddCategoryType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('New category name', 'Admin.Catalog.Feature'),
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_CATALOG_NAME),
                    ],
                ],
            ])
            //@todo; extract to some CategorySearch component?
            ->add('search_category', TextType::class, [
                'label' => $this->trans('Parent of the category', 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search categories', 'Admin.Catalog.Help'),
                    'class' => 'autocomplete search ui-autocomplete-input',
                ],
            ])
            ->add('parent_id', HiddenType::class, [
                'required' => true,
            ])
            ->add('cancel', ButtonType::class, [
                'label' => $this->trans('Cancel', 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn btn-outline-secondary cancel-btn',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->trans('Save', 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn btn-primary submit-btn float-right',
                ],
            ])
        ;
    }
}
