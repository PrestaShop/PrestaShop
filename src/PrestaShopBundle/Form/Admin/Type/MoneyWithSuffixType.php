<?php

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MoneyWithSuffixType is a money type,
 * which also has a suffix string right after the currency sign
 */
class MoneyWithSuffixType extends MoneyType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $pattern = self::getPattern($options['currency']);

        // Currency sign in the pattern string can be either to the left,
        // or to the right of the {{ widget }}, depending on the currency.
        $tokens = explode('{{ widget }}', $pattern);

        // The only non-empty token will be the currency sign - append the suffix to it
        array_walk($tokens, [$this, 'applySuffix'], $options['suffix']);

        // Restore the pattern as it was before,
        // just the suffix is already appended after the currency sign
        $view->vars['money_pattern'] = implode('{{ widget }}', $tokens);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('suffix', '');
    }

    /**
     * Appends the suffix to the value, if the value is not empty.
     *
     * @param string $value
     * @param int $key
     * @param string $suffix
     */
    private function applySuffix(&$value, $key, $suffix)
    {
        if (strlen($value) > 0) {
            $value = rtrim($value).' '.$suffix;
        }
    }
}
