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

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class DiscountInformationType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $discountType = $options['discount_type'];
        $builder
            ->add('discount_type', TextPreviewType::class, [
                'data' => $discountType,
                'label' => $this->trans('Discount Type', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('names', TranslatableType::class, [
                'label' => $this->trans('Discount Name', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('This will be displayed in the cart summary, as well as on the invoice.', 'Admin.Catalog.Help'),
                'required' => true,
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => DiscountSettings::MAX_NAME_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => DiscountSettings::MAX_NAME_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->trans('Discount description', 'Admin.Global'),
                'required' => false,
                'label_help_box' => $this->trans(
                    'For your eyes only. This will never be displayed to the customer.',
                    'Admin.Catalog.Help'
                ),
                'constraints' => [
                    new TypedRegex([
                        'type' => TypedRegex::CLEAN_HTML_NO_IFRAME,
                    ]),
                    new Length([
                        'max' => DiscountSettings::MAX_DESCRIPTION_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => DiscountSettings::MAX_DESCRIPTION_LENGTH]
                        ),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired([
            'discount_type',
        ]);
        $resolver->setAllowedTypes('discount_type', ['string']);
        $resolver->setDefaults([
            'label' => $this->trans('Discount information', 'Admin.Catalog.Feature'),
        ]);
    }

    public function getParent()
    {
        return CardType::class;
    }
}
