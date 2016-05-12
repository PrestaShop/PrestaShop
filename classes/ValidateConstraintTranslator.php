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
                'invalid name', [], 'ValidateConstraint'
            );
        } elseif ($validator === 'required') {
            return $this->translator->trans(
                'required field', [], 'ValidateConstraint'
            );
        }


        return sprintf(
            $this->translator->trans(
                'does not satisfy the "%1$s" validation constraint - sorry about the cryptic explanation',
                [],
                'ValidateConstraint'
            ),
            $validator
        );
    }
}
