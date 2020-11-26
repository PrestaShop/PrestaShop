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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BasicInformationType extends TranslatorAwareType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    $this->trans('Standard product', 'Admin.Catalog.Feature') => 0,
                    $this->trans('Pack of products', 'Admin.Catalog.Feature') => 1,
                    $this->trans('Virtual product', 'Admin.Catalog.Feature') => 2,
                ],
                'choice_translation_domain' => 'Admin.Catalog.Feature',
                'attr' => [
                    'class' => 'custom-select',
                ],
                'label' => $this->trans('Type', 'Admin.Catalog.Feature'),
                'help' => $this->trans('Is the product a pack (a combination of at least two existing products), a virtual product (downloadable file, service, etc.), or simply a standard, physical product?', 'Admin.Catalog.Help'),
                'required' => true,
            ])
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
            // This should be FormattedTextAreaType, waiting for #21115
            ->add('description_short', TranslatableType::class, [
                'label' => $this->trans('Summary', 'Admin.Global'),
                'type' => TextareaType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
            // This should be FormattedTextAreaType, waiting for #21115
            ->add('description', TranslatableType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'type' => TextareaType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
        ;
    }
}
