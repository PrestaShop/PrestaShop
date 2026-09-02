<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is the entry type of the GroupedItemCollectionType, it contains the group data
 * (id + name), and the list of items represented with a TaggedItemCollectionType.
 */
class GroupedItemType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
                'attr' => [
                    'class' => 'grouped-item-id',
                ],
            ])
            ->add('name', TextPreviewType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'grouped-item-name',
                ],
            ])
            ->add('items', TaggedItemCollectionType::class, [
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => false,
            'row_attr' => [
                'class' => 'grouped-item-row',
            ],
        ]);
    }
}
