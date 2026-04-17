<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\StockSettings;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', TextPreviewType::class, [
                'block_prefix' => 'delta_quantity_quantity',
                'constraints' => [
                    new Range([
                        'min' => StockSettings::INT_32_MAX_NEGATIVE,
                        'max' => StockSettings::INT_32_MAX_POSITIVE,
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
                        'min' => StockSettings::INT_32_MAX_NEGATIVE - StockSettings::INT_32_MAX_POSITIVE, // because stock_mvt doesn't use negative numbers it can save 2x the size.
                        'max' => StockSettings::INT_32_MAX_POSITIVE + StockSettings::INT_32_MAX_POSITIVE,
                    ]),
                ],
                'required' => false,
                'modify_all_shops' => $options['modify_delta_for_all_shops'],
            ])
            // This field is mostly used on submit so that we can provide the initial quantity and avoid increasing the
            // offset in quantity on each submit
            ->add('initial_quantity', HiddenType::class)
        ;

        $builder->get('quantity')->addViewTransformer(new NumberToLocalizedStringTransformer(0, false));
        $builder->addEventListener(FormEvents::SUBMIT, static function (FormEvent $event) {
            $formData = $event->getData();
            if (isset($formData['quantity'], $formData['delta'])) {
                $deltaQuantity = (int) $formData['delta'];
                $initialQuantity = (int) $formData['quantity'] - $deltaQuantity;
                $event->setData(array_merge($event->getData(), [
                    'initial_quantity' => $initialQuantity,
                ]));
            }
        });
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
        $deltaQuantity = (int) ($formData['delta'] ?? 0);
        $initialQuantity = isset($formData['initial_quantity']) ? (int) $formData['initial_quantity'] : null;
        $view->vars['deltaQuantity'] = $deltaQuantity;
        $view->vars['initialQuantity'] = $initialQuantity;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'delta_label' => $this->trans('Add or subtract items', 'Admin.Global'),
                'modify_delta_for_all_shops' => false,
            ])
            ->setAllowedTypes('delta_label', ['string', 'boolean', 'null'])
            ->setAllowedTypes('modify_delta_for_all_shops', ['boolean'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'delta_quantity';
    }
}
