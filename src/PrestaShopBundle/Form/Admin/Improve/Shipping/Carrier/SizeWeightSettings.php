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

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\PositiveOrZero;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class SizeWeightSettings extends TranslatorAwareType
{
    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param string $dimensionUnit
     * @param string $weightUnit
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private string $dimensionUnit,
        private string $weightUnit,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('max_width', IntegerType::class, [
                'label' => $this->trans('Max order width', 'Admin.Shipping.Feature'),
                'unit' => $this->dimensionUnit,
                'label_help_box' => $this->trans('Maximum width managed by this carrier. Set the value to "0" to ignore.', 'Admin.Shipping.Help') . ' ' . $this->trans('The value must be an integer.', 'Admin.Shipping.Help'),
                'default_empty_data' => 0,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new PositiveOrZero(),
                ],
            ])
            ->add('max_height', IntegerType::class, [
                'label' => $this->trans('Max order height', 'Admin.Shipping.Feature'),
                'unit' => $this->dimensionUnit,
                'label_help_box' => $this->trans('Maximum height managed by this carrier. Set the value to "0" to ignore.', 'Admin.Shipping.Help') . ' ' . $this->trans('The value must be an integer.', 'Admin.Shipping.Help'),
                'default_empty_data' => 0,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new PositiveOrZero(),
                ],
            ])
            ->add('max_depth', IntegerType::class, [
                'label' => $this->trans('Max order depth', 'Admin.Shipping.Feature'),
                'unit' => $this->dimensionUnit,
                'label_help_box' => $this->trans('Maximum depth managed by this carrier. Set the value to "0" to ignore.', 'Admin.Shipping.Help') . ' ' . $this->trans('The value must be an integer.', 'Admin.Shipping.Help'),
                'default_empty_data' => 0,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new PositiveOrZero(),
                ],
            ])
            ->add('max_weight', NumberType::class, [
                'label' => $this->trans('Max order weight', 'Admin.Shipping.Feature'),
                'unit' => $this->weightUnit,
                'label_help_box' => $this->trans('Maximum weight managed by this carrier. Set the value to "0" to ignore.', 'Admin.Shipping.Help'),
                'default_empty_data' => 0,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new PositiveOrZero(),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label_tab' => $this->trans('Shipping size and weight', 'Admin.Shipping.Feature'),
            'label_tag_name' => 'h3',
        ]);
    }
}
