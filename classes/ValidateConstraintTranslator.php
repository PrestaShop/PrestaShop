<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ValidateConstraintTranslatorCore.
 */
class ValidateConstraintTranslatorCore
{
    private $translator;

    /**
     * ValidateConstraintTranslatorCore constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $validator
     *
     * @return string
     */
    public function translate($validator)
    {
        if ($validator === 'isName') {
            return $this->translator->trans(
                'Invalid name',
                [],
                'Shop.Forms.Errors'
            );
        }

        if ($validator === 'isCustomerName') {
            return $this->translator->trans(
                'Invalid format.',
                [],
                'Shop.Forms.Errors'
            );
        }

        if ($validator === 'isBirthDate') {
            return $this->translator->trans(
                'Format should be %s.',
                [Tools::formatDateStr('31 May 1970')],
                'Shop.Forms.Errors'
            );
        }

        if ($validator === 'required') {
            return $this->translator->trans(
                'Required field',
                [],
                'Shop.Forms.Errors'
            );
        }

        return $this->translator->trans(
            'Invalid format.',
            [],
            'Shop.Forms.Errors'
        );
    }
}
