<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Shipping;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type containing product shipping information
 */
class ShippingType extends TranslatorAwareType
{
    /**
     * @var string
     */
    private $currencyIsoCode;

    /**
     * @var FormChoiceProviderInterface
     */
    private $carrierChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $deliveryTimeNoteTypesProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param string $currencyIsoCode
     * @param FormChoiceProviderInterface $carrierChoiceProvider
     * @param FormChoiceProviderInterface $additionalDeliveryTimeNoteTypesProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        string $currencyIsoCode,
        FormChoiceProviderInterface $carrierChoiceProvider,
        FormChoiceProviderInterface $additionalDeliveryTimeNoteTypesProvider
    ) {
        parent::__construct($translator, $locales);
        $this->currencyIsoCode = $currencyIsoCode;
        $this->carrierChoiceProvider = $carrierChoiceProvider;
        $this->deliveryTimeNoteTypesProvider = $additionalDeliveryTimeNoteTypesProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dimensions', DimensionsType::class)
            ->add('delivery_time_note_type', ChoiceType::class, [
                'choices' => $this->deliveryTimeNoteTypesProvider->getChoices(),
                'placeholder' => false,
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'label' => $this->trans('Delivery time', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_help_box' => $this->trans('Display delivery time for a product is advised for merchants selling in Europe to comply with the local laws.', 'Admin.Catalog.Help'),
            ])
            ->add('delivery_time_notes', DeliveryTimeNotesType::class)
            ->add('additional_shipping_cost', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Shipping fees', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_subtitle' => $this->trans('Additional shipping costs', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('Optional additional fee added to the shipping cost for each purchased item. Applies only if shipping is not free.', 'Admin.Catalog.Help'),
                'currency' => $this->currencyIsoCode,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'default_empty_data' => 0.0,
                'modify_all_shops' => true,
            ])
            ->add('carriers', ChoiceType::class, [
                'modify_all_shops' => true,
                'choices' => $this->carrierChoiceProvider->getChoices(),
                'label_attr' => [
                    'class' => 'carrier-choice-label',
                ],
                'attr' => [
                    'data-translations' => json_encode([
                        'allCarriers.label' => $this->trans('All carriers', 'Admin.Actions'),
                        'selectedCarriers.label' => $this->trans('Only selected carriers', 'Admin.Actions'),
                        'modifyAllShops.label' => $this->trans('Apply changes to all stores', 'Admin.Global'),
                    ]),
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'label' => $this->trans('Available carriers', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_help_box' => $this->trans('Here you can restrict which carriers are available for this product. For example, if the product cannot be shipped, you can allow only in-store pickup.', 'Admin.Catalog.Help'),
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Shipping', 'Admin.Catalog.Feature'),
        ]);
    }
}
