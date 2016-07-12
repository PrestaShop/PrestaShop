<?php

use Symfony\Component\Translation\TranslatorInterface;

class ValidateConstraintTranslatorCore
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function translate($validator)
    {
        if ($validator === 'isName') {
            return $this->translator->trans(
                'Invalid name', [], 'Shop.Forms.Errors'
            );
        } elseif ($validator === 'required') {
            return $this->translator->trans(
                'Required field', [], 'Shop.Forms.Errors'
            );
        }


        return sprintf(
            $this->translator->trans(
                'Invalid format.',
                [],
                'Shop.Forms.Errors'
            ),
            $validator
        );
    }
}
