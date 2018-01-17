<?php

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslateTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['locales'] as $locale) {
            $localeOptions = $options['options'];
            $localeOptions['label'] = $locale['iso_code'];

            if (!isset($localeOptions['required'])) {
                $localeOptions['required'] = false;
            }

            $builder->add($locale['id_lang'], TextType::class, $localeOptions);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $options['locales'];
        $view->vars['default_locale'] = reset($options['locales']);
        $view->vars['hide_locales'] = 1 >= count($options['locales']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'options' => [],
            'locales' => [],
        ]);

        $resolver->setAllowedTypes('locales', 'array');
        $resolver->setAllowedTypes('options', 'array');
    }

    public function getBlockPrefix()
    {
        return 'translate_text';
    }
}
