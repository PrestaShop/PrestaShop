<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type handles a selection of grouped values (ex: AttributeGroup and their
 * Attributes, Feature and their FeatureValue)
 *
 * The form type includes two imbricated collection, each group is rendered via an input
 * composed of tags that represent their values, the tags are removable from the input directly.
 *
 * To select the grouped items you can click on the associated button which open a modal handled
 * by a Vue app that allows selecting in groups, toggle all the values of a group and a search input
 * for all non-selected values.
 *
 * The form type expects an array of groups with their ID and name (even if they may not always be necessary
 * while saving the data), and a sub-array containing the item values with their name and ID:
 *
 *  $groupedItemData = [
 *      2 => [
 *          'id' => 2,
 *          'name' => 'Color',
 *          'items' => [
 *              'id' => 10,
 *              'name' => 'Red',
 *          ],
 *          [
 *              'id' => 11,
 *              'name' => 'Black',
 *          ],
 *      ],
 *      3 => [
 *          'id' => 3,
 *          'name' => 'Dimension',
 *          'items' => [
 *              'id' => 19,
 *              'name' => '40x60cm',
 *          ],
 *          [
 *              'id' => 21,
 *              'name' => '80x120cm',
 *          ],
 *      ],
 *  ];
 */
class GroupedItemCollectionType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groups', CollectionType::class, [
                'entry_type' => GroupedItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'prototype_name' => '__GROUPED_ITEM_COLLECTION_INDEX__',
                'prototype_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'class' => 'grouped-item-collection',
                    'data-prototype-name' => '__GROUPED_ITEM_COLLECTION_INDEX__',
                    'data-translations' => json_encode([
                        'group.select-all' => $options['modal_select_group_label'],
                        'search.placeholder' => $options['modal_search_placeholder'],
                        'modal.title' => $options['modal_title'],
                        'modal.close' => $options['modal_close_label'],
                        'modal.loading' => $options['modal_loading'],
                        'select.action' => $options['modal_select_label'],
                    ]),
                ],
            ])
            ->add('select_items', ButtonType::class, [
                'label' => $options['select_button_label'],
                'attr' => [
                    'class' => 'btn-default grouped-item-collection-modal-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Items', 'Admin.Global'),
            'select_button_label' => $this->trans('Select items', 'Admin.Global'),
            'modal_select_group_label' => $this->trans('Select all values ({valuesNb})', 'Admin.Actions'),
            'modal_search_placeholder' => $this->trans('Search for item...', 'Admin.Actions'),
            'modal_title' => $this->trans('Select items', 'Admin.Actions'),
            'modal_close_label' => $this->trans('Cancel', 'Admin.Actions'),
            'modal_select_label' => $this->trans('Select {selectedItemsNb} items', 'Admin.Actions'),
            'modal_loading' => $this->trans('Loading items', 'Admin.Actions'),
        ]);
    }
}
