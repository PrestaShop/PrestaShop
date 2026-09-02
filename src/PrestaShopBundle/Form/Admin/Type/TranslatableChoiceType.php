<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslatableChoiceType extends TranslatableType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['default_locale'] = reset($options['locales'])['iso_code'];
        $view->vars['choices'] = $options['choices'];
        $view->vars['button'] = $options['button'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'button' => [],
            'choices' => [],
            'choice_translation_domain' => false,
            'allow_extra_fields' => true,
        ]);

        $resolver->setAllowedTypes('button', 'array');
        $resolver->setAllowedTypes('choices', 'array');

        parent::configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'translatable_choice';
    }
}
