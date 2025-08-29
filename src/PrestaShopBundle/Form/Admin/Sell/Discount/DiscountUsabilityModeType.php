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

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueDiscountCode;
use PrestaShopBundle\Form\Admin\Type\GeneratableTextType;
use PrestaShopBundle\Form\Admin\Type\ToggleChildrenChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\When;

class DiscountUsabilityModeType extends TranslatorAwareType
{
    public const AUTO_MODE = 'auto';
    public const CODE_MODE = 'code';
    protected const GENERATED_CODE_LENGTH = 8;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::AUTO_MODE, HiddenType::class, [
                'label' => $this->trans('Create automatic discount', 'Admin.Catalog.Feature'),
            ])
            ->add(self::CODE_MODE, GeneratableTextType::class, [
                'label' => $this->trans('Generate discount code', 'Admin.Catalog.Feature'),
                'required' => false,
                'generated_value_length' => self::GENERATED_CODE_LENGTH,
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::CODE_MODE,
                        ),
                        constraints: [
                            new NotBlank(),
                            new TypedRegex(TypedRegex::TYPE_DISCOUNT_CODE),
                            new UniqueDiscountCode(),
                        ],
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
        ]);
    }

    public function getParent()
    {
        return ToggleChildrenChoiceType::class;
    }
}
