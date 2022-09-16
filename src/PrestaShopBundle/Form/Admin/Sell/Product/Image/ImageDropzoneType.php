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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Image;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type integrates a Dropzone used to manage images, the twig templates is a simple div
 * with data attributes, it needs javascript from the Dropzone.vue component to work.
 *
 * This form type though should be used to pass any configuration to the javascript components, like
 * translations, locales, or productId (maybe even urls could be configurable).
 */
class ImageDropzoneType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'translations' => [
                'window.selectAll' => $this->trans('Select all', 'Admin.Actions'),
                'window.settingsUpdated' => $this->trans('Settings updated', 'Admin.Global'),
                'window.imageReplaced' => $this->trans('Image replaced', 'Admin.Catalog.Notification'),
                'window.unselectAll' => $this->trans('Unselect all', 'Admin.Actions'),
                'window.replaceSelection' => $this->trans('Replace selection', 'Admin.Actions'),
                'window.cantDisableCover' => $this->trans('Using another image as cover will automatically uncheck this box.', 'Admin.Catalog.Help'),
                'window.selectedFiles' => $this->trans(
                    '[1]%filesNb%[/1] selected file(s)',
                    'Admin.Catalog.Feature',
                    ['[1]' => '<span>', '[/1]' => '</span>']
                ),
                'window.useAsCover' => $this->trans('Use as cover image', 'Admin.Catalog.Feature'),
                'window.saveImage' => $this->trans('Save image settings', 'Admin.Actions'),
                'window.delete' => $this->trans('Delete selection', 'Admin.Actions'),
                'window.close' => $this->trans('Close window', 'Admin.Actions'),
                'window.closePhotoSwipe' => $this->trans('Close (esc)', 'Admin.Actions'),
                'window.download' => $this->trans('Download', 'Admin.Actions'),
                'window.toggleFullscreen' => $this->trans('Toggle Fullscreen', 'Admin.Actions'),
                'window.zoomPhotoSwipe' => $this->trans('Zoom in/out', 'Admin.Actions'),
                'window.previousPhotoSwipe' => $this->trans('Previous (arrow left)', 'Admin.Actions'),
                'window.nextPhotoSwipe' => $this->trans('Next (arrow right)', 'Admin.Actions'),
                'window.downloadImage' => $this->trans('Download image', 'Admin.Actions'),
                'window.zoom' => $this->trans('Zoom on selection', 'Admin.Actions'),
                'modal.close' => $this->trans('Cancel', 'Admin.Actions'),
                'modal.accept' => $this->trans('Delete', 'Admin.Actions'),
                'modal.title' => $this->trans('Are you sure you want to delete the selected image?|Are you sure you want to delete the %filesNb% selected images?', 'Admin.Catalog.Notification'),
                'delete.success' => $this->trans('The selection has been successfully deleted', 'Admin.Notifications.Success'),
                'window.fileisTooLarge' => $this->trans(
                    'The file is too large. The maximum size allowed is [1] MB. The file you are trying to upload is [2] MB.',
                    'Admin.Notifications.Error',
                    ['[1]' => '{{maxFilesize}}', '[2]' => '{{filesize}}']
                ),
                'window.dropImages' => $this->trans('Drop images here', 'Admin.Catalog.Feature'),
                'window.selectFiles' => $this->trans('or select files', 'Admin.Catalog.Feature'),
                'window.recommendedSize' => $this->trans('Recommended size 800 x 800px for default theme.', 'Admin.Catalog.Feature'),
                'window.recommendedFormats' => $this->trans('JPG, GIF, PNG or WebP format.', 'Admin.Catalog.Feature'),
                'window.cover' => $this->trans('Cover', 'Admin.Catalog.Feature'),
                'window.caption' => $this->trans('Caption', 'Admin.Catalog.Feature'),
            ],
            'update_form_type' => null,
            'attr' => [
                'class' => 'product-image-dropzone',
            ],
            'required' => false,
            'label' => false,
        ]);

        $resolver
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('update_form_type', ['string', 'null'])
            ->setAllowedTypes('translations', ['array'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['locales'] = $this->locales;
        $view->vars['translations'] = $options['translations'];
        $view->vars['product_id'] = $options['product_id'];
        $view->vars['update_form_name'] = StringUtil::fqcnToBlockPrefix($options['update_form_type']) ?: '';
    }
}
