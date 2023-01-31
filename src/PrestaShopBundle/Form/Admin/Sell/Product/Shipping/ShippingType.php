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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Shipping;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Extension\ModifyAllShopsExtension;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $modifyAllShopsInputName = ModifyAllShopsExtension::MODIFY_ALL_SHOPS_PREFIX . 'carriers';

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
                'label_help_box' => $this->trans('If a carrier has a tax, it will be added to the shipping fees. Does not apply to free shipping.', 'Admin.Catalog.Help'),
                'currency' => $this->currencyIsoCode,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'default_empty_data' => 0.0,
                'modify_all_shops' => true,
            ])
            // related all shops checkbox is rendered in javascript side, but it is still needed here to be correctly filled with data
            ->add($modifyAllShopsInputName, HiddenType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($modifyAllShopsInputName) {
            $carrierChoices = $this->carrierChoiceProvider->getChoices();
            $form = $event->getForm();
            // Vue js CarrierSelector component mounts on this one instead of rendering default bootstrap choices list
            $form->add('carriers', ChoiceType::class, [
                'choices' => $carrierChoices,
                'attr' => [
                    'data-modify-all-shops-name' => sprintf('product[shipping][%s]', $modifyAllShopsInputName),
                    'data-choice-input-name' => 'product[shipping][carriers][]',
                    'data-translations' => json_encode([
                        'allCarriers.label' => $this->trans('All carriers', 'Admin.Actions'),
                        'selectedCarriers.label' => $this->trans('Only selected carriers', 'Admin.Actions'),
                        'modifyAllShops.label' => $this->trans('Apply changes to all stores', 'Admin.Global'),
                    ]),
                    'data-carrier-choices' => json_encode($carrierChoices),
                    // this attribute is fetched from data, so we use PRE_SET_DATA event to add whole carrier choices type
                    'data-selected-carrier-ids' => json_encode($event->getData()['carriers']),
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'label' => $this->trans('Available carriers', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
            ]);
        });
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
