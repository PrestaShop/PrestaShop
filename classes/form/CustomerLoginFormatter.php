<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerLoginFormatterCore implements FormFormatterInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFormat()
    {
        return [
            'back' => (new FormField)
                ->setName('back')
                ->setType('hidden'),
            'email' => (new FormField)
                ->setName('email')
                ->setType('email')
                ->setRequired(true)
                ->setLabel($this->translator->trans(
                    'Email', [], 'Shop.Forms.Labels'
                ))
                ->addConstraint('isEmail'),
            'password' => (new FormField)
                ->setName('password')
                ->setType('password')
                ->setRequired(true)
                ->setLabel($this->translator->trans(
                    'Password', [], 'Shop.Forms.Labels'
                ))
                ->addConstraint('isPasswd'),
        ];
    }
}
