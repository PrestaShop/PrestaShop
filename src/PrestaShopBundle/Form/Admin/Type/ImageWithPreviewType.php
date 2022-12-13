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

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is used as a container of sub forms, each sub form will be rendered as a part of an accordion.
 */
class ImageWithPreviewType extends FileType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['download_url'] = $options['download_url'];

        /* An array of preview images, must have params id and image_path.*/
        $view->vars['preview_images'] = $options['preview_images'];

        /*
         * Indicates if image can be deleted.
         * If image can be deleted you also need to have csrf_delete_token which is checked in delete action.
         * Also if images can be deleted preview_images must contain delete_path,
         * a path to to delete action of that specific image.
         */
        $view->vars['can_be_deleted'] = $options['can_be_deleted'];
        $view->vars['csrf_delete_token'] = $options['csrf_delete_token'];

        /*
         * Indicates whether or not to show size next to image.
         * If used, then preview_images must also contain param size.
         */
        $view->vars['show_size'] = $options['show_size'];

        /* A warning message that will be shown if field is disabled.*/
        $view->vars['warning_message'] = $options['warning_message'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'download_url' => null,
            'preview_images' => null,
            'can_be_deleted' => false,
            'csrf_delete_token' => null,
            'show_size' => false,
            'warning_message' => null,
        ])
            ->setAllowedTypes('can_be_deleted', ['bool'])
            ->setAllowedTypes('download_url', ['null', 'string'])
            ->setAllowedTypes('csrf_delete_token', ['null', 'string'])
            ->setAllowedTypes('show_size', ['bool'])
            ->setAllowedTypes('warning_message', ['null', 'string'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'image_with_preview';
    }
}
