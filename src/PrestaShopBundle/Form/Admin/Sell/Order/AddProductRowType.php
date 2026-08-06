<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type used to add a product row
 */
class AddProductRowType extends TranslatorAwareType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $orderInvoiceByIdChoiceProvider;

    /**
     * @var int
     */
    private $contextLangId;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurableFormChoiceProviderInterface $orderInvoiceByIdChoiceProvider,
        int $contextLangId
    ) {
        parent::__construct($translator, $locales);

        $this->orderInvoiceByIdChoiceProvider = $orderInvoiceByIdChoiceProvider;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $builder->getData();

        $invoices = $data['order_id'] ?
            $this->orderInvoiceByIdChoiceProvider->getChoices([
                'id_order' => $data['order_id'],
                'id_lang' => $this->contextLangId,
                'display_total' => false,
            ]) : [];

        if ($data['is_multishipment_is_enabled'] === true) {
            $builder
                ->add('addShipment', ChoiceType::class, [
                    'label' => $this->trans('Select a shipment', 'Admin.Orderscustomers.Feature'),
                    'disabled' => true,
                    'placeholder' => $this->trans('Choose a shipment', 'Admin.Orderscustomers.Feature'),
                    'attr' => [
                        'class' => 'custom-select',
                    ],
                ])
                ->add('carrier_for_shipment', ChoiceType::class, [
                    'label' => $this->trans('Select a carrier for the new shipment', 'Admin.Orderscustomers.Feature'),
                    'disabled' => true,
                    'placeholder' => $this->trans('Choose a carrier', 'Admin.Orderscustomers.Feature'),
                    'attr' => [
                        'class' => 'custom-select',
                    ],
                ])
                ->add('confirm_new_invoice', CheckboxType::class, [
                    'required' => false,
                    'label' => $this->trans('I confirm the creation of a new invoice', 'Admin.Orderscustomers.Feature', []),
                    'attr' => [
                        'material_design' => true,
                    ],
                ]);
        }

        $builder
            ->add('product_id', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('tax_rate', HiddenType::class)
            ->add('search', TextType::class, [
                'label' => $this->trans('Search for a product', 'Admin.Orderscustomers.Feature'),
                'attr' => [
                    'class' => 'col-sm-12',
                    'autocomplete' => 'off',
                    'placeholder' => $this->trans('Search for a product', 'Admin.Orderscustomers.Feature'),
                    'data-currency' => $data['currency']->id,
                    'data-order' => $data['order_id'],
                ],
            ])
            ->add('addProductCombinations', ChoiceType::class, [
                'attr' => [
                    'class' => 'custom-select',
                ],
            ])
            ->add('price_tax_excluded', MoneyType::class, [
                'label' => $this->trans('tax excl.', 'Admin.Global'),
                'currency' => $data['currency']->iso_code,
            ])
            ->add('price_tax_included', MoneyType::class, [
                'label' => $this->trans('tax incl.', 'Admin.Global'),
                'currency' => $data['currency']->iso_code,
            ])
            ->add('quantity', IntegerType::class, [
                'label' => false,
                'data' => 1,
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('invoice', ChoiceType::class, [
                'label' => false,
                'disabled' => true,
                'choices' => [
                    $this->trans('Existing', 'Admin.Global') => $invoices,
                    $this->trans('New', 'Admin.Global') => [
                        $this->trans('Create a new invoice', 'Admin.Orderscustomers.Feature') => 0,
                    ],
                ],
            ])
            ->add('free_shipping', CheckboxType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Free shipping', 'Admin.Orderscustomers.Feature', []),
                    'attr' => [
                        'material_design' => true,
                    ],
                ]
            )
            ->add('cancel', ButtonType::class, [
                'label' => $this->trans('Cancel', 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn btn-sm btn-secondary js-product-add-action-btn mr-2 mt-2 mb-2',
                ],
            ])
            ->add('add', ButtonType::class, [
                'label' => $this->trans('Add', 'Admin.Actions'),
                'disabled' => true,
                'attr' => [
                    'class' => 'btn btn-sm btn-primary js-product-add-action-btn mt-2 mb-2',
                    'data-order-id' => $data['order_id'],
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'order_id' => null,
            'currency' => [],
            'is_multishipment_is_enabled' => false,
        ]);
    }
}
