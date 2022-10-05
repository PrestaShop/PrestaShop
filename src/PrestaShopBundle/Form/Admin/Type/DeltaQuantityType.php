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

use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Quantity field that displays the initial quantity (not editable) and allows editing with delta quantity
 * instead (ex: +5, -8). The input data of this form type is the initial (as a plain integer) however its output
 * on submit is the delta quantity.
 */
class DeltaQuantityType extends TranslatorAwareType
{
    /**
     * this is the biggest int number that can be saved in database, bigger than this will throw error
     */
    public const INT_32_MAX_POSITIVE = 2147483647;

    /**
     * this is the smallest int number that can be saved in database, smaller than this will throw error
     */
    public const INT_32_MAX_NEGATIVE = -2147483648;

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', TextPreviewType::class, [
                'block_prefix' => 'delta_quantity_quantity',
                'constraints' => [
                    new Range([
                        'min' => static::INT_32_MAX_NEGATIVE,
                        'max' => static::INT_32_MAX_POSITIVE,
                    ]),
                ],
            ])
            ->add('delta', IntegerType::class, [
                'default_empty_data' => 0,
                'label' => $options['delta_label'],
                'block_prefix' => 'delta_quantity_delta',
                'constraints' => [
                    new Type(['type' => 'numeric']),
                    new NotBlank(),
                    new Range([
                        'min' => static::INT_32_MAX_NEGATIVE * 2,
                        'max' => static::INT_32_MAX_POSITIVE * 2,
                    ]),
                ],
                'required' => false,
                'modify_all_shops' => true,
            ]);

        $builder->get('quantity')->addViewTransformer(new NumberToLocalizedStringTransformer(0, false));
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        // We always need to compute the initial quantity based on data because if submit is invalid quantity value is changed,
        // so we can't rely on it to initialize the data attributes containing the initial value which is used in FO later. If
        // we don't handle this the quantity will keep increasing (or decreasing) on each submit.
        $formData = $form->getData();
        if (isset($formData['quantity'], $formData['delta'])) {
            $deltaQuantity = (int) $formData['delta'];
            $initialQuantity = (int) $formData['quantity'] - $deltaQuantity;
        } else {
            $deltaQuantity = 0;
            $initialQuantity = null;
        }
        $view->vars['deltaQuantity'] = $deltaQuantity;
        $view->vars['initialQuantity'] = $initialQuantity;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'delta_label' => $this->trans('Add or subtract items', 'Admin.Global'),
            ])
            ->setAllowedTypes('delta_label', ['string', 'boolean', 'null']);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'delta_quantity';
    }
}
