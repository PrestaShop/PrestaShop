<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        /* A warning message that will be shown if field is disabled. */
        $view->vars['warning_message'] = $options['warning_message'];
    }

    public function configureOptions(OptionsResolver $resolver)
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
