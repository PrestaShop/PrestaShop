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

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\PositiveOrZero;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

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
            ->add('width', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Width', 'Admin.Catalog.Feature'),
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
            ])->add('height', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Height', 'Admin.Catalog.Feature'),
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
            ])->add('depth', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Depth', 'Admin.Catalog.Feature'),
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
            ])->add('weight', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Weight', 'Admin.Catalog.Feature'),
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
            ])->add('delivery_time_note_type', ChoiceType::class, [
                'choices' => $this->deliveryTimeNoteTypesProvider->getChoices(),
                'placeholder' => false,
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'label' => $this->trans('Delivery Time', 'Admin.Catalog.Feature'),
            ])->add('delivery_time_in_stock_note', TranslatableType::class, [
                'label' => $this->trans('Delivery time of in-stock products:', 'Admin.Catalog.Feature'),
                'type' => TextType::class,
                'required' => false,
                'options' => [
                    'attr' => [
                        'placeholder' => $this->trans('Delivered within 3-4 days', 'Admin.Catalog.Feature'),
                    ],
                ],
            ])->add('delivery_time_out_stock_note', TranslatableType::class, [
                'locales' => $this->locales,
                'required' => false,
                'label' => $this->trans(
                    'Delivery time of out-of-stock products with allowed orders:',
                    'Admin.Catalog.Feature'
                ),
                'options' => [
                    'attr' => [
                        'placeholder' => $this->trans('Delivered within 5-7 days', 'Admin.Catalog.Feature'),
                    ],
                ],
            ])->add('additional_shipping_cost', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Shipping fees', 'Admin.Catalog.Feature'),
                'currency' => $this->currencyIsoCode,
                'constraints' => [
                    new NotBlank(),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])->add('carriers', ChoiceType::class, [
                'choices' => $this->carrierChoiceProvider->getChoices(),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'label' => $this->trans('Available carriers', 'Admin.Catalog.Feature'),
            ])
        ;
    }
}
