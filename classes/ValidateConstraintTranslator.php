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
                'Invalid name', array(), 'Shop.Forms.Errors'
            );
        } elseif ($validator === 'isBirthDate') {
            return $this->translator->trans(
                'Format should be %s.', array(Tools::formatDateStr('31 May 1970')), 'Shop.Forms.Errors'
            );
        }
        elseif ($validator === 'required') {
            return $this->translator->trans(
                'Required field', array(), 'Shop.Forms.Errors'
            );
        }

        return sprintf(
            $this->translator->trans(
                'Invalid format.',
                array(),
                'Shop.Forms.Errors'
            ),
            $validator
        );
    }
}
