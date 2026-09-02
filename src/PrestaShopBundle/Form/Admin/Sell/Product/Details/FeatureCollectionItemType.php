<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FeatureCollectionItemType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feature_id', HiddenType::class, [
                'attr' => [
                    'class' => 'feature-id',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('Choose a feature', 'Admin.Catalog.Feature'),
                    ]),
                ],
            ])
            ->add('feature_name', HiddenType::class, [
                'attr' => [
                    'class' => 'feature-name',
                ],
                'label' => false,
            ])
            ->add('feature_values', CollectionType::class, [
                'entry_type' => FeatureValueType::class,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__FEATURE_VALUE_INDEX__',
                'label' => false,
            ])
        ;
    }

    /**
     * Change block prefix for theme override.
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'feature_collection_item';
    }
}
