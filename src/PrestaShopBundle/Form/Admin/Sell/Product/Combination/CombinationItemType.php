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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\SubmittableInputType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class CombinationItemType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('is_selected', CheckboxType::class, [
                'label' => false,
            ])
            ->add('combination_id', HiddenType::class, [
                'attr' => [
                    'class' => 'combination-id-input',
                ],
            ])
            ->add('name', HiddenType::class)
            ->add('reference', SubmittableInputType::class, [
                'type' => TextType::class,
                'type_options' => [
                    'constraints' => [
                        new Length([
                            'max' => Reference::MAX_LENGTH,
                            'maxMessage' => $this->trans(
                                'The %1$s field is too long (%2$d chars max).',
                                'Admin.Notifications.Error',
                                ['%1$s' => 'reference', '%2$d' => Reference::MAX_LENGTH]
                            ),
                        ]),
                        new TypedRegex(TypedRegex::TYPE_REFERENCE),
                    ],
                ],
                'attr' => [
                    'class' => 'combination-reference',
                ],
            ])
            ->add('impact_on_price', SubmittableInputType::class, [
                'type' => MoneyType::class,
                'attr' => [
                    'class' => 'combination-impact-on-price',
                ],
            ])
            ->add('final_price_te', HiddenType::class)
            ->add('quantity', SubmittableInputType::class, [
                'attr' => [
                    'class' => 'combination-quantity',
                ],
            ])
            ->add('is_default', RadioType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'combination-is-default-input',
                ],
            ])
            ->add('edit', IconButtonType::class, [
                'icon' => 'mode_edit',
                'attr' => [
                    'class' => 'edit-combination-item tooltip-link',
                    'data-toggle' => 'pstooltip',
                    'data-original-title' => $this->trans('Edit', 'Admin.Global'),
                ],
            ])
            ->add('delete', IconButtonType::class, [
                'icon' => 'delete',
                'attr' => [
                    'class' => 'delete-combination-item tooltip-link',
                    'data-modal-title' => $this->trans('Delete item', 'Admin.Notifications.Warning'),
                    'data-modal-message' => $this->trans('Are you sure you want to delete this item?', 'Admin.Notifications.Warning'),
                    'data-modal-apply' => $this->trans('Delete', 'Admin.Actions'),
                    'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
                    'data-toggle' => 'pstooltip',
                    'data-original-title' => $this->trans('Delete', 'Admin.Global'),
                ],
            ])
        ;
    }
}
