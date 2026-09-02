<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form uses collection of combination items and so it can be rendered as a grid consisting of submittable inputs.
 */
class CombinationListType extends CollectionType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'entry_type' => CombinationItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__COMBINATION_INDEX__',
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/combination_listing.html.twig',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'combination_list';
    }
}
