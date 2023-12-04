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

use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\NotBlank;

class FeatureValueType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feature_id', HiddenType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('Choose a feature', 'Admin.Catalog.Feature'),
                    ]),
                ],
            ])
            ->add('feature_name', TextPreviewType::class, [
                'label' => false,
            ])
            ->add('feature_value_id', HiddenType::class)
            ->add('feature_value_name', TextPreviewType::class, [
                'label' => false,
            ])
            ->add('custom_value', TranslatableType::class, [
                'label' => false,
                'required' => false,
                'type' => TextType::class,
                'attr' => [
                    'class' => 'custom-values',
                ],
            ])
            ->add('custom_value_id', HiddenType::class, [
                'required' => false,
                'empty_data' => null,
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

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $formData = $form->getData();
        // When data is null the prototype is being rendered so the input is not custom and is not not custom either (schrodinger custom input)
        $view->vars['is_custom'] = null === $formData ? null : !empty($formData['custom_value_id']);
    }
}
