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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatureValueType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feature_value_id', HiddenType::class, [
                'attr' => [
                    'class' => 'feature-value-id',
                ],
            ])
            ->add('feature_value_name', TextPreviewType::class, [
                'row_attr' => [
                    'class' => 'feature-value-preview',
                ],
                'attr' => [
                    'class' => 'feature-value-name',
                ],
                'label' => false,
            ])
            ->add('is_custom', HiddenType::class, [
                'attr' => [
                    'class' => 'is-custom-feature-value',
                ],
                'required' => false,
                'empty_data' => false,
            ])
            ->add('custom_value', TranslatableType::class, [
                'label' => false,
                'required' => false,
                'type' => TextType::class,
                'row_attr' => [
                    'class' => 'custom-values-form-group',
                ],
                'constraints' => [
                    new DefaultLanguage([
                        'message' => $this->trans(
                            'The field %field_name% is required at least in your default language.',
                            'Admin.Notifications.Error',
                            [
                                '%field_name%' => sprintf(
                                    '"%s"',
                                    $this->trans('Custom value', 'Admin.Catalog.Feature')
                                ),
                            ]
                        ),
                        'groups' => 'custom_value',
                    ]),
                ],
            ])
            ->add('delete', IconButtonType::class, [
                'icon' => 'delete',
                'attr' => [
                    'class' => 'tooltip-link delete-feature-value pl-0 pr-0',
                    'data-modal-title' => $this->trans('Delete item', 'Admin.Notifications.Warning'),
                    'data-modal-message' => $this->trans('Are you sure you want to delete this item?', 'Admin.Notifications.Warning'),
                    'data-modal-apply' => $this->trans('Delete', 'Admin.Actions'),
                    'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
                    'data-toggle' => 'pstooltip',
                    'data-original-title' => $this->trans('Delete', 'Admin.Actions'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form): array {
                $formData = $form->getData();

                return !empty($formData['is_custom']) ? ['Default', 'custom_value'] : ['Default'];
            },
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $formData = $form->getData();

        // When data is null the prototype is being rendered so the input is not custom and is not not custom either (schrodinger custom input)
        $view->vars['is_custom'] = null === $formData ? null : !empty($formData['is_custom']);
    }
}
