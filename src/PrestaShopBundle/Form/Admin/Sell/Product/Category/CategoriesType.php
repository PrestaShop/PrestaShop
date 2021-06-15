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

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\UnavailableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriesType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_categories', CategoriesCollectionType::class)
            ->add('create_category', UnavailableType::class, [
                'label' => $this->trans('Create a new category', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'label_help_box' => $this->trans('If you want to quickly create a new category, you can do it here. Don’t forget to then go to the Categories page to fill in the needed details (description, image, etc.). A new category will not automatically appear in your shop\'s menu, please read the Help about it.', 'Admin.Catalog.Help'),
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
            'label' => false,
            'required' => false,
        ]);
    }
}
