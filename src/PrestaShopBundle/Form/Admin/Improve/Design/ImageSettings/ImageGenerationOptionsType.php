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

namespace PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TextWithUnitType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Form type for image generation options.
 */
class ImageGenerationOptionsType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isAllShopContext;

    /**
     * @var bool
     */
    private $isMultistoreEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isAllShopContext
     * @param bool $isMultistoreEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isAllShopContext,
        bool $isMultistoreEnabled
    ) {
        parent::__construct($translator, $locales);

        $this->isAllShopContext = $isAllShopContext;
        $this->isMultistoreEnabled = $isMultistoreEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image_quality', ChoiceType::class, [
                'label' => $this->trans('Image format', 'Admin.Design.Feature'),
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    $this->trans('Use JPEG.', 'Admin.Design.Feature') => 'jpg',
                    $this->trans('Use PNG only if the base image is in PNG format.', 'Admin.Design.Feature') => 'png',
                    $this->trans('Use PNG for all images.', 'Admin.Design.Feature') => 'png_all',
                ],
            ])
            ->add('jpeg_quality', TextType::class, [
                'label' => $this->trans('JPEG compression', 'Admin.Design.Feature'),
                'required' => true,
                'help' => $this->trans(
                    'Ranges from 0 (worst quality, smallest file) to 100 (best quality, biggest file).', 'Admin.Design.Help'
                    ) . ' ' . $this->trans(
                        'Recommended: 90.', 'Admin.Design.Help'
                    ),
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('png_quality', TextType::class, [
                'label' => $this->trans('PNG compression', 'Admin.Design.Feature'),
                'required' => true,
                'help' => $this->trans(
                    'PNG compression is lossless: unlike JPG, you do not lose image quality with a high compression ratio. However, photographs will compress very badly.',
                    'Admin.Design.Help'
                    ) . ' ' . $this->trans(
                        'Ranges from 0 (biggest file) to 9 (smallest file, slowest decompression).',
                        'Admin.Design.Help'
                    ) . ' ' . $this->trans(
                        'Recommended: 7.',
                        'Admin.Design.Help'
                    ),
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 9,
                    ]),
                ],
            ]);

        if (!$this->isMultistoreEnabled || $this->isAllShopContext) {
            $builder
                ->add('image_regeneration_method', ChoiceType::class, [
                    'label' => $this->trans('Generate images based on one side of the source image', 'Admin.Design.Feature'),
                    'choices' => [
                        $this->trans('Automatic (longest side)', 'Admin.Design.Feature') => 0,
                        $this->trans('Width', 'Admin.Global') => 1,
                        $this->trans('Height', 'Admin.Global') => 2,
                    ],
                ])
                ->add('product_picture_max_size', TextWithUnitType::class, [
                    'label' => $this->trans('Maximum file size of product customization pictures', 'Admin.Design.Feature'),
                    'help' => $this->trans('The maximum file size of pictures that customers can upload to customize a product (in bytes).', 'Admin.Design.Help'),
                    'required' => true,
                    'constraints' => [
                        new Type([
                            'type' => 'numeric',
                            'message' => $this->trans(
                                'This field is invalid, it must contain numeric values',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                    'unit' => $this->trans('bytes', 'Admin.Design.Feature'),
                ])
                ->add('product_picture_width', TextWithUnitType::class, [
                    'label' => $this->trans('Product picture width', 'Admin.Design.Feature'),
                    'help' => $this->trans('Width of product customization pictures that customers can upload (in pixels).', 'Admin.Design.Help'),
                    'required' => true,
                    'constraints' => [
                        new Type([
                            'type' => 'numeric',
                            'message' => $this->trans(
                                'This field is invalid, it must contain numeric values',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                    'unit' => $this->trans('pixels', 'Admin.Design.Feature'),
                ])
                ->add('product_picture_height', TextWithUnitType::class, [
                    'label' => $this->trans('Product picture height', 'Admin.Design.Feature'),
                    'help' => $this->trans('Height of product customization pictures that customers can upload (in pixels).', 'Admin.Design.Help'),
                    'required' => true,
                    'constraints' => [
                        new Type([
                            'type' => 'numeric',
                            'message' => $this->trans(
                                'This field is invalid, it must contain numeric values',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                    'unit' => $this->trans('pixels', 'Admin.Design.Feature'),
                ])
                ->add('hight_dpi', SwitchType::class, [
                    'label' => $this->trans('Generate high resolution images', 'Admin.Design.Feature'),
                    'help' => $this->trans(
                        'This will generate an additional file for each image (thus doubling your total amount of images). Resolution of these images will be twice higher.',
                        'Admin.Design.Help'
                        ) . ' ' . $this->trans(
                            'Enable to optimize the display of your images on high pixel density screens.',
                            'Admin.Design.Help'
                        ),
                ]);
        }
    }
}
