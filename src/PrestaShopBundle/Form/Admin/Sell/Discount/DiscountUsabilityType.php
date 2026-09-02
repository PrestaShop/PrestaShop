<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DiscountUsabilityType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mode', DiscountUsabilityModeType::class, [
                'label' => $this->trans('Specifiy discount mode', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
                'choice_options' => [
                    'label' => false,
                ],
            ])
            ->add('quantity_total', IntegerType::class, [
                'required' => false,
                'label' => $this->trans('Select total usage limits', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('This discount can be used "X" time(s) in total.', 'Admin.Catalog.Help'),
                'label_tag_name' => 'h3',
                'attr' => [
                    'min' => 0,
                ],
                'label_attr' => [
                    'class' => 'd-flex align-items-center',
                ],
                'disabling_switch' => true,
                'disabling_switch_label' => $this->trans('No limit', 'Admin.Catalog.Feature'),
                'switch_state_on_disable' => 'on',
                'default_empty_data' => null,
                'disabled_value' => function ($data) {
                    return null === $data;
                },
                'constraints' => [
                    new Assert\GreaterThanOrEqual(0),
                ],
            ])
            ->add('usage', DiscountUsagePreviewType::class, [
                'label' => $this->trans('Usage', 'Admin.Catalog.Feature'),
            ])
            ->add('quantity_per_customer', IntegerType::class, [
                'required' => false,
                'label' => $this->trans('Select usage limits per customer', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('A customer will only be able to use this discount "X" time(s).', 'Admin.Catalog.Help'),
                'label_tag_name' => 'h3',
                'attr' => [
                    'min' => 0,
                ],
                'label_attr' => [
                    'class' => 'd-flex align-items-center',
                ],
                'disabling_switch' => true,
                'disabling_switch_label' => $this->trans('No limit', 'Admin.Catalog.Feature'),
                'switch_state_on_disable' => 'on',
                'default_empty_data' => null,
                'disabled_value' => function ($data) {
                    return null === $data;
                },
                'constraints' => [
                    new Assert\GreaterThanOrEqual(0),
                    new Assert\LessThanOrEqual([
                        'propertyPath' => 'parent.all[quantity_total].data',
                        'message' => $this->trans('The usage limit per customer cannot be higher than the total usage limit.', 'Admin.Catalog.Notification'),
                    ]),
                ],
            ])
            ->add('compatibility', ChoiceType::class, [
                'label_tag_name' => 'h3',
                'label' => $this->trans('Compatible with discounts', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('Select which discount types this discount is compatible with.', 'Admin.Catalog.Help'),
                'choices' => $options['available_discount_types'],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('priority', IntegerType::class, [
                'label' => $this->trans('Priority', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
                'label_help_box' => $this->trans('Lower numbers indicate higher priority. When multiple discounts are applied, lower priority numbers are processed first.', 'Admin.Catalog.Help'),
                'attr' => [
                    'min' => 1,
                    'placeholder' => '1',
                ],
                'constraints' => [
                    new Assert\GreaterThanOrEqual(1),
                    new Assert\LessThanOrEqual(999),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'available_discount_types' => [],
        ]);
        $resolver->setAllowedTypes('available_discount_types', ['array']);
    }

    public function getParent()
    {
        return CardType::class;
    }
}
