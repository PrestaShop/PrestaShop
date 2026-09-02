<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form type is used as the default entry_type for TaggedItemCollectionType.
 */
class TaggedItemType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextPreviewType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'tagged-item-name',
                ],
            ])
            ->add('id', HiddenType::class, [
                'attr' => [
                    'class' => 'tagged-item-value',
                ],
            ])
        ;
    }
}
