<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
