<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is responsible to generate the product combination form.
 */
class ProductCombination extends CommonAbstractType
{
    private $translator;
    private $contextLegacy;
    private $configuration;

    /**
     * Constructor.
     *
     * @param object $translator
     * @param object $legacyContext
     */
    public function __construct($translator, $legacyContext)
    {
        $this->translator = $translator;
        $this->contextLegacy = $legacyContext->getContext();
        $this->configuration = $this->getConfiguration();
        $this->currency = $this->contextLegacy->currency;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $is_stock_management = $this->configuration->get('PS_STOCK_MANAGEMENT');

        $builder->add('id_product_attribute', HiddenType::class, [
            'required' => false,
        ])
            ->add('attribute_reference', TextType::class, [
                'required' => false,
                'label' => $this->translator->trans('Reference', [], 'Admin.Global'),
                'empty_data' => '',
            ])
            ->add('attribute_ean13', TextType::class, [
                'required' => false,
                'error_bubbling' => true,
                'label' => $this->translator->trans('EAN-13 or JAN barcode', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Regex('/^[0-9]{0,13}$/'),
                ],
                'empty_data' => '',
            ])
            ->add('attribute_isbn', TextType::class, [
                'required' => false,
                'label' => $this->translator->trans('ISBN code', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Regex('/^[0-9-]{0,32}$/'),
                ],
                'empty_data' => '',
            ])
            ->add('attribute_upc', TextType::class, [
                'required' => false,
                'label' => $this->translator->trans('UPC barcode', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Regex('/^[0-9]{0,12}$/'),
                ],
                'empty_data' => '',
            ])
            ->add('attribute_mpn', TextType::class, [
                'required' => false,
                'label' => $this->translator->trans('MPN', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Length(['max' => 40]),
                ],
                'empty_data' => '',
            ])
            ->add('attribute_wholesale_price', MoneyType::class, [
                'required' => false,
                'label' => $this->translator->trans('Cost price', [], 'Admin.Catalog.Feature'),
                'currency' => $this->currency->iso_code,
                'attr' => ['class' => 'attribute_wholesale_price'],
            ])
            ->add('attribute_price', MoneyType::class, [
                'required' => false,
                'label' => $this->translator->trans('Impact on price (tax excl.)', [], 'Admin.Catalog.Feature'),
                'currency' => $this->currency->iso_code,
                'attr' => ['class' => 'attribute_priceTE'],
            ])
            ->add('attribute_priceTI', MoneyType::class, [
                'required' => false,
                'mapped' => false,
                'label' => $this->translator->trans('Impact on price (tax incl.)', [], 'Admin.Catalog.Feature'),
                'currency' => $this->currency->iso_code,
                'attr' => ['class' => 'attribute_priceTI'],
            ])
            ->add('attribute_ecotax', MoneyType::class, [
                'required' => false,
                'label' => $this->translator->trans('Ecotax (tax incl.)', [], 'Admin.Catalog.Feature'),
                'currency' => $this->currency->iso_code,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'float']),
                ],
            ])
            ->add('attribute_weight', NumberType::class, [
                'required' => false,
                'label' => $this->translator->trans('Impact on weight', [], 'Admin.Catalog.Feature'),
            ])
            ->add('attribute_unity', MoneyType::class, [
                'required' => false,
                'label' => $this->translator->trans('Impact on price per unit (tax excl.)', [], 'Admin.Catalog.Feature'),
                'currency' => $this->currency->iso_code,
                'attr' => ['class' => 'attribute_unity'],
            ])
            ->add('attribute_minimal_quantity', NumberType::class, [
                'required' => false,
                'label' => $this->translator->trans('Min. quantity for sale', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'numeric']),
                ],
            ])
            ->add('attribute_location', TextType::class, [
                'label' => $this->translator->trans('Stock location', [], 'Admin.Catalog.Feature'),
            ])
            ->add('attribute_low_stock_threshold', NumberType::class, [
                'label' => $this->translator->trans('Low stock level', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Type(['type' => 'numeric']),
                ],
                'attr' => [
                    'placeholder' => $this->translator->trans(
                        'Leave empty to disable',
                        [],
                        'Admin.Catalog.Help'
                    ),
                ],
            ])
            ->add('attribute_low_stock_alert', CheckboxType::class, [
                'label' => $this->translator->trans('Send me an email when the quantity is below or equals this level', [], 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Assert\Type(['type' => 'bool']),
                ],
            ])
            ->add('available_date_attribute', DatePickerType::class, [
                'required' => false,
                'label' => $this->translator->trans('Availability date', [], 'Admin.Catalog.Feature'),
                'attr' => ['class' => 'date', 'placeholder' => 'YYYY-MM-DD'],
            ])
            ->add('attribute_default', CheckboxType::class, [
                'label' => $this->translator->trans('Set as default combination', [], 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => ['class' => 'attribute_default_checkbox'],
            ]);
        if ($is_stock_management) {
            $builder->add(
                'attribute_quantity',
                NumberType::class,
                [
                    'required' => true,
                    'label' => $this->translator->trans('Quantity', [], 'Admin.Catalog.Feature'),
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'numeric']),
                    ],
                ]
            );
        }
        $builder->add('id_image_attr', ChoiceType::class, [
            'choices' => [],
            'required' => false,
            'expanded' => true,
            'multiple' => true,
            'label' => $this->translator->trans('Select images of this combination:', [], 'Admin.Catalog.Feature'),
            'attr' => ['class' => 'images'],
        ])
            ->add('final_price', MoneyType::class, [
                'required' => false,
                'label' => $this->translator->trans('Final price', [], 'Admin.Catalog.Feature'),
                'currency' => $this->currency->iso_code,
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $choices = [];
            if (!empty($data['id_image_attr'])) {
                foreach ($data['id_image_attr'] as $id) {
                    $choices[$id] = $id;
                }
            }

            $form->add('id_image_attr', ChoiceType::class, [
                'choices' => $choices,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ]);
        });
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_combination';
    }
}
