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

use PrestaShop\PrestaShop\Core\Image\AvifExtensionChecker;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageSettingsType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly AvifExtensionChecker $avifExtensionChecker,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Check if AVIF is enabled on the server
        $avifEnabled = $this->avifExtensionChecker->isAvailable();
        $helpFormats = $this->trans('Choose which image formats you want to be generated. Base image will always have .jpg extension, other formats will have .webp or .avif.', 'Admin.Design.Help');

        if (!$avifEnabled) {
            $helpFormats .= '<br/><strong>' . $this->trans('AVIF is disabled because it\'s not supported on your server, check your configuration if you want to use it.', 'Admin.Design.Help') . '</strong>';
        }

        // Build the settings form
        $builder
            ->add('formats', ChoiceType::class, [
                'label' => $this->trans('Image formats to generate', 'Admin.Design.Feature'),
                'help' => $helpFormats,
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    $this->trans('Base JPEG/PNG', 'Admin.Design.Feature') => 'jpg',
                    $this->trans('WebP', 'Admin.Design.Feature') => 'webp',
                    $this->trans('AVIF', 'Admin.Design.Feature') => 'avif',
                ],
                'choice_attr' => function (string $choice, string $key) use ($avifEnabled): array {
                    return ['disabled' => $choice === 'jpg' || $choice === 'avif' && !$avifEnabled];
                },
            ])
            ->add('base-format', ChoiceType::class, [
                'label' => $this->trans('Base format', 'Admin.Design.Feature'),
                'expanded' => true,
                'choices' => [
                    $this->trans('Use JPEG', 'Admin.Design.Feature') => 'jpg',
                    $this->trans('Use PNG only if the base image is in PNG format', 'Admin.Design.Feature') => 'png',
                    $this->trans('Use PNG', 'Admin.Design.Feature') => 'png_all',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('avif-quality', IntegerType::class, [
                'label' => $this->trans('AVIF compression', 'Admin.Design.Feature'),
                'required' => $avifEnabled,
                'disabled' => !$avifEnabled,
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'constraints' => $avifEnabled ? [new NotBlank(), new Range(['min' => 0, 'max' => 100])] : [],
                'help' => $this->trans('Ranges from 0 (worst quality, smallest file) to 100 (best quality, biggest file).', 'Admin.Design.Help') . ' ' . $this->trans('Recommended: %d.', 'Admin.Design.Help', [90]),
            ])
            ->add('jpeg-quality', IntegerType::class, [
                'label' => $this->trans('JPEG compression', 'Admin.Design.Feature'),
                'required' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                    ]),
                ],
                'help' => $this->trans('Ranges from 0 (worst quality, smallest file) to 100 (best quality, biggest file).', 'Admin.Design.Help') . ' ' . $this->trans('Recommended: %d.', 'Admin.Design.Help', [90]),
            ])
            ->add('png-quality', IntegerType::class, [
                'label' => $this->trans('PNG compression', 'Admin.Design.Feature'),
                'required' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 9,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 0,
                        'max' => 9,
                    ]),
                ],
                'help' => $this->trans('PNG compression is lossless: unlike JPG, you do not lose image quality with a high compression ratio. However, photographs will compress very badly.', 'Admin.Design.Help') . ' ' . $this->trans('Ranges from 0 (biggest file) to 9 (smallest file, slowest decompression).', 'Admin.Design.Help') . ' ' . $this->trans('Recommended: %d.', 'Admin.Design.Help', [7]),
            ])
            ->add('webp-quality', IntegerType::class, [
                'label' => $this->trans('WebP compression', 'Admin.Design.Feature'),
                'required' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                    ]),
                ],
                'help' => $this->trans('Ranges from 0 (worst quality, smallest file) to 100 (best quality, biggest file).', 'Admin.Design.Help') . ' ' . $this->trans('Recommended: %d.', 'Admin.Design.Help', [80]),
            ])
            ->add('generation-method', ChoiceType::class, [
                'label' => $this->trans('Generate images based on one side of the source image', 'Admin.Design.Feature'),
                'choices' => [
                    $this->trans('Automatic (longest side)', 'Admin.Design.Feature') => 0,
                    $this->trans('Width', 'Admin.Global') => 1,
                    $this->trans('Height', 'Admin.Global') => 2,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('picture-max-size', IntegerType::class, [
                'label' => $this->trans('Maximum file size of product customization pictures', 'Admin.Design.Feature'),
                'help' => $this->trans('The maximum file size of pictures that customers can upload to customize a product (in bytes).', 'Admin.Design.Help'),
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 0,
                    ]),
                ],
            ])
            ->add('picture-max-width', IntegerType::class, [
                'label' => $this->trans('Product picture width', 'Admin.Design.Feature'),
                'help' => $this->trans('Width of product customization pictures that customers can upload (in pixels).', 'Admin.Design.Help'),
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 0,
                    ]),
                ],
            ])
            ->add('picture-max-height', IntegerType::class, [
                'label' => $this->trans('Product picture height', 'Admin.Design.Feature'),
                'help' => $this->trans('Height of product customization pictures that customers can upload (in pixels).', 'Admin.Design.Help'),
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => 0,
                    ]),
                ],
            ])
        ;
    }
}
