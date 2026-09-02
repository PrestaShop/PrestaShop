<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Validator\Constraints;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Adapter\Validate;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The computation here means to only count the raw text, not the rich text with html strip tags, also all the
 * line breaks are simply ignored (not event replaced with spaces). This computation is made to match the one
 * from the TinyMce text count. You can see it in TinyMCEEditor.js component, if the js component is modified
 * so should this validator.
 *
 * Note: if you rely on Product class validation you might also need to update Product::validateField
 * Note: if you are still using the legacy AdminProductsController you should also update the checkProduct() function
 */
class TinyMceMaxLengthValidator extends ConstraintValidator
{
    /**
     * @var Validate
     */
    private $validateAdapter;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(Validate $validate, TranslatorInterface $translator)
    {
        $this->validateAdapter = $validate;
        $this->translator = $translator;
    }

    /**
     * @param mixed $value
     * @param TinyMceMaxLength $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TinyMceMaxLength) {
            throw new UnexpectedTypeException($constraint, TinyMceMaxLength::class);
        }

        if (!$this->validateAdapter->isUnsignedInt($constraint->max)) {
            throw new InvalidArgumentException('Max must be int. Input was: ' . \gettype($constraint->max));
        }

        // If the provided value is not a string, nothing to validate here
        if (!is_string($value)) {
            return;
        }

        $replaceArray = [
            "\n",
            "\r",
            "\n\r",
            "\r\n",
        ];
        $str = str_replace($replaceArray, [''], strip_tags($value));

        if (iconv_strlen($str) > $constraint->max) {
            $message = $constraint->message ?? $this->translator->trans(
                'This value is too long. It should have %limit% characters or less.',
                ['%limit%' => $constraint->max],
                'Admin.Catalog.Notification'
            );

            $this->context->buildViolation($message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(TinyMceMaxLength::TOO_LONG_ERROR_CODE)
                ->addViolation()
            ;
        }
    }
}
