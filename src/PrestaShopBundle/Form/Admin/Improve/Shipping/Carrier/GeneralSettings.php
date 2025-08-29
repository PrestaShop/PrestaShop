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

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Contracts\Translation\TranslatorInterface;

class GeneralSettings extends TranslatorAwareType
{
    private const MAX_IMAGE_SIZE_IN_BYTES = 8 * 1000000;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly GroupByIdChoiceProvider $groupByIdChoiceProvider
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $maximumFileSize = (int) str_replace('M', '', strval(self::MAX_IMAGE_SIZE_IN_BYTES));

        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Carrier name', 'Admin.Shipping.Feature'),
                'label_help_box' => $this->trans('Allowed characters: letters, spaces and "%special_chars%".', 'Admin.Shipping.Help', ['%special_chars%' => '().-']) . '<br/>' .
                    $this->trans('The carrier\'s name will be displayed during checkout.', 'Admin.Shipping.Help') . '<br/>' .
                    $this->trans('For in-store pickup, enter 0 to replace the carrier name with your shop name.', 'Admin.Shipping.Help'),
                'required' => true,
            ])
            ->add('localized_delay', TranslatableType::class, [
                'required' => true,
                'label' => $this->trans('Transit time', 'Admin.Shipping.Feature'),
                'label_help_box' => $this->trans('The delivery time will be displayed during checkout.', 'Admin.Shipping.Help'),
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Active', 'Admin.Global'),
                'required' => false,
            ])
            ->add('grade', NumberType::class, [
                'label' => $this->trans('Speed grade', 'Admin.Shipping.Feature'),
                'label_help_box' => $this->trans('Enter "0" for a longest shipping delay, or "9" for the shortest shipping delay.', 'Admin.Shipping.Help'),
                'scale' => 0,
                'required' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 9,
                ],
            ])
            ->add('logo_preview', ImagePreviewType::class, [
                'label' => $this->trans('Logo', 'Admin.Global'),
                'image_class' => 'img-fluid carrier__logo',
            ])
            ->add('logo', FileType::class, [
                'label' => null,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => $maximumFileSize,
                        'mimeTypes' => [
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => $this->trans('Please upload a valid jpeg file', 'Admin.Shipping.Feature'),
                        'maxSizeMessage' => $this->trans('The file is too large. Allowed maximum size is 8MB.', 'Admin.Shipping.Feature'),
                    ]),
                ],
            ])
            ->add('tracking_url', TextType::class, [
                'required' => false,
                'label' => $this->trans('Tracking URL', 'Admin.Shipping.Feature'),
                'label_help_box' => $this->trans('Delivery tracking URL: Type \'@\' where the tracking number should appear. It will be automatically replaced by the tracking number.', 'Admin.Shipping.Help'),
                'help' => $this->trans('For example: \'http://example.com/track.php?num=@\' with \'@\' where the tracking number should appear.', 'Admin.Shipping.Help'),
                'constraints' => [
                    new Url([
                        'message' => $this->trans('Please enter a valid URL.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('group_access', MaterialChoiceTableType::class, [
                'label' => $this->trans('Group access', 'Admin.Shipping.Feature'),
                'help' => $this->trans('Mark the groups that are allowed access to this carrier.', 'Admin.Shipping.Help'),
                'empty_data' => [],
                'choices' => $this->groupByIdChoiceProvider->getChoices(),
                'display_total_items' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
        ;
    }
}
