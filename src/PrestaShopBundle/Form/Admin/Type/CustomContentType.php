<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Type is used to add any content in any position of the form rather than actual field.
 */
final class CustomContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['data'] = $options['data'];
        $view->vars['template'] = $options['template'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'template',
            ])
            ->setDefaults([
                'required' => false,
                'data' => [],
            ])
            ->setAllowedTypes('template', 'string')
            ->setAllowedTypes('data', 'array')
        ;
    }
}
