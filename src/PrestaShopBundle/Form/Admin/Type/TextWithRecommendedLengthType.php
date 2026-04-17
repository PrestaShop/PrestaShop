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
 * Is used to add field with recommended input length counter to the form.
 *
 * Requires Javasript text-with-recommended-lenght-counter.js to work properly.
 */
class TextWithRecommendedLengthType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['recommended_length'] = $options['recommended_length'];
        $view->vars['input_type'] = $options['input_type'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'input_type' => 'text',
                'compound' => false,
            ])
            ->setRequired([
                'recommended_length',
            ])
            ->setAllowedTypes('recommended_length', 'int')
            ->setAllowedValues('input_type', ['text', 'textarea'])
        ;
    }
}
