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

use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductCategoryType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextPreviewType::class, [
                'preview_class' => 'category-name-preview',
                'attr' => [
                    'class' => 'category-name-input',
                ],
//@todo:
//      The whole problem that in js category tree component communicates with tags component by collecting input names
//      So when I change the name in php side, it all renders fine on page load, but name becomes the breadcrumb, and i cannot compare
//      previoslySelectedCategory.name with new selectedCategoryName because one will have actual name and another will contain breadcrumb
//      so its kind of a loophole. I need some additional var "displayName" which would be decided by comparing names.Thought the preview component automatically
//      But the preview component renders the input value same as preview.
//
//      So the idea here was to provide custom value for a preview section (which could be a breadcrumb)
//      then in js i could compare categories by name which stays in input value and show breadcrumbs as preview
//      but this doesnt seem to ever work because data provider does not provide options.
//      i could maybe try custom rendering in twig depending if data provider returns something like "isDuplicateName" in each category
//      but that also sounds stupid
//                'custom_preview_value' =>
            ])
            ->add('breadcrumb', HiddenType::class, [
                'attr' => [
                    'class' => 'category-breadcrumb',
                ],
            ])
            ->add('id', HiddenType::class)
        ;
    }
}
