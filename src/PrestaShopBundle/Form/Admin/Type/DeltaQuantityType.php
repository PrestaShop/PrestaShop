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

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Quantity field that displays the initial quantity (not editable) and allows editing with delta quantity
 * instead (ex: +5, -8). The input data of this form type is the initial (as a plain integer) however its output
 * on submit is the delta quantity.
 */
class DeltaQuantityType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', TextPreviewType::class, [
                'block_prefix' => 'delta_quantity_quantity',
            ])
        ;

        if ($options['submittable']) {
            $builder->add('delta', SubmittableInputType::class, [
                'block_prefix' => 'delta_quantity_delta',
                'label' => false,
                'attr' => [
                    'aria-label' => $this->trans('Add or subtract items', 'Admin.Global'),
                ],
            ]);
        } else {
            $builder->add('delta', NumberType::class, [
                'scale' => 0,
                'default_empty_data' => 0,
                'label' => $this->trans('Add or subtract items', 'Admin.Global'),
                'block_prefix' => 'delta_quantity_delta',
                'row_attr' => [
                    'class' => 'delta-quantity-delta-container',
                ],
            ]);
        }

        $builder->get('quantity')->addViewTransformer(new NumberToLocalizedStringTransformer(0, false));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('submittable', false);
        $resolver->setAllowedTypes('submittable', 'bool');
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['submittable'] = $options['submittable'];
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'delta_quantity';
    }
}
