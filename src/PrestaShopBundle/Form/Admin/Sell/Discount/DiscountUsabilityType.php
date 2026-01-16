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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType as DiscountTypeVO;
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
        $cartRuleTypeChoices = [];
        foreach ($options['available_cart_rule_types'] as $cartRuleType) {
            if ($cartRuleType['name'] === DiscountTypeVO::ORDER_LEVEL) {
                continue;
            }
            $cartRuleTypeChoices[$cartRuleType['name']] = $cartRuleType['id_cart_rule_type'];
        }
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
                'choices' => $cartRuleTypeChoices,
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
            'available_cart_rule_types' => [],
        ]);
        $resolver->setAllowedTypes('available_cart_rule_types', ['array']);
    }

    public function getParent()
    {
        return CardType::class;
    }
}
