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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\VirtualProductFileSettings;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class VirtualProductFileType extends TranslatorAwareType
{
    /**
     * @var int
     */
    private $maxFileSizeInMegabytes;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param int $maxFileSizeInMegabytes
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        int $maxFileSizeInMegabytes,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->maxFileSizeInMegabytes = $maxFileSizeInMegabytes;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $maxUploadSize = $this->maxFileSizeInMegabytes . 'M';

        $builder
            ->add('has_file', SwitchType::class, [
                'label' => $this->trans('Does this product have an associated file?', 'Admin.Catalog.Feature'),
            ])
            ->add('virtual_product_file_id', HiddenType::class)
            ->add('file', FileType::class, [
                'label' => $this->trans('File', 'Admin.Global'),
                'help' => $this->trans(
                    'Upload a file from your computer (%maxUploadSize% max.)',
                    'Admin.Catalog.Help',
                    ['%maxUploadSize%' => $maxUploadSize]
                ),
                'constraints' => [
                    new File(['maxSize' => $maxUploadSize]),
                ],
            ])
            ->add('name', TextType::class, [
                'label' => $this->trans('Filename', 'Admin.Global'),
                'help' => $this->trans('The full filename with its extension (e.g. Book.pdf)', 'Admin.Catalog.Help'),
                'constraints' => [
                    new NotBlank(),
                    new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                    new Length([
                        'max' => VirtualProductFileSettings::MAX_DISPLAY_FILENAME_LENGTH,
                    ]),
                ],
            ])
            ->add('download_times_limit', NumberType::class, [
                'label' => $this->trans('Number of allowed downloads', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'Number of downloads allowed per customer. Set to 0 for unlimited downloads.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => VirtualProductFileSettings::MAX_DOWNLOAD_TIMES_LIMIT,
                        'message' => $this->trans(
                            'This value should be less than or equal to %value%.',
                            'Admin.Notifications.Error',
                            ['%value%' => VirtualProductFileSettings::MAX_DOWNLOAD_TIMES_LIMIT]
                        ),
                    ]),
                ],
            ])
            ->add('access_days_limit', NumberType::class, [
                'label' => $this->trans('Number of days', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'Number of days this file can be accessed by customers. Set to zero for unlimited access.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => VirtualProductFileSettings::MAX_ACCESSIBLE_DAYS_LIMIT,
                        'message' => $this->trans(
                            'This value should be less than or equal to %value%.',
                            'Admin.Notifications.Error',
                            ['%value%' => VirtualProductFileSettings::MAX_ACCESSIBLE_DAYS_LIMIT]
                        ),
                    ]),
                ],
            ])
            ->add('expiration_date', DatePickerType::class, [
                'label' => $this->trans('Expiration date', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'If set, the file will not be downloadable after this date. Leave blank if you do not wish to attach an expiration date.',
                    'Admin.Catalog.Help'
                ),
                'attr' => ['placeholder' => 'YYYY-MM-DD'],
                'required' => false,
                'empty_data' => '',
            ])
        ;

        // Preset the input attributes correctly depending on the data
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            if (empty($data['virtual_product_file_id'])) {
                return;
            }
            $virtualProductFileId = (int) $data['virtual_product_file_id'];
            $virtualProductFileDownloadUrl = $this->router->generate('admin_products_v2_download_virtual_product_file', [
                'virtualProductFileId' => $virtualProductFileId,
            ]);

            $form = $event->getForm();
            $fileField = $form->get('file');
            $fileOptions = $fileField->getConfig()->getOptions();
            $fileOptions['download_url'] = $virtualProductFileDownloadUrl;

            // Replace existing field with new one with adapted options
            $cloner = new FormCloner();
            $clonedForm = $cloner->cloneForm($fileField, $fileOptions);
            $form->add($clonedForm);
        });
    }
}
