<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use Symfony\Contracts\Translation\TranslatorInterface;

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
            'back' => (new FormField())
                ->setName('back')
                ->setType('hidden'),
            'email' => (new FormField())
                ->setName('email')
                ->setType('email')
                ->setAutocompleteAttribute('username')
                ->setRequired(true)
                ->setLabel($this->translator->trans(
                    'Email',
                    [],
                    'Shop.Forms.Labels'
                ))
                ->addConstraint('isEmail'),
            'password' => (new FormField())
                ->setName('password')
                ->setType('password')
                ->setAutocompleteAttribute('current-password')
                ->setRequired(true)
                ->setLabel($this->translator->trans(
                    'Password',
                    [],
                    'Shop.Forms.Labels'
                )),
        ];
    }
}
