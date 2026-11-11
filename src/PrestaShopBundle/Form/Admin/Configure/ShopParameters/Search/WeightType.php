<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Search;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

final class WeightType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $weightOptions = [
            'required' => false,
            'empty_data' => '0',
            'constraints' => [new PositiveOrZero()],
        ];

        $builder
            ->add('product_name_weight', IntegerType::class, array_merge($weightOptions, [
                'multistore_configuration_key' => 'PS_SEARCH_WEIGHT_PNAME',
                'label' => $this->trans('Product name weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('reference_weight', IntegerType::class, array_merge($weightOptions, [
                'multistore_configuration_key' => 'PS_SEARCH_WEIGHT_REF',
                'label' => $this->trans('Reference weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('short_description_weight', IntegerType::class, array_merge($weightOptions, [
                'multistore_configuration_key' => 'PS_SEARCH_WEIGHT_SHORTDESC',
                'label' => $this->trans('Short description weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('description_weight', IntegerType::class, array_merge($weightOptions, [
                'multistore_configuration_key' => 'PS_SEARCH_WEIGHT_DESC',
                'label' => $this->trans('Description weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('category_weight', IntegerType::class, array_merge($weightOptions, [
                'multistore_configuration_key' => 'PS_SEARCH_WEIGHT_CNAME',
                'label' => $this->trans('Category weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('brand_weight', IntegerType::class, array_merge($weightOptions, [
                'multistore_configuration_key' => 'PS_SEARCH_WEIGHT_MNAME',
                'label' => $this->trans('Brand weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('tags_weight', IntegerType::class, array_merge($weightOptions, [
                'multistore_configuration_key' => 'PS_SEARCH_WEIGHT_TAG',
                'label' => $this->trans('Tags weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('attributes_weight', IntegerType::class, array_merge($weightOptions, [
                'multistore_configuration_key' => 'PS_SEARCH_WEIGHT_ATTRIBUTE',
                'label' => $this->trans('Attributes weight', 'Admin.Shopparameters.Feature'),
            ]))
            ->add('features_weight', IntegerType::class, array_merge($weightOptions, [
                'multistore_configuration_key' => 'PS_SEARCH_WEIGHT_FEATURE',
                'label' => $this->trans('Features weight', 'Admin.Shopparameters.Feature'),
            ]));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'search_weight_block';
    }
}
