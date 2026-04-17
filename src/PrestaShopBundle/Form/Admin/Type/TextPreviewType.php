<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is used to display a text value without providing an interactive input to edit it.
 * It is based on a hidden input so it could be changed programmatically, or be used just to display
 * a data in a form.
 */
class TextPreviewType extends TextType
{
    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['type'] = 'hidden';
        $view->vars['preview_class'] = $options['preview_class'];
        $view->vars['allow_html'] = $options['allow_html'];
        if (!empty($options['prefix'])) {
            $view->vars['prefix'] = $options['prefix'];
        }
        if (!empty($options['suffix'])) {
            $view->vars['suffix'] = $options['suffix'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'text_preview';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults(
                [
                    'preview_class' => '',
                    'prefix' => null,
                    'suffix' => null,
                    'allow_html' => false,
                ]
            )
            ->setAllowedTypes('preview_class', 'string')
            ->setAllowedTypes('prefix', ['string', 'null'])
            ->setAllowedTypes('suffix', ['string', 'null'])
            ->setAllowedTypes('allow_html', 'bool');
    }
}
