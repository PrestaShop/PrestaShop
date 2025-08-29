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

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type contains a list of elements represented in an input containing
 * tags. Each value is represented in the tag via its name an is associated its ID
 * value:
 *
 *  $taggedItems = [
 *      [
 *          'id' => 1,
 *          'name' => 'S',
 *      ],
 *      [
 *          'id' => 2,
 *          'name' => 'M',
 *      ],
 *  ];
 */
class TaggedItemCollectionType extends CollectionType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'block_prefix' => 'tagged_item_collection',
            'entry_type' => TaggedItemType::class,
            'entry_options' => [
                'label' => false,
                'block_prefix' => 'tagged_item_collection_entry',
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__TAGGED_ITEM_INDEX__',
            'attr' => [
                'class' => 'tagged-item-collection',
            ],
        ]);
    }
}
