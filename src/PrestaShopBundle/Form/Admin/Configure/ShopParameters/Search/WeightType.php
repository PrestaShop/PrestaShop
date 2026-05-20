<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Search;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class WeightType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $weightOptions = [
            'required' => false,
            'constraints' => [new PositiveOrZero()],
        ];

        $builder
            ->add('product_name_weight', IntegerType::class, array_merge($weightOptions, [
                'label' => $this->trans('Product name weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('reference_weight', IntegerType::class, array_merge($weightOptions, [
                'label' => $this->trans('Reference weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('short_description_weight', IntegerType::class, array_merge($weightOptions, [
                'label' => $this->trans('Short description weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('description_weight', IntegerType::class, array_merge($weightOptions, [
                'label' => $this->trans('Description weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('category_weight', IntegerType::class, array_merge($weightOptions, [
                'label' => $this->trans('Category weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('brand_weight', IntegerType::class, array_merge($weightOptions, [
                'label' => $this->trans('Brand weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('tags_weight', IntegerType::class, array_merge($weightOptions, [
                'label' => $this->trans('Tags weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('attributes_weight', IntegerType::class, array_merge($weightOptions, [
                'label' => $this->trans('Attributes weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('features_weight', IntegerType::class, array_merge($weightOptions, [
                'label' => $this->trans('Features weight', 'Admin.Shopparameters.Feature'),
            ]));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'search_weight_block';
    }
}
