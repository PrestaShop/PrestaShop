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
 * Defines reusable text input with max length counter
 */
class TextWithLengthCounterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['max_length'] = $options['max_length'];
        $view->vars['position'] = $options['position'];
        $view->vars['input'] = $options['input'];
        $view->vars['input_attr'] = $options['input_attr'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'max_length',
            ])
            ->setDefaults([
                'position' => 'before',
                'input' => 'text',
                'input_attr' => [],
                'compound' => false,
            ])
            ->setAllowedTypes('max_length', 'int')
            ->setAllowedTypes('input_attr', ['array'])
            ->setAllowedValues('position', ['before', 'after'])
            ->setAllowedValues('input', ['text', 'textarea'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'text_with_length_counter';
    }
}
