<?php

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class CustomerCreditField
{
    /**
     * Dodaje pole limitu kredytowego do formularza Symfony w BO
     */
    public static function addCreditLimitField(FormBuilderInterface $formBuilder, string $label): void
    {
        $formBuilder->add('credit_limit', NumberType::class, [
            'label' => $label,
            'required' => false,
            'scale' => 2,
            'constraints' => [
                new GreaterThanOrEqual([
                    'value' => 0,
                    'message' => 'Limit kredytowy nie może być wartością ujemną.',
                ]),
            ],
            'help' => 'Maksymalny limit zadłużenia przypisany do tego klienta.',
        ]);
    }
}
