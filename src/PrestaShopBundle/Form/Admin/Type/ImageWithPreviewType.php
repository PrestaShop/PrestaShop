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
 * This form is used to show file type with preview images
 */
class ImageWithPreviewType extends FileType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['download_url'] = $options['download_url'];

        /*
         * Indicates if image can be deleted.
         * If image can be deleted you also need to have csrf_delete_token_id which is checked in delete action.
         */
        $view->vars['can_be_deleted'] = $options['can_be_deleted'];
        $view->vars['csrf_delete_token_id'] = $options['csrf_delete_token_id'];
        $view->vars['show_size'] = $options['show_size'];

        /* A warning message that will be shown if field is disabled.*/
        $view->vars['warning_message'] = $options['warning_message'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'download_url' => null,
            'can_be_deleted' => false,
            'csrf_delete_token_id' => null,
            'show_size' => false,
            'warning_message' => null,
            'data_class' => null,
        ])
            ->setAllowedTypes('can_be_deleted', ['bool'])
            ->setAllowedTypes('download_url', ['null', 'string'])
            ->setAllowedTypes('csrf_delete_token_id', ['null', 'string'])
            ->setAllowedTypes('show_size', ['bool'])
            ->setAllowedTypes('warning_message', ['null', 'string'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'image_with_preview';
    }
}
