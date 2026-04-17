<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Details;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatureCollectionType extends CollectionType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'entry_type' => FeatureCollectionItemType::class,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__FEATURE_ITEM_INDEX__',
            'label' => false,
        ]);
    }

    /**
     * Change block prefix for theme override.
     *
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'feature_collection';
    }
}
