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

namespace PrestaShopBundle\Form\Admin\Sell\CartRule;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\CartRule\CartRuleSettings;
use PrestaShopBundle\Form\Admin\Type\GeneratableTextType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class InformationType extends TranslatorAwareType
{
    public const GENERATED_CODE_LENGTH = 8;

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $this->trans(
                    'This will be displayed in the cart summary, as well as on the invoice.',
                    'Admin.Catalog.Help'
                ),
                'options' => [
                    'constraints' => [
                        new Length(['max' => CartRuleSettings::NAME_MAX_LENGTH]),
                    ],
                ],
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'required' => false,
                'help' => $this->trans(
                    'For your eyes only. This will never be displayed to the customer.',
                    'Admin.Catalog.Help'
                ),
                'constraints' => [
                    new TypedRegex(TypedRegex::CLEAN_HTML_NO_IFRAME),
                    new Length(['max' => CartRuleSettings::DESCRIPTION_MAX_LENGTH]),
                ],
            ])
            ->add('code', GeneratableTextType::class, [
                'label' => $this->trans('Code', 'Admin.Global'),
                'generated_value_length' => self::GENERATED_CODE_LENGTH,
                'empty_data' => '',
                'required' => false,
                'help' => $this->trans(
                    'Caution! If you leave this field blank, the rule will automatically be applied to benefiting customers.',
                    'Admin.Catalog.Help'
                ),
                'constraints' => [
                    new TypedRegex(TypedRegex::CLEAN_HTML_NO_IFRAME),
                    new Length(['max' => CartRuleSettings::CODE_MAX_LENGTH]),
                ],
            ])
            ->add('highlight', SwitchType::class, [
                'row_attr' => [
                    'class' => 'js-highlight-switch-container',
                ],
                'attr' => [
                    // disabled by default, but correct state should be handled by js depending if field "code" is not empty
                    'disabled' => true,
                ],
                'label' => $this->trans('Highlight', 'Admin.Catalog.Feature'),
                'required' => false,
                'help' => $this->trans(
                    'If the voucher is not yet in the cart, it will be displayed in the cart summary.',
                    'Admin.Catalog.Help'
                ),
            ])
            ->add('partial_use', SwitchType::class, [
                'label' => $this->trans('Partial use', 'Admin.Global'),
                'required' => false,
                'help' => $this->trans(
                    'Only applicable if the voucher value is greater than the cart total.',
                    'Admin.Catalog.Help'
                    ) . ' ' .
                    $this->trans(
                        'If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.',
                        'Admin.Catalog.Help'
                    ),
            ])
            ->add('priority', NumberType::class, [
                'label' => $this->trans('Priority', 'Admin.Catalog.Feature'),
                'required' => false,
                'help' => $this->trans(
                    'Cart rules are applied by priority. A cart rule with a priority of "1" will be processed before a cart rule with a priority of "2".',
                    'Admin.Catalog.Help'
                ),
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', 'Admin.Global'),
                'required' => false,
            ])
        ;
    }
}
