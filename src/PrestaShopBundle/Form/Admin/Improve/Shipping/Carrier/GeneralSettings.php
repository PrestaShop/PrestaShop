<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
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
use Symfony\Component\Validator\Constraints\Range;
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
                'constraints' => [
                    new CleanHtml([
                        'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('localized_delay', TranslatableType::class, [
                'required' => true,
                'label' => $this->trans('Transit time', 'Admin.Shipping.Feature'),
                'label_help_box' => $this->trans('The delivery time will be displayed during checkout.', 'Admin.Shipping.Help'),
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
                        ]),
                    ],
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
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 9,
                        'notInRangeMessage' => $this->trans('The grade must be between 0 and 9.', 'Admin.Shipping.Notification'),
                    ]),
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
